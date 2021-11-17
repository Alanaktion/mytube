@php
/** @var \Illuminate\View\ComponentAttributeBag $attributes */
$tag = $attributes->has('href') ? 'a' : 'button';
$classList = [
    'btn',
    'btn-primary' => $primary,
    'btn-secondary' => !$primary,
    'rounded-full' => $rounded,
    'btn-lg' => $large,
    'px-5' => $rounded && !$large,
    'px-6' => $rounded && $large,
];
@endphp
<{{ $tag }}
    {{ $tag == 'button' ? 'type=' . ($attributes->get('type') ?? 'button') : null }}
    {{ $attributes->class($classList) }}>
    {{ $slot }}
</{{ $tag }}>
