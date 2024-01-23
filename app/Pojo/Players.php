<?php

namespace App\Pojo;

use Illuminate\Support\Collection;
use App\Enums\Role;

class Players extends Collection
{
    /**
     * @param Player[] $players
     */
    public function __construct($players = [])
    {
        $this->items = $this->getArrayableItems($players);
    }

    public function villagers(): static
    {
        return $this->filter(function ($player) {
            return $player->role == Role::villager->value;
        });
    }

    public function setPlayerRole(Player $player, string $role): static
    {
        return $this->map(function ($item) use ($player, $role) {
            if($item->name == $player->name) {
                $item->role = $role;
            }
            return $item;
        });
    }

    public function voteToKick(string $playerName): static
    {
        return $this->map(function ($item) use ($playerName){

            if($item->name == $playerName) {
                $item->numberOfVotesToBeKicked++;
            }
            
            return $item;
        });
    }

    public function restartVotes(): static
    {
        return $this->map(function ($item) {
            $item->numberOfVotesToBeKicked = 0;
            
            return $item;
        });
    }
}