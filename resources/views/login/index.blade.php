<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colegio Sigma - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/login.css'])

    <style>
        *, *::before, *::after { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
    </style>
</head>
<body>
    {{-- Toast de errores: fixed en esquina superior derecha --}}
    @if ($errors->any())
        <div class="toast-error-container" id="toastContainer">
            @foreach ($errors->all() as $error)
                <div class="toast-error">
                    <div class="toast-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="toast-content">
                        <p>{{ $error }}</p>
                    </div>
                    <button type="button" class="toast-close" onclick="dismissToast(this.parentElement)">
                        &times;
                    </button>
                </div>
            @endforeach
        </div>
    @endif

    <div class="container">
        <div class="login-container">
            <div class="brand flex align-center">
                <img style="height: 2rem; display:inline" src="{{ asset('images/sigma_logo.png') }}" alt="Sigma LOGO">
                <h1 style="display:inline">SIGMA</h1>
            </div>
            <div class="welcome-text">
                <h2>Bienvenido,</h2>
                <p>por favor, introduce tus datos</p>
            </div>
            <form id="loginForm" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="username">Nombre de usuario</label>
                    {{-- Usamos una condición simple para agregar la clase 'invalid-input' --}}
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        value="{{ old('username') }}" 
                        class="{{ $errors->has('username') ? 'invalid-input' : '' }}" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="{{ $errors->has('password') ? 'invalid-input' : '' }}" 
                        required
                    >
                </div>

                <div class="forgot-password">
                    <a href="#">¿Olvidó su contraseña?</a>
                </div>

                <button type="submit" class="btn-login" onclick="sessionStorage.removeItem('toastsDismissed')">Iniciar sesión</button>
            </form>

            <!-- Separador -->
            <div class="login-separator">
                <span>o</span>
            </div>

            <!-- Botón de Pasarela de Pagos -->
            <a href="{{ route('pasarela.index') }}" id="btn-pagar-en-linea" class="btn-pasarela">
                <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                    <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                </svg>
                Pagar en Línea
            </a>
            <p class="helper-text">
                Realiza tus pagos de forma rápida y segura
            </p>


            @if (App\Services\Cronograma\CronogramaAcademicoService::preMatriculaHabilitada())
            <a href="{{ route('solicitud_prematricula.create') }}" id="btn-solicitar-prematricula" class="btn-prematricula">
                <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path>
                </svg>
                Solicitar Prematrícula
            </a>
            <p class="helper-text">
                ¿Nuevo estudiante? Solicita tu prematrícula aquí
            </p>

            <div class="footer">
                <p>&copy; 2025</p>
            </div>
            @endif
        </div>
        <div class="image-container" style="background-image: url({{ asset('images/login/fondo2.jpg') }});">
        </div>
    </div>

    {{-- Script para auto-desaparecer los toasts y limpiar en navegación atrás/adelante --}}
    <script>
        (function() {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            // Si los toasts YA fueron mostrados antes, eliminar de inmediato
            const toastKey = 'toastShown_' + document.querySelector('meta[name="csrf-token"]')?.content?.slice(0, 8);
            if (sessionStorage.getItem('toastsDismissed')) {
                container.remove();
                return;
            }

            // Marcar como mostrados
            sessionStorage.setItem('toastsDismissed', '1');

            // Auto-dismiss tras 5s
            const toasts = container.querySelectorAll('.toast-error');
            toasts.forEach(toast => {
                setTimeout(() => {
                    toast.style.transition = "opacity 0.5s ease";
                    toast.style.opacity = "0";
                    setTimeout(() => toast.remove(), 500);
                }, 5000);
            });
        })();

        // También limpiar en bfcache por si acaso
        window.addEventListener('pageshow', function(e) {
            if (e.persisted) {
                const container = document.getElementById('toastContainer');
                if (container) container.remove();
            }
        });
    </script>

    {{-- Script para continuar la guía de pagos (Paso 2) --}}
    <script>
        // ============ FUNCIONES AUXILIARES PARA LA GUÍA ============
        function obtenerEstadoGuia() {
            const dataStr = sessionStorage.getItem('guiaPagos');
            if (!dataStr) return null;
            
            try {
                const data = JSON.parse(dataStr);
                // Verificar si ha expirado (5 minutos)
                if (Date.now() > data.expira) {
                    console.log('Guía expirada, limpiando...');
                    sessionStorage.removeItem('guiaPagos');
                    return null;
                }
                return data;
            } catch (e) {
                console.error('Error parseando guía:', e);
                sessionStorage.removeItem('guiaPagos');
                return null;
            }
        }

        function limpiarGuia() {
            sessionStorage.removeItem('guiaPagos');
            console.log('Guía limpiada de sessionStorage');
        }

        // Función de emergencia (desde consola)
        window.limpiarTodo = function() {
            sessionStorage.clear();
            localStorage.removeItem('usuarioConoceGuiaPagos');
            console.log('Todo limpiado');
            location.reload();
        };

        // ============ INICIO DEL SCRIPT ============
        document.addEventListener('DOMContentLoaded', function() {

            // Verificar si debe continuar con la guía de pagar
            const estadoGuia = obtenerEstadoGuia();
            
            console.log('Login - Estado guía:', estadoGuia);

            // SOLO mostrar guía si el estado es válido y el paso es correcto
            if (estadoGuia && estadoGuia.activa && estadoGuia.paso === 'loginPagar') {
                console.log('✅ Iniciando guía en login');
                setTimeout(() => {
                    mostrarGuiaLoginPagar();
                }, 500);
            } else {
                console.log('❌ NO se inicia guía - Modo normal');
                // Asegurar que no haya estilos residuales
                const btnPagar = document.getElementById('btn-pagar-en-linea');
                if (btnPagar) {
                    btnPagar.style.border = '';
                    btnPagar.style.outline = '';
                    btnPagar.style.borderRadius = '';
                    btnPagar.style.position = '';
                    btnPagar.style.zIndex = '';
                }
            }
        });

        function mostrarGuiaLoginPagar() {
            // Bloquear clics ANTES de crear elementos
            document.addEventListener('click', bloquearClicsLogin, true);
            
            // Crear overlay
            const overlay = document.createElement('div');
            overlay.id = 'overlayGuiaLogin';
            overlay.className = 'fixed inset-0 z-[99998] bg-black bg-opacity-70 backdrop-blur-sm';
            document.body.appendChild(overlay);

            // Resaltar botón Pagar en Línea
            const btnPagar = document.getElementById('btn-pagar-en-linea');
            if (btnPagar) {
                btnPagar.style.position = 'relative';
                btnPagar.style.zIndex = '100000';
                btnPagar.style.border = '3px solid rgb(168, 85, 247)';
                btnPagar.style.outline = '3px solid rgba(168, 85, 247, 0.3)';
                btnPagar.style.borderRadius = '8px';
                
                // Agregar event listener para continuar con paso 3
                btnPagar.addEventListener('click', function(e) {
                    // Guardar estado para paso 3 antes de navegar
                    const timestamp = Date.now();
                    const guiaData = {
                        activa: true,
                        paso: 'pagoMetodo',
                        subpaso: 0,
                        timestamp: timestamp,
                        expira: timestamp + (5 * 60 * 1000)
                    };
                    sessionStorage.setItem('guiaPagos', JSON.stringify(guiaData));
                    console.log('✅ Estado guardado para paso 3');
                }, { once: true });
            }

            // Crear tooltip
            const tooltip = document.createElement('div');
            tooltip.className = 'fixed z-[99999] top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 max-w-md';
            tooltip.innerHTML = `
                <div class="bg-white rounded-2xl shadow-2xl border-4 border-purple-500 overflow-hidden">
                    <div style="background: linear-gradient(to right, rgb(168, 85, 247), rgb(236, 72, 153)); padding: 1.5rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="font-size: 0.75rem; font-weight: bold; color: white; text-transform: uppercase; letter-spacing: 0.05em;">Paso 2 de 3 • 2/2</span>
                            <button id="btnCerrarGuiaLogin" style="color: rgba(255,255,255,0.8); background: none; border: none; cursor: pointer;">
                                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <h3 style="font-size: 1.5rem; font-weight: bold; color: white; margin-bottom: 0.5rem;">¡Haz Clic Aquí!</h3>
                        <div style="height: 6px; background: rgba(255,255,255,0.3); border-radius: 9999px; overflow: hidden;">
                            <div style="height: 100%; background: white; border-radius: 9999px; width: 100%; transition: width 0.5s;"></div>
                        </div>
                    </div>
                    <div style="padding: 1.5rem;">
                        <p style="color: #374151; margin-bottom: 1rem;">
                            Haz clic en el botón <strong>"Pagar en Línea"</strong> para acceder a la pasarela de pagos donde el estudiante puede realizar el pago.
                        </p>
                        <div style="padding: 1rem; background: #FEF3C7; border-left: 4px solid #F59E0B; border-radius: 0.5rem; margin-bottom: 1rem;">
                            <p style="font-size: 0.875rem; color: #92400E;">
                                <strong>Nota:</strong> Necesitarás el código de la orden de pago que generaste anteriormente.
                            </p>
                        </div>
                        <div style="display: flex; justify-content: flex-end;">
                            <button id="btnEntendidoLogin" style="padding: 0.75rem 2rem; background: linear-gradient(to right, rgb(168, 85, 247), rgb(236, 72, 153)); color: white; font-weight: bold; border-radius: 0.5rem; border: none; cursor: pointer; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); transition: all 0.3s;">
                                ¡Entendido!
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(tooltip);

            // Event listeners
            document.getElementById('btnCerrarGuiaLogin').addEventListener('click', cerrarGuiaLogin);
            document.getElementById('btnEntendidoLogin').addEventListener('click', cerrarGuiaLogin);
        }

        function bloquearClicsLogin(e) {
            const btnPagar = document.getElementById('btn-pagar-en-linea');
            const btnCerrar = document.getElementById('btnCerrarGuiaLogin');
            const btnEntendido = document.getElementById('btnEntendidoLogin');
            const tooltip = document.querySelector('.fixed.z-\\[99999\\]');
            
            const elementosPermitidos = [btnPagar, btnCerrar, btnEntendido, tooltip];
            let permitido = false;
            
            for (let elem of elementosPermitidos) {
                if (elem && (elem === e.target || elem.contains(e.target))) {
                    permitido = true;
                    break;
                }
            }
            
            if (!permitido) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
        }

        function cerrarGuiaLogin() {
            console.log('Cerrando guía de login');
            
            // Limpiar estilos
            const btnPagar = document.getElementById('btn-pagar-en-linea');
            if (btnPagar) {
                btnPagar.style.border = '';
                btnPagar.style.outline = '';
                btnPagar.style.borderRadius = '';
                btnPagar.style.position = '';
                btnPagar.style.zIndex = '';
            }

            // Remover elementos
            const overlay = document.getElementById('overlayGuiaLogin');
            const tooltip = document.querySelector('.fixed.z-\\[99999\\]');
            
            if (overlay) overlay.remove();
            if (tooltip) tooltip.remove();

            // Remover listener
            document.removeEventListener('click', bloquearClicsLogin, true);

            // Limpiar sessionStorage
            limpiarGuia();
            console.log('Guía cerrada');
        }
    </script>
</body>
</html>