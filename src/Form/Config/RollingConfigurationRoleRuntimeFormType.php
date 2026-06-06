<?php

declare(strict_types=1);

namespace App\Rolling\Form\Config;

use App\Rolling\Value\Form\Config\RollingConfigurationRoleRuntimeData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RollingConfigurationRoleRuntimeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roleEnabled', CheckboxType::class, ['label' => 'Role subsystem enabled', 'required' => false])
            ->add('rolePolicyNamespace', TextType::class, ['label' => 'Policy namespace'])
            ->add('roleAdminNamespace', TextType::class, ['label' => 'Admin namespace'])
            ->add('roleAuditNamespace', TextType::class, ['label' => 'Audit namespace'])
            ->add('roleOpsDir', TextType::class, ['label' => 'Operations directory'])
            ->add('roleSdkNamespace', TextType::class, ['label' => 'SDK namespace'])
            ->add('save', SubmitType::class, ['label' => 'Save pending'])
            ->add('apply', SubmitType::class, ['label' => 'Apply now', 'attr' => ['class' => 'btn btn-primary']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RollingConfigurationRoleRuntimeData::class,
        ]);
    }
}
