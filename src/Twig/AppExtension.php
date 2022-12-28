<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('icon', [$this, 'getIcons']),
        ];

    }

    public function getIcons(string $icon=null): string
    {
        return match ($icon) {
            'doc', 'docx' => 'word',
            'pdf' => 'pdf',
            'ppt', 'pptx' => 'power-point',
            'xls', 'xlsx' => 'excel',
            default => 'file'
        };
    }
}