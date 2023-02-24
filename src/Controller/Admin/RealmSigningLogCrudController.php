<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\RealmSigningLog;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * @return FieldInterface[]
     * @psalm-return iterable<FieldInterface>
     */
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
                ->formatValue(static function ($value, $entity) {
                    return $value ? $value : '-';
                }),
            DateTimeField::new('revoked')
                ->setFormat('yyyy-MM-dd HH:mm:ss')
                ->formatValue(static function ($value, $entity) {
                    return $value ? $value : '-';
                }),
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

    /**
     * LinkToCrudAction to revoke one realm signing log, configured at configureActions
     *
     * @throws Exception
     */
    public function revokeRealmSigningLog(AdminContext $context): Response
    {
        try {
            $entity        = $context->getEntity()->getInstance();
            $entityManager = $this->container->get('doctrine')->getManagerForClass(RealmSigningLog::class);
            if ($entityManager !== null) {
                $repository = $entityManager->getRepository(RealmSigningLog::class);
                $repository->revoke($entity, true);
            }

            $url = $context->getReferrer()
                ?? $this->container->get(AdminUrlGenerator::class)->setAction(Action::INDEX)->generateUrl();
        } catch (NotFoundExceptionInterface | ContainerExceptionInterface $e) {
            throw new Exception($e->getMessage());
        }

        return $this->redirect($url);
    }

    /**
     * LinkToCrudAction to revoke multiple (batch) realm signing logs, configured at configureActions
     *
     * @throws Exception
     */
    public function revokeRealmSigningLogBatch(BatchActionDto $batchActionDto): Response
    {
        try {
            $entityManager = $this->container->get('doctrine')->getManagerForClass(RealmSigningLog::class);

            if ($entityManager !== null) {
                foreach ($batchActionDto->getEntityIds() as $id) {
                    $entity     = $entityManager->find(RealmSigningLog::class, $id);
                    $repository = $entityManager->getRepository(RealmSigningLog::class);
                    if ($entity === null) {
                        continue;
                    }

                    $repository->revoke($entity, true);
                }

                $entityManager->flush();
            }
        } catch (NotFoundExceptionInterface | ContainerExceptionInterface $e) {
            throw new Exception($e->getMessage());
        }

        return $this->redirect($batchActionDto->getReferrerUrl());
    }
}
