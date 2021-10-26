@extends('saas:auth.layout')
@section('content')

<div class="text-center">
    <div class="mb-4 text-center text-xl font-bold">
        Mot de passe
    </div>
    <div>
        <form method="post" action="/user/confirm-password">

            @csrf

            <input 
                required
                autofocus
                id="password"
                type="password"
                name="password"
                class="input w-full mb-2"
                placeholder="Votre mot de passe...">

            <button
                type="submit"
                class="btn btn-blue w-full mb-2">
                Confirmer
            </button>
            
        </form>
    </div>
</div>

@endsection
