@props([
    'sticky' => null,
    'container' => null,
])

@php
$classes = Flux::classes('[grid-area:header]')
    ->add('z-10 min-h-12')
    ->add($container ? '' : 'flex items-center ')
    ;

if ($sticky) {
    $attributes = $attributes->merge([
        'x-data' => '',
        'x-bind:style' => '{ position: \'sticky\', top: $el.offsetTop + \'px\', \'max-height\': \'calc(100vh - \' + $el.offsetTop + \'px)\' }',
    ]);
}
@endphp

<header {{ $attributes->class($classes) }} data-flux-header>
    @if ($container)
        <div class="mx-auto w-full h-full [:where(&)]:max-w-7xl  flex items-center">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</header>
