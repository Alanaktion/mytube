<input :type="$type ?? 'checkbox'" {{ $attributes->merge([
    'class' => 'focus:ring-blue-500 dark:ring-offset-trueGray-900 h-4 w-4 text-blue-600 border-gray-300 rounded',
]) }}>
