<?php

declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 * Copyright: 2023, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace App\Form\Entity;

use App\Command\SaveRealmCommand;
use App\Entity\CA;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
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
                DateTimeType::class,
                [
                    'attr' => ['placeholder' => 'SignerDateTime'],
                    'label' => 'SignerDays',
                    'required' => true,
                ],
            )
            ->add(
                'trustedCAs',
                ChoiceType::class,
                [
                    'label' => 'TrustedCAs',
                    'choices' => $saveRealmCommand->getCas(),
                    'multiple' => true,
                    'expanded' => true,
                ],
            )
            ->add(
                'key',
                TextType::class,
                ['required' => true],
            )
            ->add('refreshKey', ButtonType::class, ['attr' => ['class' => 'btn btn-primary']])
            ->add(
                'vhost',
                TextType::class,
                [
                    'label' => 'vhost',
                    'required' => true,
                ],
            )
            ->add(
                'oid',
                TextType::class,
                [
                    'label' => 'OID',
                    'required' => true,
                ],
            )
            ->add(
                'ssid',
                TextType::class,
                [
                    'label' => 'SSID',
                    'required' => true,
                ],
            )
            ->add(
                $builder->create('Helpdesk', FormType::class, ['inherit_data' => true])
                ->add(
                    'emailAddress',
                    TextType::class,
                    [
                        'label' => 'EmailAddress',
                        'required' => true,
                    ],
                )
                ->add(
                    'web',
                    TextType::class,
                    [
                        'label' => 'Website',
                        'required' => true,
                    ],
                )
                ->add(
                    'phone',
                    TextType::class,
                    [
                        'label' => 'PhoneNumber',
                        'required' => true,
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
