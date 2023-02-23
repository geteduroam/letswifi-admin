<?php

namespace App\Controller\Admin;

use App\Entity\RealmSigningLog;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Exception\EntityRemoveException;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

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
                ->setLabel('Pseudo account')
                ->hideOnIndex(),
            TextField::new('realm'),
            TextField::new('client'),
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
            ->add(TextFilter::new('realm'))
            ->add(DateTimeFilter::new('revoked'));
    }

    /*
     * LinkToCrudAction to revoke one realm signing log, configured at configureActions
     */
    public function revokeRealmSigningLog(AdminContext $context) : Response
    {
        try {
            $entity = $context->getEntity()->getInstance();
            $entityManager = $this->container->get('doctrine')->getManagerForClass(RealmSigningLog::class);
            $repository = $entityManager->getRepository(RealmSigningLog::class);
            $repository->revoke($entity, true);
        } catch (ForeignKeyConstraintViolationException $e) {
            throw new EntityRemoveException(['entity_name' => $context->getEntity()->getName(), 'message' => $e->getMessage()]);
        }

        $url = $context->getReferrer()
            ?? $this->container->get(AdminUrlGenerator::class)->setAction(Action::INDEX)->generateUrl();

        return $this->redirect($url);
    }

    /*
     * LinkToCrudAction to revoke multiple (batch) realm signing logs, configured at configureActions
     */
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
