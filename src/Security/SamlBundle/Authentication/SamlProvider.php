<?php

declare(strict_types=1);

namespace App\Security\SamlBundle\Authentication;

use App\Repository\ContactRepository;
use App\Security\SamlBundle\Exception\MissingSamlAttribute;
use App\Security\SamlBundle\Identity;
use BadMethodCallException;
use Psr\Log\LoggerInterface;
use SAML2\Assertion;
use Surfnet\SamlBundle\SAML2\Attribute\AttributeDictionary;
use Surfnet\SamlBundle\SAML2\Response\AssertionAdapter;
use Surfnet\SamlBundle\Security\Authentication\Provider\SamlProviderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

use function count;
use function gettype;
use function is_array;
use function is_object;
use function is_string;
use function reset;
use function sprintf;

class SamlProvider implements SamlProviderInterface, UserProviderInterface
{
    public function __construct(
        private readonly ContactRepository $contacts,
        private readonly AttributeDictionary $attributeDictionary,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function getNameId(Assertion $assertion): string
    {
        $name = $this->attributeDictionary->translate($assertion)->getNameID();

        if ($name === null) {
            return '';
        }

        return $name;
    }

    public function getUser(Assertion $assertion): UserInterface
    {
        $translatedAssertion = $this->attributeDictionary->translate($assertion);

        try {
            $userId = $this->getSingleStringValue('eduPersonPrincipalName', $translatedAssertion);
        } catch (MissingSamlAttribute $e) {
            throw new BadCredentialsException($e->getMessage());
        }

        $contact = $this->contacts->findByUserId($userId);

        if ($contact === null) {
            throw new BadCredentialsException('You have no access');
        }

        return new Identity($contact);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return $class === Identity::class;
    }

    public function loadUserByUsername(string $username): void
    {
        throw new BadMethodCallException('Use `getUser` to load a user by username');
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $contact = $this->contacts->findByNameId($identifier);

        if ($contact !== null) {
            return new Identity($contact);
        }

        throw new BadCredentialsException();
    }

    private function getSingleStringValue(string $attribute, AssertionAdapter $translatedAssertion): string
    {
        $values = $translatedAssertion->getAttributeValue($attribute);
        if (!is_array($values) || count($values) === 0) {
            $message = sprintf(
                'No value(s) found for attribute "%s"',
                $attribute,
            );

            $this->logger->warning($message);

            throw new MissingSamlAttribute(sprintf('Missing value for requested attribute "%s"', $attribute));
        }

        // see https://www.pivotaltracker.com/story/show/121296389
        if (count($values) > 1) {
            $this->logger->warning(sprintf(
                'Found "%d" values for attribute "%s", using first value',
                count($values),
                $attribute,
            ));
        }

        $value = reset($values);

        if (!is_string($value)) {
            $message = sprintf(
                'First value of attribute "%s" must be a string, "%s" given',
                $attribute,
                is_object($value) ? $value::class : gettype($value),
            );

            $this->logger->warning($message);

            throw new MissingSamlAttribute($message);
        }

        return $value;
    }
}
