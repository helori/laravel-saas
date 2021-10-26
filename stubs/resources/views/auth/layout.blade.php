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

    <div id="auth-app" class="min-h-screen bg-gray-100 flex flex-col justify-center antialiased">
        <div class="sm:max-w-sm sm:mx-auto sm:rounded-md py-4 sm:py-6 px-4 sm:px-6 lg:px-8 bg-white shadow border-b border-gray-100">
            @yield('content')
        </div>
    </div>

    <script src="{{ mix('js/app.js') }}"></script>

</body>

