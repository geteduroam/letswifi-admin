<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Controller;

use App\Command\SaveRealmCommand;
use App\CommandHandler\SaveRealmCommandHandler;
use App\Factory\SaveRealmCommandFactory;
use App\Form\Entity\RealmType;
use App\Repository\RealmRepository;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function array_key_exists;
use function is_array;
use function sprintf;

class RealmController extends AbstractController
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly SaveRealmCommandFactory $saveRealmCommandFactory,
        private readonly RealmRepository $realmRepository,
        private readonly SaveRealmCommandHandler $saveRealmCommandHandler
    ) {
    }

    #[Route('/realm', name: 'app_realm')]
    public function editAction(
        Request $request,
    ): Response {
        $entityId = $this->getRouteParameter($request, 'entityId');
        $referrer = $this->getRouteParameter($request, 'referrer');

        $entity = $this->realmRepository->find($entityId);

        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($referrer);
        }

        $realmCommand = $this->saveRealmCommandFactory->buildRealmCommandByRealm($entity);

        $form = $this->formFactory->create(RealmType::class, $realmCommand);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            try {
                if ($form->isValid()) {
                    $command = $form->getData();
                    if ($this->isSaveAction($request)) {
                        $this->saveRealm($command);
                    }

                    return $this->redirect($referrer);
                } else {
                    $this->addFlash('error', 'FormDidNotPassValidation');
                }
            } catch (InvalidArgumentException $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->render('bundles/easyAdminBundle/realm.html.twig', [
            'form' => $form->createView(),
            'title' => 'Realm',
        ]);
    }

    private function getRouteParameter(Request $request, string $parameterName): string
    {
        $routeParams = $request->query->getIterator('routeParams');

        foreach ($routeParams as $parameter) {
            if (is_array($parameter) && (array_key_exists($parameterName, $parameter))) {
                return $parameter[$parameterName];
            }
        }

        throw new InvalidArgumentException(sprintf('Unknown or invalid argument %s', $parameterName));
    }

    private function saveRealm(
        SaveRealmCommand $saveRealmCommand,
    ): void {
        $this->saveRealmCommandHandler->save($saveRealmCommand);
    }

    private function isSaveAction(Request $request): bool
    {
        return $request->request->get('save') === 'save';
    }
}
