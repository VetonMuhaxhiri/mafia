<?php

namespace App\Http\Controllers;

use App\Enums\State;
use App\Pojo\Game;
use Exception;
use Illuminate\Http\Request;
use App\Pojo\Player;

class PlayController extends Controller
{
    private Game $game;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Game $game)
    {
        $this->middleware('auth');
        
        $this->game = $game;
    }

    /**
     * Build the game.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function play()
    {
        if(session('role')) {
            return view('play');
        }

        $this->game->setup();

        return view('play');
    }

    /**
     * Handle the voting logic.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function voteToKick(Request $request)
    {
        $votedFor = $request->player;
        $players = session('players');

        match(session('state')) {
            State::day => $this->game->voteOnDay($players, $votedFor),

            State::night => $this->game->voteOnNight($players, $votedFor),

            default => throw new Exception('Something went wrong!')
        };

        if($this->game->getWinner()) {
            return view('end-game', [
                'message' => 'The winners are ' . $this->game->getWinner(),
                'winner' => $this->game->getWinner()
            ]);
        }

        if($this->game->getKicked()) {
            return view('end-game', [
                'message' => 'You have been kicked out'
            ]);
        }

        return redirect('play');
    }
}
