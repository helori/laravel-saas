@extends('saas::auth.layout')
@section('content')

<div class="text-center">

    <div v-show="!showRecoveryCodeForm">
        <div class="mb-4 text-center text-xl font-bold">
            Code de sécurité
        </div>
        <div class="mb-3 text-sm">
            Vous devez saisir un code de sécurité qui vous est donné 
            par l'application que vous avez utilisée pour scanner le QR Code 
            quand vous avez activé la double authentification.
        </div>
        <div>
            <form method="post" action="/two-factor-challenge">

                @csrf

                <input 
                    required
                    autofocus
                    id="code"
                    type="text"
                    name="code"
                    class="input w-full mb-2"
                    placeholder="Code de sécurité...">

                <button
                    type="submit"
                    class="btn btn-blue w-full mb-2">
                    Envoyer
                </button>

            </form>
        </div>

        <div class="text-center mt-4">
            <a @click="showRecoveryCodeForm = !showRecoveryCodeForm"
                class="text-sm text-blue-900 no-underline cursor-pointer">
                Utiliser un code de secours
            </a>
        </div>
    </div>

    <div v-show="showRecoveryCodeForm">
        <div class="mb-4 text-center text-xl font-bold">
            Code de secours
        </div>
        <div class="mb-3 text-sm">
            Si vous n'êtes pas en mesure de saisir un code de sécurité,
            vous pouvez utiliser un des codes de secours qui vous a été affiché
            au moment de l'activation de la double authentification.
        </div>
        <div>
            <form method="post" action="/two-factor-challenge">

                @csrf

                <input 
                    required
                    id="recovery_code"
                    type="text"
                    name="recovery_code"
                    class="input w-full mb-2"
                    placeholder="Code de secours...">

                <button
                    type="submit"
                    class="btn btn-blue w-full mb-2">
                    Envoyer
                </button>

            </form>

            <div class="text-center mt-4">
                <a @click="showRecoveryCodeForm = !showRecoveryCodeForm"
                    class="text-sm text-blue-900 no-underline cursor-pointer">
                    Utiliser un code de sécurité
                </a>
            </div>
        </div>
    </div>

</div>

@endsection
