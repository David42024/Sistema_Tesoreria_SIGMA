<li>
    <a href="{{ route($link) }}"
        class="group relative flex items-center gap-2.5 rounded-lg px-4 py-2 font-medium text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800
        {{ request()->routeIs($link) ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
        @include('components.svg.' . $icon)
        {{ $name }}
    </a>
</li>