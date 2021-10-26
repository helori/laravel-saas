@extends('saas:auth.layout')
@section('content')

<div class="text-center">
    <div class="mb-4 text-center text-xl font-bold">
        Choisissez votre mot de passe
    </div>
    <div>
        <form method="post" action="/reset-password">

            @csrf

            <input 
                id="token"
                type="hidden"
                name="token"
                value="{{ $token }}">

            <input 
                required
                autofocus
                id="email"
                type="text"
                name="email"
                class="input w-full mb-2"
                placeholder="Votre email..."
                value="{{ old('email') }}">

            <input required
                id="password"
                type="password"
                name="password"
                class="input w-full mb-2"
                placeholder="Votre nouveau mot de passe...">

            <input required
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                class="input w-full mb-2"
                placeholder="Confirmation du mot de passe...">

            @include('errors')

            <button
                type="submit"
                class="btn btn-blue w-full mb-2">
                Enregistrer le nouveau mot de passe
            </button>

        </form>
    </div>
</div>

@endsection
