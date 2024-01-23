<?php

namespace App\Enums;

enum Role: string {
    case villager = 'villager';
    case mafia = 'mafia';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
    
    public static function getRandomRole(): string
    {
        $randomNumber = rand(0, count(self::values()) - 1);

        return self::values()[$randomNumber];
    }
}
