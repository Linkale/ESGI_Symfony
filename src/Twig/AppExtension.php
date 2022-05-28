<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('pathFileFactures', [$this, 'pathFileFactures']),
        ];
    }

    public function pathFileFactures(string $file): string
    {
        $pathFile = "/uploads/factures/". $file;

        return $pathFile;
    }
}