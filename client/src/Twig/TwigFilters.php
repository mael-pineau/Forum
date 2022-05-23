<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigFilters extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new twigFilter('html_decode', [$this, 'htmlDecode']),
        ];
    }

    /**
     * Retun a string decoded with the html_entity_decode() function
     *
     * @param $user
     * @return string
     */
    public function htmlDecode($str)
    {
        $str = html_entity_decode($str, ENT_QUOTES, "UTF-8");

        return $str;
    }
}