@extends('saas::auth.layout')
@section('content')

<div class="">
    <div class="mb-4 text-center text-xl font-bold">
        Créez votre compte
    </div>
    <div>
        <form method="post" action="/register">

            @csrf

            <input 
                autofocus
                id="email"
                type="text"
                name="email"
                class="input w-full mb-2"
                placeholder="Votre email..."
                value="{{ old('email') }}">

            <input 
                id="firstname"
                type="text"
                name="firstname"
                class="input w-full mb-2"
                placeholder="Votre prénom..."
                value="{{ old('firstname') }}">

            <input 
                id="lastname"
                type="text"
                name="lastname"
                class="input w-full mb-2"
                placeholder="Votre nom..."
                value="{{ old('lastname') }}">

            <input 
                id="password"
                type="password"
                name="password"
                class="input w-full mb-2"
                placeholder="Votre mot de passe...">

            <input 
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                class="input w-full mb-2"
                placeholder="Confirmez votre mot de passe...">

            <label class="flex items-center my-4 text-sm">
                <input 
                    type="checkbox" 
                    class="" 
                    name="cgvu"
                    value="1">
                <div class="ml-3">
                    J'ai lu et accepte
                    <a href="{{ route('cgvu') }}"
                        class="content-link">
                        conditions générales de ventes et d'utilisation
                    </a>
                </div>
            </label>

            @include('saas::errors')
            
            <button
                type="submit"
                class="btn btn-blue w-full mb-2">
                Créer mon compte
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
            class="text-sm text-blue-900 dark:text-blue-400 no-underline">
            Vous avez déjà un compte ?
        </a>
    </div>
</div>

@endsection
