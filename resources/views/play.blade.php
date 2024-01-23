@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    You are {{session('role')}} now! Vote to kick someone. Its a {{session('state')}}.
                    <div class="d-flex justify-content-between">
                    <table class="table float-left">
                        <thead>
                            <tr>
                            <th scope="col">Name</th>
                            </tr>
                            <tr></tr>
                        </thead>
                        
                        <tbody>
                        @foreach (session('players') as $player)
                            @if ($player->name != auth()->user()->name)
                                <tr>
                                    <td>{{$player->name}}</td>
                                    <td><a href="{{route('kick', ['player' => $player->name])}}" type="button" class="btn btn-danger"/>Kick</td>
                                </tr> 
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                    
                    </div>
                </div>
                
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-header">Kicked player</div>
                <div class="card-body">
                    <ul>
                        @foreach (session('kickedPlayers') as $kickedPlayer)
                            <li>{{$kickedPlayer}}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
