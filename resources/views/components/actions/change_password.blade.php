@if (is_null($resource) || Gate::allows('manage-resource', [$resource, 'edit']))
    <a href="{{ route($routeName, [$params[0]]) }}" class="text-yellow-600 hover:text-yellow-800 text-theme-sm dark:text-yellow-400 dark:hover:text-yellow-300">
        Cambiar contraseña
    </a>
@endif
