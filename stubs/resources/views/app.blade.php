<!DOCTYPE html>
<html>
<head>

    <title>{{ config('app.name') }}</title>

    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="canonical" href="{{ Request::url() }}">
    <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">

</head>

<body>

    <div id="vue-app">

        <app-layout
            :user="{{ json_encode($user) }}"
            @dialog-message="openDialogMessage">
            <template #logo>
                @include('saas::logo')
            </template>
        </app-layout>

        <dialog-modal
            ref="dialogMessage"
            :max-width-class="dialogMessageData.maxWidthClass"
            :header-class="dialogMessageData.headerClass">
            
            <template #title>
                @{{ dialogMessageData.title }}
            </template>

            <template #content>
                @{{ dialogMessageData.message }}
            </template>
            
            <template #footer>
                <button 
                    type="button"
                    class="btn btn-white"
                    @click="closeDialogMessage">
                    @{{ dialogMessageData.closeText }}
                </button>
            </template>
        </dialog-modal>
        
    </div>

    <script type="text/javascript">
        window.stripeKey = "{{ env('STRIPE_KEY') }}"
        window.mapboxToken = "{{ env('MAPBOX_TOKEN') }}"
    </script>
    
    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ mix('js/app.js') }}"></script>

</body>
