@php
    $dropdownName = $name ?? 'default';
    $forSelectedItems = "(";

    for ($i = 0; $i < count($items); $i++) {
        $forSelectedItems .= "page === ";
        $forSelectedItems .= "'" . $items[$i] . "'";

        if ($i < count($items) - 1){
            $forSelectedItems .= " || ";
        }
    }

    $forSelectedItems .= ')'
@endphp

<!-- Menu Item Dashboard -->
<li>
    <a href="#" id="menu-{{ Str::slug($dropdownName) }}" @click.prevent="selected = (selected === '{{ $dropdownName }}' ? '':'{{ $dropdownName }}')" class="menu-item group" :class=" (selected === '{{ $dropdownName }}') || {{ $forSelectedItems }} ? 'menu-item-active' : 'menu-item-inactive'">
        <svg :class="(selected === 'Dashboard') || (page === 'ecommerce' || page === 'analytics' || page === 'marketing' || page === 'crm' || page === 'stocks') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'" width="24"
            height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        
        @php
            $selectedIcon = 'components.icons.' . ($icon ?? 'default');
        @endphp

        @include($selectedIcon)
        </svg>

        <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
            {{$dropdownName}}
        </span>

        <svg class="menu-item-arrow" :class="[(selected === '{{ $dropdownName }}') ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '']" width="20" height="20" viewBox="0 0 20 20"
            fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5"
                stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </a>

    <!-- Dropdown Menu Start -->
    <div class="overflow-hidden transform translate" :class="(selected === '{{ $dropdownName }}') ? 'block' : 'hidden'">
        <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 mt-2 menu-dropdown pl-9">
            @foreach ($items as $item)
                <li>
                    <a href="{{ route($links[$item] ?? 'principal')}}" id="submenu-{{ Str::slug($item) }}" class="menu-dropdown-item group" :class="page === '{{ $item }}' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                        {{ $item }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>