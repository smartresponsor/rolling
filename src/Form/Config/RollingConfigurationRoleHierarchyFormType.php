<?php

declare(strict_types=1);

namespace App\Rolling\Form\Config;

use App\Rolling\Value\Form\Config\RollingConfigurationRoleHierarchyData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RollingConfigurationRoleHierarchyFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roleHierarchyEnabled', CheckboxType::class, ['label' => 'Default role hierarchy enabled', 'required' => false])
            ->add('roleHierarchyReviewRequired', CheckboxType::class, ['label' => 'Require review for hierarchy changes', 'required' => false])
            ->add('roleHierarchyBootstrapViewerRole', TextType::class, ['label' => 'Bootstrap viewer role'])
            ->add('roleHierarchyBootstrapOperatorRole', TextType::class, ['label' => 'Bootstrap operator role'])
            ->add('roleHierarchyBootstrapSecurityAdminRole', TextType::class, ['label' => 'Bootstrap security admin role'])
            ->add('roleHierarchyDefaultEdges', TextareaType::class, ['label' => 'Default hierarchy edges', 'required' => false])
            ->add('save', SubmitType::class, ['label' => 'Save pending'])
            ->add('apply', SubmitType::class, ['label' => 'Apply now', 'attr' => ['class' => 'btn btn-primary']]);

        $builder->get('roleHierarchyDefaultEdges')->addModelTransformer(new CallbackTransformer(
            static fn (array $edges): string => implode("\n", $edges),
            static fn (?string $edges): array => array_values(array_filter(
                array_map('trim', preg_split('/\R/', (string) $edges) ?: []),
                static fn (string $edge): bool => '' !== $edge,
            )),
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RollingConfigurationRoleHierarchyData::class,
        ]);
    }
}
