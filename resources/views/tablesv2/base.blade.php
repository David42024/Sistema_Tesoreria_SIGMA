<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
    />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />

    @vite(['resources/css/style.css', 'resources/js/index.js'])
    
    <title>
        {{ $page->title }}
    </title>
    @yield('extracss')
  </head>
  <body
    x-data="{ page: '', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }"
    x-init="
         darkMode = JSON.parse(localStorage.getItem('darkMode'));
         $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
    :class="{'dark bg-gray-900': darkMode === true}"
  >

    @if ($page->modals != null)
      @foreach ($page->modals as $modal)
        {{ $modal->render() }}
      @endforeach
    @endif

    <!-- ===== Preloader Start ===== -->
    @include('layout.preloader')
    <!-- ===== Preloader End ===== -->
    
    @if ($page->topbar != null)
    {{ $page->topbar->render() }}
    @endif

    <!-- ===== Page Wrapper Start ===== -->
    <div class="flex h-screen overflow-hidden">
      <!-- ===== Sidebar Start ===== -->
      @if ($page->sidebar != null)
      {{ $page->sidebar->render() }}
      @endif
      <!-- ===== Sidebar End ===== -->

      <!-- ===== Content Area Start ===== -->
      <div
        class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto"
      >
        <!-- Small Device Overlay Start -->
        @include('layout.overlay')
        <!-- Small Device Overlay End -->

        <!-- ===== Header Start ===== -->
        @if ($page->header != null)
        {{ $page->header->render() }}
        @endif
        <!-- ===== Header End ===== -->

        <!-- ===== Main Content Start ===== -->
        <main class="p-4">
          @if (session('success'))
            <div id="flash-success" class="fixed right-4 top-20 z-[9999] max-w-md rounded-2xl border border-green-200 bg-white px-4 py-3 text-green-800 shadow-lg">
              <div class="flex items-start gap-3">
                <div class="mt-1 h-2.5 w-2.5 flex-none rounded-full bg-green-500"></div>
                <div>
                  <p class="text-sm font-semibold">Correcto</p>
                  <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
              </div>
            </div>
          @endif
          @if (session('error'))
            <div id="flash-error" class="fixed right-4 top-20 z-[9999] max-w-md rounded-2xl border border-red-200 bg-white px-4 py-3 text-red-800 shadow-lg">
              <div class="flex items-start gap-3">
                <div class="mt-1 h-2.5 w-2.5 flex-none rounded-full bg-red-500"></div>
                <div>
                  <p class="text-sm font-semibold">Atencion</p>
                  <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
              </div>
            </div>
          @endif
          @if ($page->content != null)
          {{ $page->content->render() }}
          @endif
        </main>

        <script>
          (function () {
            const hideAfterMs = 3500;
            const fadeMs = 400;
            const ids = ['flash-success', 'flash-error'];

            ids.forEach((id) => {
              const el = document.getElementById(id);
              if (!el) return;

              setTimeout(() => {
                el.style.transition = `opacity ${fadeMs}ms ease`;
                el.style.opacity = '0';
                setTimeout(() => el.remove(), fadeMs);
              }, hideAfterMs);
            });
          })();
        </script>
        <!-- ===== Main Content End ===== -->
      </div>
      <!-- ===== Content Area End ===== -->
    </div>
    <!-- ===== Page Wrapper End ===== -->
  </body>
</html>


