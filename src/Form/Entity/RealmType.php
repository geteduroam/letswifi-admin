<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Form\Entity;

use App\Application\Command\SaveRealmCommand;
use App\Entity\CA;
use App\Entity\NetworkProfile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RealmType extends AbstractType
{
    /** @param array<Object|string> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $saveRealmCommand = $options['data'];

        $builder->add(
            $builder->create('realm', FormType::class, ['inherit_data' => true])
            ->add(
                'realm',
                TextType::class,
                [
                    'required' => true,
                    'disabled' => true,
                ],
            )
            ->add(
                'Ca',
                EntityType::class,
                [
                    'label' => 'Signer',
                    'required' => true,
                    'multiple' => false,
                    'class' => CA::class,
                    'choice_label' => 'sub',
                    'choice_name' => 'sub',
                ],
            )
            ->add(
                'signerDays',
                IntegerType::class,
                [
                    'label' => 'SignerDays',
                    'required' => true,
                ],
            )
            ->add(
                'selectedNetworkProfiles',
                ChoiceType::class,
                [
                    'label' => 'NetworkProfile',
                    'choices' => $saveRealmCommand->getNetworkProfiles(),
                    'choice_value' => 'id',
                    'choice_label' => static function (NetworkProfile|null $networkProfile) {
                        return $networkProfile ? $networkProfile->getTypeName() . ' ' . $networkProfile->getName() . ' ' . $networkProfile->getValue() : '';
                    },
                    'required' => true,
                    'multiple' => true,
                ],
            )

            ->add(
                'trustedCAs',
                ChoiceType::class,
                [
                    'label' => 'TrustedCAs',
                    'choices' => $saveRealmCommand->getCas(),
                    'multiple' => true,
                    'expanded' => false,
                ],
            )
            ->add(
                $builder->create('RefreshKey', FormType::class, [
                    'inherit_data' => true,
                    'required' => false,
                ])
                ->add(
                    'refreshKey',
                    CheckboxType::class,
                    [
                        'label' => 'refresh',
                        'required' => false,
                    ],
                ),
            ),
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => SaveRealmCommand::class,
                'compound' => true,
            ],
        );
    }

    public function getBlockPrefix(): string
    {
        return 'realm_type';
    }
}
