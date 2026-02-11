<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-100 antialiased dark:bg-linear-to-b dark:from-zinc-950 dark:to-zinc-800">
        <x-site-loader />
 
                
                     {{ $slot }}  
        @livewireScripts
@fluxScripts
    </body>
</html>
