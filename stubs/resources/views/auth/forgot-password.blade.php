@extends('saas::auth.layout')
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

            @include('saas::errors')

            <button
                type="submit"
                class="btn btn-primary w-full mb-2">
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
            class="text-sm text-primary-900 dark:text-blue-400 no-underline mx-2 pb-1 border-b border-primary-900">
            Retour à la page de connexion
        </a>
    </div>
</div>

@endsection
