@extends('saas::auth.layout')
@section('content')

<div class="text-center">
    
<div class="mb-4 text-center text-xl font-bold">
        Confirmez votre email
    </div>

    <div class="mb-4">
        Vous avez reçu un email contenant un lien pour confirmer votre identité
    </div>

    <div class="font-semi-bold text-sm pt-4 border-t border-gray-600">
        Vous n'avez pas reçu d'email ?
    </div>

    <div>
        <form method="post" action="/email/verification-notification">

            @csrf

            @include('saas::errors')

            <button
                type="submit"
                class="underline text-primary-500 text-sm">
                Renvoyer l'email de vérification
            </button>

            @if(session('status') == 'verification-link-sent')
                <div class="mt-4 font-medium text-sm text-center text-green-500">
                    Un nouvel email vous a été envoyé !
                </div>
            @endif

        </form>
    </div>
</div>

@endsection
