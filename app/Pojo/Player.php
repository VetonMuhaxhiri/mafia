<?php

namespace App\Pojo;

class Player
{
    const NAMES = [
        "Alice",
        "Bob",
        "Charlie",
        "David",
        "Emma",
        "Frank",
        "Grace",
        "Henry",
        "Ivy"
    ];

    public function __construct(
       public string $name, 
       public string $role,
       public int $numberOfVotesToBeKicked = 0,
    ) {}
}