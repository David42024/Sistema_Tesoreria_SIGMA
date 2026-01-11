<div>
    @if ($error)
      <p class="py-2 text-red-700 dark:text-red-200">{{ $error }}</p>
    @endif

    <label for="" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
        {{ $label }}
        @if(isset($required) && $required === false)
            <span class="text-gray-400 text-xs font-normal">(Opcional)</span>
        @endif
    </label>
    <input @if(isset($placeholder)) placeholder="{{ $placeholder }}" @endif @if(isset($value)) value="{{ $value }}" @endif @if(isset($readonly) && $readonly === true) readonly @endif type="text" name="{{isset($name) ? Str::snake($name) : Str::snake($label)}}"
        class="text-{{$label}} dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @if($error) border-red-700 dark:border-red-200 @endif @if(isset($readonly) && $readonly === true) bg-gray-100 dark:bg-gray-800/50 cursor-not-allowed @endif" />
</div>