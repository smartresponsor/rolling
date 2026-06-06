<?php

declare(strict_types=1);

namespace App\Rolling\Value\View;

/**
 * Template-neutral surface payload exported by Rolling.
 *
 * The word is intentionally singular and stable: roll.
 * Interfacing may render templates/roll/{view}.html.twig when present,
 * or may render the same slots as safe data when the template is absent.
 */
final readonly class RollSurfaceOutput
{
    /**
     * @param array<string, mixed> $slots
     */
    public function __construct(
        private string $view,
        private array $slots,
    ) {
    }

    public function word(): string
    {
        return 'roll';
    }

    public function view(): string
    {
        return $this->view;
    }

    public function templateFolder(): string
    {
        return 'templates/roll';
    }

    public function templateName(): string
    {
        return $this->view.'.html.twig';
    }

    public function templateKey(): string
    {
        return 'roll/'.$this->view;
    }

    /** @return array<string, mixed> */
    public function slots(): array
    {
        return $this->slots;
    }

    /** @return array<string, mixed> */
    public function toSafeArray(): array
    {
        return [
            'word' => $this->word(),
            'view' => $this->view(),
            'templateFolder' => $this->templateFolder(),
            'templateName' => $this->templateName(),
            'templateKey' => $this->templateKey(),
            'slots' => $this->slots,
        ];
    }
}
