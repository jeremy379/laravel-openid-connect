@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">Authorization Request</div>

            <div class="card-body">
                <p>
                    <strong>{{ $client->name }}</strong> is requesting permission to access your account.
                </p>

                @if (count($scopes) > 0)
                    <p><strong>This application will be able to:</strong></p>
                    <ul>
                        @foreach ($scopes as $scope)
                            <li>{{ $scope->description }}</li>
                        @endforeach
                    </ul>
                @endif

                <form method="POST" action="{{ route('passport.authorizations.approve') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="state" value="{{ $request->state }}">
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                    <button type="submit" class="btn btn-success">Authorize</button>
                </form>

                <form method="POST" action="{{ route('passport.authorizations.deny') }}" class="d-inline ms-2">
                    @csrf
                    <input type="hidden" name="state" value="{{ $request->state }}">
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                    <button type="submit" class="btn btn-danger">Cancel</button>
                </form>
            </div>
        </div>
    </div>
@endsection
