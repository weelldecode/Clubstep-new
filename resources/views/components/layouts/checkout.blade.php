<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  >

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white">
    <x-site-loader />
 

        {{ $slot }} 

    @fluxScripts
</body>

</html>
