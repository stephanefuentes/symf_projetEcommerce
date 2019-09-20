<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{

    // 3 façons d'écrire un Callable (une fonction qu'on peut appeler) :
    // - fonction annonyme
    // - tableau avec instance d'objet et nom de méthode (c'est ce qu'on fais ici, en dessous avec [$this, "formatPrice"])
    // - nom d'une fonction

    public function getFilters()
    {
        return [
            new TwigFilter("price", [$this, "formatPrice"])
        ];
    }



             // {{ 25.5 | price }}
    public function formatPrice($value, $symbol = "€")
    {
        $final = number_format($value, 2, ",", " ");
        return $final." $symbol";
    }


}