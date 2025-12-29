@if (is_null($resource) || Gate::allows('manage-resource', [$resource, 'edit']))
    <form action="{{ route($routeName) }}" method="POST" style="display: inline;">
        @csrf
        <input type="hidden" name="id_personal" value="{{ $params[0] }}">
        <button type="submit" class="remove-button text-red-600 text-theme-sm dark:text-red-400" 
                onclick="return confirm('¿Estás seguro de quitar este docente del departamento?')">
            Quitar
        </button>
    </form>
@endif