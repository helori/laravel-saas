@extends('saas:auth.layout')
@section('content')

<div class="">
    <div class="mb-4 text-center text-xl font-bold">
        Connexion
    </div>
    <div>
        <form method="post" action="/login">

            @csrf

            <input 
                required
                autofocus
                id="email"
                type="text"
                name="email"
                class="input w-full mb-2"
                placeholder="Votre email..."
                value="{{ old('email') }}">

            <input 
                required
                id="password"
                type="password"
                name="password"
                class="input w-full mb-2"
                placeholder="Votre mot de passe...">

            @include('laravel-saas:errors')

            <button
                type="submit"
                class="btn btn-blue w-full mb-2">
                Connexion
            </button>

            @if (session('status'))
                <div class="alert alert-green mb-2">
                    {{ session('status') }}
                </div>
            @endif

        </form>
    </div>
    <div class="text-center mt-4">
        <a href="{{ url('/forgot-password') }}"
            class="text-sm text-blue-900 no-underline">
            Mot de passe perdu ?
        </a>
    </div>
</div>

@endsection
