@extends('saas:auth.layout')
@section('content')

<div class="text-center">
    <div class="mb-4 text-center text-xl font-bold">
        Mot de passe perdu
    </div>
    <div>
        <form method="post" action="/forgot-password">

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

            @include('errors')

            <button
                type="submit"
                class="btn btn-blue w-full mb-2">
                Envoyer le lien de ré-initialisation
            </button>

            @if (session('status'))
                <div class="alert alert-green mb-2">
                    {{ session('status') }}
                </div>
            @endif

        </form>
    </div>
    <div class="text-center mt-4">
        <a href="{{ url('/login') }}"
            class="text-sm text-blue-900 no-underline">
            Retour à la page de connexion
        </a>
    </div>
</div>

@endsection
