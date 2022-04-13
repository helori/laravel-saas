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

    <div id="auth-app" 
        class="min-h-screen bg-gray-100 dark:bg-gray-800 py-10 flex flex-col justify-center antialiased">
        <div class="mx-auto">
            @include('saas::logo', ['align' => 'center'])
        </div>
        <div class="sm:max-w-sm sm:mx-auto mt-10 sm:rounded-md py-4 sm:py-6 px-4 sm:px-6 lg:px-8 bg-white dark:bg-gray-900 dark:text-white shadow border-b border-gray-100 dark:border-gray-700">
            @yield('content')
        </div>
    </div>

    <script src="{{ mix('js/app.js') }}"></script>

</body>

