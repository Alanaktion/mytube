@php
$options = [
    'published_at' => __('Published date'),
    'created_at' => __('Imported date'),
];
@endphp
<sort-by
    :options='@json($options)'
    value="{{ $value }}"
    label="{{ __('Sort') }}"
></sort-by>
