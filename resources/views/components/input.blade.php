<input :type="$type ?? 'text'" {{ $attributes->merge([
    'class' => 'focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 dark:bg-trueGray-800 dark:bg-opacity-50 dark:border-trueGray-700 dark:focus:border-blue-500 shadow-sm rounded-md placeholder-gray-500 dark:placeholder-trueGray-500',
]) }}>
