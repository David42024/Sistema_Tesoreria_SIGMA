@php
    $componentId = 'combo_' . Str::snake($name);
    $searchId = $componentId . '_search';
    $dropdownId = $componentId . '_dropdown';
    $hiddenInputId = $componentId . '_hidden';
@endphp

<div class="relative combo-container" 
     data-component-id="{{ $componentId }}" 
     data-field-name="{{ $name }}"
     data-depends-on="{{ $depends_on ?? '' }}"
     data-placeholder="{{ $placeholder ?? 'Seleccionar ' . ($label ?? 'opción') . '...' }}">

    <!-- Label -->
    @if(isset($label))
        <label for="{{ $componentId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ $label }}
            @if(isset($required) && $required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <!-- Select Button -->
    <button type="button" 
            id="{{ $componentId }}"
            class="combo-button w-full h-[46px] px-4 text-left bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500/20"
            {{ !($enabled ?? true) ? 'disabled' : '' }}>
        <div class="flex justify-between items-center h-full">
            <span class="combo-text text-gray-500 dark:text-gray-400 text-sm">
                {{ $placeholder ?? 'Seleccionar ' . ($label ?? 'opción') . '...' }}
            </span>
            <svg class="combo-arrow w-5 h-5 text-gray-400 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </button>

    <!-- Hidden Input -->
    <input type="hidden" 
           id="{{ $hiddenInputId }}"
           name="{{ $name }}" 
           value="{{ $value ?? '' }}"
           @if(isset($required) && $required) required @endif>

    <!-- Dropdown -->
    <div id="{{ $dropdownId }}" 
         class="combo-dropdown absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg z-50" 
         style="display: none;">
        
        <!-- Search Field (solo si no está deshabilitado) -->
        @if(!isset($disableSearch) || $disableSearch === false)
        <div class="p-3 border-b border-gray-200 dark:border-gray-600">
            <input id="{{ $searchId }}"
                   type="text"
                   class="combo-search w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-500 rounded-md bg-white dark:bg-gray-700 text-gray-800 dark:text-white placeholder:text-gray-400 dark:placeholder:text-white/40 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500/20"
                   placeholder="Buscar..."
                   autocomplete="off">
        </div>
        @endif

        <!-- Options Container -->
        <div class="combo-options max-h-60 overflow-y-auto">
            @if(isset($options) && count($options) > 0)
                @foreach($options as $option)
                    @php
                        // Handle both objects and arrays
                        if (is_object($option)) {
                            // Handle composite keys for value
                            if (is_array($value_field)) {
                                $valueArray = [];
                                foreach($value_field as $field) {
                                    $valueArray[] = $option->{$field} ?? '';
                                }
                                $optionValue = implode('|', $valueArray);
                            } else {
                                $optionValue = $option->{$value_field} ?? '';
                            }
                            
                            // Handle composite keys for text
                            if (is_array($text_field)) {
                                $textArray = [];
                                foreach($text_field as $field) {
                                    $textArray[] = $option->{$field} ?? '';
                                }
                                $optionText = implode(' - ', $textArray);
                            } else {
                                $optionText = $option->{$text_field} ?? '';
                            }
                            
                            // Get parent field value
                            $parentValue = isset($parent_field) ? ($option->{$parent_field} ?? '') : '';
                            
                        } else {
                            // Handle arrays
                            if (is_array($value_field)) {
                                $valueArray = [];
                                foreach($value_field as $field) {
                                    $valueArray[] = $option[$field] ?? '';
                                }
                                $optionValue = implode('|', $valueArray);
                            } else {
                                $optionValue = $option[$value_field] ?? '';
                            }
                            
                            if (is_array($text_field)) {
                                $textArray = [];
                                foreach($text_field as $field) {
                                    $textArray[] = $option[$field] ?? '';
                                }
                                $optionText = implode(' - ', $textArray);
                            } else {
                                $optionText = $option[$text_field] ?? '';
                            }
                            
                            $parentValue = isset($parent_field) ? ($option[$parent_field] ?? '') : '';
                        }
                    @endphp

                    <div class="combo-option px-4 py-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border-t border-gray-100 dark:border-gray-600 first:border-t-0"
                         data-value="{{ $optionValue }}"
                         data-text="{{ $optionText }}"
                         data-parent="{{ $parentValue }}"
                         data-search="{{ strtolower($optionText) }}">
                        <div class="flex justify-between items-center">
                            <span>{{ $optionText }}</span>
                            <span class="selected-icon hidden text-blue-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="px-4 py-3 text-gray-500 dark:text-gray-400 text-center">
                    No hay opciones disponibles
                </div>
            @endif
        </div>
    </div>

    <!-- Error Message -->
    @if(isset($error) && $error)
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('[data-component-id="{{ $componentId }}"]');
    const button = container.querySelector('.combo-button');
    const dropdown = container.querySelector('.combo-dropdown');
    const searchInput = container.querySelector('.combo-search');
    const hiddenInput = container.querySelector('input[type="hidden"]');
    const options = container.querySelectorAll('.combo-option');
    const selectText = container.querySelector('.combo-text');
    const arrow = container.querySelector('.combo-arrow');
    
    const fieldName = '{{ $name }}';
    const dependsOn = '{{ $depends_on ?? '' }}';
    const originalPlaceholder = container.dataset.placeholder;
    const isEnabled = {{ ($enabled ?? true) ? 'true' : 'false' }};

    // Function to reset placeholder text
    function resetPlaceholder() {
        selectText.textContent = originalPlaceholder;
        selectText.classList.add('text-gray-500', 'dark:text-gray-400');
        selectText.classList.remove('text-gray-900', 'dark:text-white');
    }

    // Function to set selected text
    function setSelectedText(text) {
        selectText.textContent = text;
        selectText.classList.remove('text-gray-500', 'dark:text-gray-400');
        selectText.classList.add('text-gray-900', 'dark:text-white');
    }

    // Function to enable/disable combo
    function setEnabled(enabled) {
        if (enabled) {
            button.disabled = false;
            button.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-100', 'dark:bg-gray-700');
            button.classList.add('hover:border-gray-400', 'dark:hover:border-gray-500');
        } else {
            button.disabled = true;
            button.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-100', 'dark:bg-gray-700');
            button.classList.remove('hover:border-gray-400', 'dark:hover:border-gray-500');
            
            // Clear selection and reset placeholder
            hiddenInput.value = '';
            resetPlaceholder();
            
            // Clear selected icons
            options.forEach(opt => opt.querySelector('.selected-icon').classList.add('hidden'));
        }
    }

    // Function to filter options based on parent value
    function filterOptions(parentValue = '') {
        options.forEach(option => {
            const optionParent = option.dataset.parent;
            const shouldShow = !parentValue || !optionParent || optionParent === parentValue;
            option.style.display = shouldShow ? 'block' : 'none';
        });
    }

    // Function to reset all dependent combos
    function resetDependentCombos() {
        document.querySelectorAll('.combo-container').forEach(otherContainer => {
            const otherDependsOn = otherContainer.dataset.dependsOn;
            const otherFieldName = otherContainer.dataset.fieldName;
            
            if (otherDependsOn === fieldName) {
                const otherHiddenInput = otherContainer.querySelector('input[type="hidden"]');
                const otherSelectText = otherContainer.querySelector('.combo-text');
                const otherOptions = otherContainer.querySelectorAll('.combo-option');
                const otherPlaceholder = otherContainer.dataset.placeholder;
                
                // Reset the dependent combo
                otherHiddenInput.value = '';
                otherSelectText.textContent = otherPlaceholder;
                otherSelectText.classList.add('text-gray-500', 'dark:text-gray-400');
                otherSelectText.classList.remove('text-gray-900', 'dark:text-white');
                
                // Clear selected icons
                otherOptions.forEach(opt => opt.querySelector('.selected-icon').classList.add('hidden'));
                
                // Disable the dependent combo
                const otherButton = otherContainer.querySelector('.combo-button');
                otherButton.disabled = true;
                otherButton.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-100', 'dark:bg-gray-700');
                otherButton.classList.remove('hover:border-gray-400', 'dark:hover:border-gray-500');
                
                // Recursively reset any combos that depend on this one
                const resetEvent = new CustomEvent('comboReset', { 
                    detail: { fieldName: otherFieldName } 
                });
                otherContainer.dispatchEvent(resetEvent);
            }
        });
    }

    // Function to update dependent combos
    function updateDependentCombos(selectedValue) {
        document.querySelectorAll('.combo-container').forEach(otherContainer => {
            const otherDependsOn = otherContainer.dataset.dependsOn;
            
            if (otherDependsOn === fieldName) {
                const otherButton = otherContainer.querySelector('.combo-button');
                
                if (selectedValue) {
                    // Enable the dependent combo
                    otherButton.disabled = false;
                    otherButton.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-100', 'dark:bg-gray-700');
                    otherButton.classList.add('hover:border-gray-400', 'dark:hover:border-gray-500');
                    
                    // Filter options in the dependent combo
                    const otherOptions = otherContainer.querySelectorAll('.combo-option');
                    otherOptions.forEach(option => {
                        const optionParent = option.dataset.parent;
                        const shouldShow = !optionParent || optionParent === selectedValue;
                        option.style.display = shouldShow ? 'block' : 'none';
                    });
                } else {
                    // Disable the dependent combo
                    otherButton.disabled = true;
                    otherButton.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-100', 'dark:bg-gray-700');
                    otherButton.classList.remove('hover:border-gray-400', 'dark:hover:border-gray-500');
                }
            }
        });
    }

    // Toggle dropdown
    button.addEventListener('click', function(e) {
        e.preventDefault();
        if (button.disabled) return;
        
        const isOpen = dropdown.style.display !== 'none';
        
        // Close all other dropdowns
        document.querySelectorAll('.combo-dropdown').forEach(d => {
            if (d !== dropdown) {
                d.style.display = 'none';
                const otherArrow = d.parentElement.querySelector('.combo-arrow');
                if (otherArrow) otherArrow.style.transform = 'rotate(0deg)';
            }
        });
        
        if (isOpen) {
            dropdown.style.display = 'none';
            arrow.style.transform = 'rotate(0deg)';
        } else {
            dropdown.style.display = 'block';
            arrow.style.transform = 'rotate(180deg)';
            if (searchInput) searchInput.focus();
        }
    });

    // Search functionality (solo si existe el input de búsqueda)
    if (searchInput) {
        searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        options.forEach(option => {
            const searchText = option.dataset.search;
            const matchesSearch = searchText.includes(searchTerm);
            const optionParent = option.dataset.parent;
            
            // Check if option should be visible based on parent dependency
            let shouldShowByParent = true;
            if (dependsOn) {
                const parentContainer = document.querySelector(`[data-field-name="${dependsOn}"]`);
                if (parentContainer) {
                    const parentInput = parentContainer.querySelector('input[type="hidden"]');
                    const parentValue = parentInput ? parentInput.value : '';
                    shouldShowByParent = !optionParent || !parentValue || optionParent === parentValue;
                }
            } else {
                shouldShowByParent = !optionParent || optionParent === '';
            }
            
            // Show option only if it matches search AND parent dependency
            option.style.display = (matchesSearch && shouldShowByParent) ? 'block' : 'none';
        });
    });
    }

    // Option selection
    options.forEach(option => {
        option.addEventListener('click', function() {
            const value = this.dataset.value;
            const text = this.dataset.text;
            
            // Update UI
            setSelectedText(text);
            
            // Update hidden input
            hiddenInput.value = value;
            
            // Update selected state
            options.forEach(opt => opt.querySelector('.selected-icon').classList.add('hidden'));
            this.querySelector('.selected-icon').classList.remove('hidden');
            
            // Close dropdown
            dropdown.style.display = 'none';
            arrow.style.transform = 'rotate(0deg)';
            if (searchInput) searchInput.value = '';
            
            // Reset search filter
           options.forEach(opt => {
                const optionParent = opt.dataset.parent;
                let shouldShow = true;
                
                if (dependsOn) {
                    const parentContainer = document.querySelector(`[data-field-name="${dependsOn}"]`);
                    if (parentContainer) {
                        const parentInput = parentContainer.querySelector('input[type="hidden"]');
                        const parentValue = parentInput ? parentInput.value : '';
                        shouldShow = !optionParent || !parentValue || optionParent === parentValue;
                    }
                } else {
                    shouldShow = !optionParent || optionParent === '';
                }
                
                opt.style.display = shouldShow ? 'block' : 'none';
            });
            
            // First reset all dependent combos to clear their placeholders
            resetDependentCombos();
            
            // Then update dependent combos with new selection
            updateDependentCombos(value);
            
            // Trigger change event
            hiddenInput.dispatchEvent(new Event('change'));
        });
    });

    // Listen for reset events from parent combos
    container.addEventListener('comboReset', function(e) {
        resetDependentCombos();
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!container.contains(e.target)) {
            dropdown.style.display = 'none';
            arrow.style.transform = 'rotate(0deg)';
        }
    });

    // Initialize component state
    setEnabled(isEnabled);
    
    // If this combo depends on another, check parent state
    if (dependsOn) {
        const parentContainer = document.querySelector(`[data-field-name="${dependsOn}"]`);
        if (parentContainer) {
            const parentInput = parentContainer.querySelector('input[type="hidden"]');
            if (parentInput && parentInput.value) {
                setEnabled(true);
                filterOptions(parentInput.value);
            } else {
                setEnabled(false);
            }
        }
    }
    
    // Set initial value if provided
    const initialValue = '{{ $value ?? '' }}';
    if (initialValue) {
        const matchingOption = Array.from(options).find(opt => opt.dataset.value === initialValue);
        if (matchingOption) {
            setSelectedText(matchingOption.dataset.text);
            hiddenInput.value = initialValue;
            matchingOption.querySelector('.selected-icon').classList.remove('hidden');
        }
    }
});
</script>
