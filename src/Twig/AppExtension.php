<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('file_exists', 'file_exists'),
        );
    }

    public function getName(): string
    {
        return 'app_file';
    }
}
