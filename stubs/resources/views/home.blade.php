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

    <div class="min-h-screen bg-gray-100 flex flex-col justify-center antialiased">
        <div class="sm:max-w-sm sm:mx-auto py-4 sm:py-6 px-4 sm:px-6 lg:px-8">
            
            <div class="text-center">
                Build the front page of your app !
                <div>
                    <a href="/login"
                        class="underline text-blue-500 mx-2">
                        Login
                    </a>
                    <a href="/register"
                        class="underline text-blue-500 mx-2">
                        Register
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script src="{{ mix('js/app.js') }}"></script>

</body>
