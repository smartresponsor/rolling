<?php

declare(strict_types=1);

namespace App\Rolling\Form\Config;

use App\Rolling\Value\Form\Config\RollingConfigurationRoleRuntimeData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RollingConfigurationRoleRuntimeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('enabled', CheckboxType::class, [
                'label' => 'ROLE_ENABLED',
                'required' => false,
            ])
            ->add('policyNamespace', TextType::class, [
                'label' => 'ROLE_POLICY_NAMESPACE',
                'required' => true,
            ])
            ->add('adminNamespace', TextType::class, [
                'label' => 'ROLE_ADMIN_NAMESPACE',
                'required' => true,
            ])
            ->add('auditNamespace', TextType::class, [
                'label' => 'ROLE_AUDIT_NAMESPACE',
                'required' => true,
            ])
            ->add('opsDir', TextType::class, [
                'label' => 'ROLE_OPS_DIR',
                'required' => true,
            ])
            ->add('sdkNamespace', TextType::class, [
                'label' => 'ROLE_SDK_NAMESPACE',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RollingConfigurationRoleRuntimeData::class,
            'csrf_protection' => true,
            'label' => false,
        ]);
    }
}
