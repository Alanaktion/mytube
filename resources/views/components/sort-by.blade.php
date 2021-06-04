@php
$options = [
    'published_at' => 'Published',
    'created_at' => 'Imported',
];
@endphp
<source-filter
    :options='@json($options)'
    value="{{ $value }}"
    label="{{ __('Sort') }}"
></source-filter>
