<?php

namespace App\Helpers;

use Carbon\Carbon;

class GreetingHelper
{


    public static function getTimeIcon(): string
    {
        $hour = Carbon::now()->hour;

        return match (true) {
            $hour >= 5 && $hour < 12  => 'bi-sunrise',
            $hour >= 12 && $hour < 17 => 'bi-sun',
            $hour >= 17 && $hour < 20 => 'bi-sunset',
            default                   => 'bi-moon-stars',
        };
    }

    public static function getTimeBasedGreeting(): string
    {
        $hour = Carbon::now()->hour;
        $isFriday = Carbon::now()->isFriday();
        $salam = "Assalamu Alaikum wa Rahmatullahi wa Barakatuh";

        if ($isFriday) {
            return "{$salam} — Jumma Mubarak ho";
        }

        return match (true) {
            $hour >= 5 && $hour < 12  => "{$salam} — Subah ka safar mubarak ho",
            $hour >= 12 && $hour < 17 => "{$salam} — Din ache se guzre",
            $hour >= 17 && $hour < 20 => "{$salam} — Shaam ka waqt barkat wala ho",
            default                   => "{$salam} — Raat ka ilm barkat wala ho",
        };
    }
}
