@php
$options = [
    'published_at' => 'Published',
    'created_at' => 'Imported',
];
@endphp
<sort-by
    :options='@json($options)'
    value="{{ $value }}"
    label="{{ __('Sort') }}"
></sort-by>
