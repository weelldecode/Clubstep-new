<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<meta name="csrf_token" content="{{ csrf_token() }}" />
<link rel="icon" href="/assets/img/icone.webp" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
@wireUiScripts
