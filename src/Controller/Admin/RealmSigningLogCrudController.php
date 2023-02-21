<?php

namespace App\Controller\Admin;

use App\Entity\RealmSigningLog;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Exception\EntityRemoveException;
use EasyCorp\Bundle\EasyAdminBundle\Exception\InsufficientEntityPermissionException;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;

class RealmSigningLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RealmSigningLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Users');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('serial'),
            TextField::new('requester'),
            TextField::new('subjectWithoutCustomerName')
                ->setLabel('Pseudo account'),
            TextField::new('realm'),
            DateTimeField::new('issued')
                ->setFormat('yyyy-MM-dd HH:mm:ss')
                ->formatValue(function ($value, $entity) {
                    return $value ? $value : '-';
                }),
            DateTimeField::new('revoked')
                ->setFormat('yyyy-MM-dd HH:mm:ss')
                ->formatValue(function ($value, $entity) {
                    return $value ? $value : '-';
                })
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $revoke = Action::new('revoke', 'Revoke')
                ->linkToCrudAction('revokeRealmSigningLog')
                ->addCssClass('confirm-action')
                ->setHtmlAttributes([
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#modal-confirm',
                ]);


        return parent::configureActions($actions)
            ->disable(Action::EDIT)
            ->disable(Action::NEW)
            ->disable(Action::DELETE)
            ->add(Crud::PAGE_INDEX, $revoke)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->addBatchAction(Action::new('revoke batch', 'Revoke batch')
            ->linkToCrudAction('revokeRealmSigningLogBatch')
            ->addCssClass('btn btn-primary')
            ->setIcon('fa fa-user-check'));

    }

    public function configureAssets(Assets $assets): Assets
    {
        $assets->addJsFile('assets/js/confirm-modal.js');

        return parent::configureAssets($assets);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return parent::configureFilters($filters)
            ->add('revoked');
    }

//    public function createIndexQueryBuilder(
//        SearchDto $searchDto,
//        EntityDto $entityDto,
//        FieldCollection $fields,
//        FilterCollection $filters): QueryBuilder
//    {
//        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
//
//        // if user defined sort is not set
//        if (0 === count($searchDto->getSort())) {
//            $queryBuilder
//                ->addSelect('REPLACE(sub, \'CN=\', \'\') AS HIDDEN subject_without_customer_name');
//        }
//
//        return $queryBuilder;
//    }

    public function revokeRealmSigningLog(AdminContext $context) : Response
    {
        $event = new BeforeCrudActionEvent($context);
        $this->container->get('event_dispatcher')->dispatch($event);
        if ($event->isPropagationStopped()) {
            return $event->getResponse();
        }

//        if (!$this->isGranted(Permission::EA_EXECUTE_ACTION, ['action' => Action::EDIT, 'entity' => $context->getEntity()])) {
//            throw new ForbiddenActionException($context);
//        }

        if (!$context->getEntity()->isAccessible()) {
            throw new InsufficientEntityPermissionException($context);
        }

        $csrfToken = $context->getRequest()->request->get('token');
//        if ($this->container->has('security.csrf.token_manager') && !$this->isCsrfTokenValid('ea-delete', $csrfToken)) {
//            return $this->redirectToRoute($context->getDashboardRouteName());
//        }

        $entityInstance = $context->getEntity()->getInstance();

        $event = new BeforeEntityDeletedEvent($entityInstance);
        $this->container->get('event_dispatcher')->dispatch($event);
        if ($event->isPropagationStopped()) {
            return $event->getResponse();
        }
        $entityInstance = $event->getEntityInstance();

        try {
            $entity = $context->getEntity()->getInstance();
            $entityManager = $this->container->get('doctrine')->getManagerForClass(RealmSigningLog::class);
            $repository = $entityManager->getRepository(RealmSigningLog::class);
            $repository->revoke($entity, true);
        } catch (ForeignKeyConstraintViolationException $e) {
            throw new EntityRemoveException(['entity_name' => $context->getEntity()->getName(), 'message' => $e->getMessage()]);
        }

        $responseParameters = $this->configureResponseParameters(KeyValueStore::new([
            'entity' => $context->getEntity(),
        ]));

        $event = new AfterCrudActionEvent($context, $responseParameters);
        $this->container->get('event_dispatcher')->dispatch($event);
        if ($event->isPropagationStopped()) {
            return $event->getResponse();
        }

        $url = $context->getReferrer()
            ?? $this->container->get(AdminUrlGenerator::class)->setAction(Action::INDEX)->generateUrl();

        return $this->redirect($url);
    }

    public function revokeRealmSigningLogBatch(BatchActionDto $batchActionDto)
    {
        $className = $batchActionDto->getEntityFqcn();
        $entityManager = $this->container->get('doctrine')->getManagerForClass($className);
        foreach ($batchActionDto->getEntityIds() as $id) {
            $entity = $entityManager->find($className, $id);
            $repository = $entityManager->getRepository(RealmSigningLog::class);
            $repository->revoke($entity, true);
        }

        $entityManager->flush();

        return $this->redirect($batchActionDto->getReferrerUrl());
    }

}
