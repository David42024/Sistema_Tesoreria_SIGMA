<div
  class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6">
  <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
        {{ $titulo }}
      </h3>
    </div>

    <div class="flex items-center gap-3">
      <button
        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
        <svg class="stroke-current fill-white dark:fill-gray-800" width="20" height="20" viewBox="0 0 20 20" fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path d="M2.29004 5.90393H17.7067" stroke="" stroke-width="1.5" stroke-linecap="round"
            stroke-linejoin="round" />
          <path d="M17.7075 14.0961H2.29085" stroke="" stroke-width="1.5" stroke-linecap="round"
            stroke-linejoin="round" />
          <path
            d="M12.0826 3.33331C13.5024 3.33331 14.6534 4.48431 14.6534 5.90414C14.6534 7.32398 13.5024 8.47498 12.0826 8.47498C10.6627 8.47498 9.51172 7.32398 9.51172 5.90415C9.51172 4.48432 10.6627 3.33331 12.0826 3.33331Z"
            fill="" stroke="" stroke-width="1.5" />
          <path
            d="M7.91745 11.525C6.49762 11.525 5.34662 12.676 5.34662 14.0959C5.34661 15.5157 6.49762 16.6667 7.91745 16.6667C9.33728 16.6667 10.4883 15.5157 10.4883 14.0959C10.4883 12.676 9.33728 11.525 7.91745 11.525Z"
            fill="" stroke="" stroke-width="1.5" />
        </svg>

        Filtros
      </button>

      <button
        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
        Ver todo
      </button>

      @can('manage-resource', [$resource, 'create'])
        <a href="{{ route($create) }}" id="btn-crear-orden-pago"
          class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-gray-200 px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-300 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
          Crear un nuevo registro
        </a>
      @endcan
    </div>
  </div>

  <div class="w-full overflow-x-auto text-[0.9rem]">
    <div class="flex items-center justify-between pb-4">
      <div class="flex items-center gap-3">
        <span class="text-gray-500 dark:text-gray-300">Viendo</span>
        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
          <select
            class="select-entries dark:bg-dark-900 h-9 w-full appearance-none rounded-lg border border-gray-300 bg-transparent py-2 pl-3 pr-8 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 text-gray-500 dark:text-gray-400 inline"
            :class="isOptionSelected ? 'text-gray-600 dark:text-gray-200' : ''"
            @click="isOptionSelected = true" @change="perPage = $event.target.value">
            <option value="10" @if ($showing == 10) selected @endif
              class="text-gray-500 dark:bg-gray-900 dark:text-gray-400">
              10
            </option>
            <option value="8" @if ($showing == 8) selected @endif
              class="text-gray-500 dark:bg-gray-900 dark:text-gray-400">
              8
            </option>
            <option value="5" @if ($showing == 5) selected @endif
              class="text-gray-500 dark:bg-gray-900 dark:text-gray-400">
              5
            </option>
          </select>
          <span class="absolute right-2 top-1/2 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400 pointer-events-none">
            <svg class="stroke-current" width="16" height="16" viewBox="0 0 16 16" fill="none"
              xmlns="http://www.w3.org/2000/svg">
              <path d="M3.8335 5.9165L8.00016 10.0832L12.1668 5.9165" stroke="" stroke-width="1.2"
                stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
          </span>
        </div>
        <span class="text-gray-500 dark:text-gray-300">entradas</span>
      </div>

      <div class="relative">
        <span class="pointer-events-none absolute top-1/2 left-4 -translate-y-1/2">
          <svg class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M3.04199 9.37381C3.04199 5.87712 5.87735 3.04218 9.37533 3.04218C12.8733 3.04218 15.7087 5.87712 15.7087 9.37381C15.7087 12.8705 12.8733 15.7055 9.37533 15.7055C5.87735 15.7055 3.04199 12.8705 3.04199 9.37381ZM9.37533 1.54218C5.04926 1.54218 1.54199 5.04835 1.54199 9.37381C1.54199 13.6993 5.04926 17.2055 9.37533 17.2055C11.2676 17.2055 13.0032 16.5346 14.3572 15.4178L17.1773 18.2381C17.4702 18.531 17.945 18.5311 18.2379 18.2382C18.5308 17.9453 18.5309 17.4704 18.238 17.1775L15.4182 14.3575C16.5367 13.0035 17.2087 11.2671 17.2087 9.37381C17.2087 5.04835 13.7014 1.54218 9.37533 1.54218Z"
              fill=""></path>
          </svg>
        </span>

        <input type="text" placeholder="Buscar..."
          class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-[42px] w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-4 pl-[42px] text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden xl:w-[300px] dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
      </div>
    </div>

    <table class="min-w-full">
      <!-- table header start -->
      <thead class="">
        <tr class="border-gray-100 border-y dark:border-gray-800">
          @foreach ($columnas as $columna)
            <th class="py-3">
              <div class="flex items-center">
                <p class="font-medium text-gray-900 text-theme-xs dark:text-gray-300">
                  {{ $columna }}
                </p>
              </div>
            </th>
          @endforeach

          @if (
              Gate::allows('manage-resource', [$resource, 'edit']) ||
              Gate::allows('manage-resource', [$resource, 'delete']) ||
              Gate::allows('manage-resource', [$resource, 'view_details'])
            )
            <th class="py-3">
              <div class="flex items-center justify-center">
                <p class="font-medium text-gray-900 text-theme-xs dark:text-gray-300">
                  Acción
                </p>
              </div>
            </th>
          @endif
        </tr>
      </thead>
      <!-- Aquí es donde aparece los datos en el CRUD -->
      <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
      @foreach($filas as $fila)
        <tr>
          @for($i = 1; $i < count($fila); $i++)
            <td class="py-3">
              <div class="flex items-center">
                <p data-order="{{ $i }}" class="row{{ $fila[0] }} text-gray-600 text-theme-sm dark:text-gray-400">
                  {{ $fila[$i] }}
                </p>
              </div>
            </td>
          @endfor

          @if (
              Gate::allows('manage-resource', [$resource, 'edit']) ||
              Gate::allows('manage-resource', [$resource, 'delete']) ||
              Gate::allows('manage-resource', [$resource, 'view_details'])
            )
            <td class="py-3">
              <div class="flex gap-4 items-center justify-center">
                @can('manage-resource', [$resource, 'view_details'])
                  <a href="{{ route('orden_pago_pdf', [$fila[0]]) }}" target="_blank"
                    class="button text-blue-600 hover:text-blue-800 text-theme-sm dark:text-blue-400 dark:hover:text-blue-300">
                    Ver PDF
                  </a>
                @endcan

                @can('manage-resource', [$resource, 'delete'])
                  <button data-id="{{$fila[0]}}" class="delete-button text-red-600 hover:text-red-800 text-theme-sm dark:text-red-400 dark:hover:text-red-300">
                    Eliminar
                  </button>
                @endcan
              </div>
            </td>
          @endif
        </tr>
      @endforeach
      </tbody>
    </table>

    @if (isset($paginaActual))
      <div class="flex w-full justify-center gap-8 p-4 items-center">

        @if ($paginaActual > 1)
          <button id="{{ $paginaActual - 1 }}"
            class="anterior-pag flex gap-2 text-gray-700 hover:bg-gray-100 bg-white dark:hover:text-gray-100 dark:text-gray-300 dark:bg-gray-700 border dark:border-gray-500 rounded-lg py-2 px-4">
            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
              xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M2.58301 9.99868C2.58272 10.1909 2.65588 10.3833 2.80249 10.53L7.79915 15.5301C8.09194 15.8231 8.56682 15.8233 8.85981 15.5305C9.15281 15.2377 9.15297 14.7629 8.86018 14.4699L5.14009 10.7472L16.6675 10.7472C17.0817 10.7472 17.4175 10.4114 17.4175 9.99715C17.4175 9.58294 17.0817 9.24715 16.6675 9.24715L5.14554 9.24715L8.86017 5.53016C9.15297 5.23717 9.15282 4.7623 8.85983 4.4695C8.56684 4.1767 8.09197 4.17685 7.79917 4.46984L2.84167 9.43049C2.68321 9.568 2.58301 9.77087 2.58301 9.99715C2.58301 9.99766 2.58301 9.99817 2.58301 9.99868Z"
                fill=""></path>
            </svg>
            Anterior
          </button>
        @endif

        <p class="text-gray-700 dark:text-gray-300">Mostrando página {{ $paginaActual }} de {{ $totalPaginas }}</p>

        @if ($paginaActual < $totalPaginas)
          <button id="{{ $paginaActual + 1 }}"
            class="siguiente-pag flex gap-2 text-gray-700 hover:bg-gray-100 bg-white dark:hover:text-gray-100 dark:text-gray-300 dark:bg-gray-700 border dark:border-gray-500 rounded-lg py-2 px-4">
            Siguiente
            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
              xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M17.4175 9.9986C17.4178 10.1909 17.3446 10.3832 17.198 10.53L12.2013 15.5301C11.9085 15.8231 11.4337 15.8233 11.1407 15.5305C10.8477 15.2377 10.8475 14.7629 11.1403 14.4699L14.8604 10.7472L3.33301 10.7472C2.91879 10.7472 2.58301 10.4114 2.58301 9.99715C2.58301 9.58294 2.91879 9.24715 3.33301 9.24715L14.8549 9.24715L11.1403 5.53016C10.8475 5.23717 10.8477 4.7623 11.1407 4.4695C11.4336 4.1767 11.9085 4.17685 12.2013 4.46984L17.1588 9.43049C17.3173 9.568 17.4175 9.77087 17.4175 9.99715C17.4175 9.99763 17.4175 9.99812 17.4175 9.9986Z"
                fill=""></path>
            </svg>
          </button>
        @endif
      </div>
    @endif
  </div>
</div>
