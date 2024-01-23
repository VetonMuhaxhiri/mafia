@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">End game!</div>

                <div class="card-body">
                    <div class="alert" role="alert">
                        <h3>{{$message}}</h3>
                    </div>
                   Click <a type="button" href="{{route('play')}}" class="btn btn-primary">Start</a> to start the new game. </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
