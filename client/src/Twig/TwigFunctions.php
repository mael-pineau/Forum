<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigFunctions extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('getElapsedTime', [$this, 'secondsToString']),
            new TwigFunction('getProfilPic', [$this, 'getUserImage']),
            new TwigFunction('html_decode', [$this, 'htmlDecode']),
        ];
    }

    /**
     * Transform a unix timestamp into string
     *
     * @param $user
     * @return string
     */
    public function secondsToString($time)
    {
        $str = "";
        $minutes = floor(($time%3600)/60);
        $hours = floor(($time%86400)/3600);
        $days = floor(($time%2592000)/86400);
        $month = floor($time/2592000);
        $year = floor($time/31536000);

        if ($year != 0) {
            if ($year > 1) {
                $str = "$year ans";
            }
            else {
                $str = "$year an";
            }
        }
        elseif ($month != 0) {
            if ($month > 1) {
                $str = "$month mois";
            }
            else {
                $str = "$month moi";
            }
        }
        elseif ($days != 0) {
            if ($days > 1) {
                $str = "$days jours";
            }
            else {
                $str = "$days jour";
            }
        }
        elseif ($hours != 0) {
            $str = "$hours h";
        }
        elseif ($minutes != 0) {
            $minutes = $minutes + 1;
            $str = "$minutes m";
        }
        elseif ($minutes == 0) {
            $str = "1 m ";
        }

        return $str;
    }

    public function getUserImage($imageName)
    {
        $pathPhoto = 'images/profil-pictures/'. $imageName;

        if (empty($imageName) || !file_exists($pathPhoto))
        {
            $pathPhoto = 'images/profil-pictures/'. "default.png";
        }

        return $pathPhoto;
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