<?php

namespace App\Twig;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('icon', [$this, 'getIcons']),
            new TwigFunction('bg_contract', [$this, 'bgContract']),
            new TwigFunction('archive', [$this, 'getArchive'])
        ];

    }

    public function getIcons(string $icon): string
    {
        return match ($icon) {
            'doc', 'docx' => 'word',
            'pdf' => 'pdf',
            'ppt', 'pptx' => 'power-point',
            'xls', 'xlsx' => 'excel',
            default => 'file'
        };
    }

    public function bgContract(string $contract=null): string
    {
        return match ($contract){
            'CDI', 'PERMANENT CONTRACT' => 'cdi',
            'CDD', 'FIXED-TERM CONTRACT' => 'cdd',
            'BENEVOLAT', 'VOLUNTEERING', 'CONSULTANT' => 'benevolat',
            'STAGE', 'INTERNSHIP', 'STAGE + EMBAUCHE', 'INTERNSHIP + EMPLOYMENT' => 'stage',
            default => 'benevolat'
        };
    }

    public function getArchive($date=null)
    {
        $today = date('Y-m-d');
        $traduction = $this->translator->trans('Archived');

        if ($today > $date) return '<span class="badge bg-danger">'.$traduction.'</span>';
        else return false;
    }
}