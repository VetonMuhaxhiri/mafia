<?php

namespace App\Pojo;

use App\Enums\Role;
use App\Enums\State;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * A singleton class to handle the game logic.
 * 
 * Injected on AppServiceProvider::class
 * 
 */
class Game
{
    public function __construct(private string $winner = '', private bool $kicked = false)
    {
    }

    /**
     * Setup the game by adding players and assigning roles.
     *
     * @return void
     */
    public function setup()
    {
        $newRole = Role::getRandomRole();  
        $players = [];

        foreach(Player::NAMES as $name) {
            $players[] = new Player($name, Role::villager->value);
        }

        $players[] = new Player(Auth::user()->name, $newRole);

        $players = new Players($players);

        // If I am mafia we only add one more mafia player otherwise we add two mafias.
        $n = $newRole == Role::mafia->value ? 1 : 2;
        for($i = 0; $i < $n; $i++) {
            $villagers = $players->villagers();

            $playerToMafia = $villagers[array_rand($villagers->toArray())];

            $players = $players->setPlayerRole($playerToMafia, Role::mafia->value);
        }

        session()->put('role', $newRole);
        session()->put('state', State::day);
        session()->put('players', $players);
        session()->put('kickedPlayers', []);
    }

    /**
     * Kick players during the day phase.
     *
     * @return void
     */
    public function voteOnDay(Players $players, string $votedFor): void
    {
        $players = $players->voteToKick($votedFor);
        
        // Players vote randomly to kick other players
        foreach($players as $player) {
            // Logged in player can't vote twice.
            if($player->name == Auth::user()->name) {
                continue;
            }

            $oponents = $players->filter(function ($oponent) use ($player){
                return $oponent->name != $player->name;
            });

            $votedToBeKicked = $oponents->random();

            $players = $players->voteToKick($votedToBeKicked->name); 
        }

        $kickedPlayer = $players->sortByDesc('numberOfVotesToBeKicked')->first();

        $players = $players->reject($kickedPlayer);
        $players = $players->restartVotes($players);

        $kickedPlayers = session('kickedPlayers');
        $kickedPlayers[] = $kickedPlayer->name;
        session()->put('kickedPlayers', $kickedPlayers);

        $this->checkWinner($players);

        if($this->winner || $this->kicked) {
            return;
        }

        session()->put('state', State::night);

        if(session()->get('role') == Role::villager->value) {
            $this->voteOnNight($players);
        } else {
            session()->put('players', $players);
        }
    }

    /**
     * Kick players during the night phase.
     *
     * @return void
     */
    public function voteOnNight(Players $players, string $votedFor = null): void
    {
        if(session()->get('role') == Role::mafia->value) {
            
            $players = $players->voteToKick($votedFor);

            $kickedPlayer = $players->sortByDesc('numberOfVotesToBeKicked')->first();

            $players = $players->reject($kickedPlayer);
            $players = $players->restartVotes($players);
            
            $kickedPlayers = session('kickedPlayers');
            $kickedPlayers[] = $kickedPlayer->name;
            session()->put('kickedPlayers', $kickedPlayers);
        
            $this->checkWinner($players);

            if($this->winner || $this->kicked) {
                return;
            }
        }

        if(session('role') == Role::villager->value) {
            // Filter only villagers
            $villagers = $players->villagers();
            
            foreach($players as $player) {
                $oponents = $villagers->filter(function ($oponent) use ($player){
                    return $oponent->name != $player->name;
                });

                $votedToBeKicked = $oponents->random();
                
                $players = $players->voteToKick($votedToBeKicked->name);
            }

            $kickedPlayer = $players->sortByDesc('numberOfVotesToBeKicked')->first();
            $players = $players->reject($kickedPlayer);
            $players = $players->restartVotes($players);
            
            $kickedPlayers = session('kickedPlayers');
            $kickedPlayers[] = $kickedPlayer->name;
            session()->put('kickedPlayers', $kickedPlayers);

            $this->checkWinner($players);

            if($this->winner || $this->kicked) {
                return;
            }
        }

        session()->put('state', State::day);
        session()->put('players', $players);
    }

    /**
     * Check if we have a winner so far.
     *
     * @return void
     */
    public function checkWinner(Collection $players): void
    {
        $me = $players->where('name', '==', Auth::user()->name)->first();
        if(!$me) {
            $this->kicked = true;

            $this->cleanSessionData();

            return;
        }

        $mafiasCount = $players->where('role', '==', Role::mafia->value)->count();
        $villagersCount = $players->where('role', '==', Role::villager->value)->count();

        if($mafiasCount == 0) {
            $this->winner = Role::villager->value;

            $this->cleanSessionData();

            return;
        }

        if($villagersCount < $mafiasCount) {
            $this->winner = Role::mafia->value;

            $this->cleanSessionData();

            return;
        }
    }

    public function cleanSessionData(): void
    {
        session()->pull('players');
        session()->pull('state');
        session()->pull('role');
        session()->pull('kickedPlayers');
    }
    public function getWinner(): string
    {
        return $this->winner;
    }

    public function getKicked(): bool
    {
        return $this->kicked;
    }
}