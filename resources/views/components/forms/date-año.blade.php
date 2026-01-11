<div>
    @if ($error)
        <p class="py-2 text-red-700 dark:text-red-200">{{ $error }}</p>
    @endif

    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
    {{ $label }}
    @if(isset($required) && $required === false)
        <span class="text-gray-400 text-xs font-normal">(Opcional)</span>
    @endif
    </label>

    <div class="relative w-full">
        <input 
            type="number" 
            id="{{ Str::snake($label) }}" 
            name="{{ isset($name) ? Str::snake($name) : Str::snake($label) }}"
            min="1900" 
            max="{{ now()->year+1}}"
            step="1" 
            @if(isset($value)) value="{{ $value }}" @else value="{{ now()->year }}" @endif
            placeholder="{{ $placeholder ?? 'Ingrese el año' }}"
            class="custom-year-input dark:bg-dark-900 shadow-theme-xs focus:border-brand-300        focus:ring-brand-500/10 
                dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 
                bg-transparent px-4 pr-10 py-2.5 text-sm text-gray-800 
                placeholder:text-gray-400 focus:ring-3 focus:outline-hidden 
                dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 
                dark:placeholder:text-white/30 @if($error) border-red-700 dark:border-red-200 @endif"
        />

    <!-- Flechas personalizadas -->
        <div class="absolute right-2 top-1/2 -translate-y-1/2 flex flex-col justify-center space-y-0.5">
            <button 
                type="button" 
                onclick="document.getElementById('{{ Str::snake($label) }}').stepUp()" 
                class="text-gray-600 dark:text-gray-300 hover:text-brand-500 focus:outline-none text-xs leading-none">
                ▲
            </button>
            <button 
                type="button" 
                onclick="document.getElementById('{{ Str::snake($label) }}').stepDown()" 
                class="text-gray-600 dark:text-gray-300 hover:text-brand-500 focus:outline-none text-xs leading-none">
                ▼
            </button>
        </div>
    </div>

  <!-- CSS inline para ocultar flechas nativas -->
    <style>
        
        .custom-year-input::-webkit-inner-spin-button,
        .custom-year-input::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
        }

        
        .custom-year-input {
        -moz-appearance: textfield;
        }
    </style>
</div>
