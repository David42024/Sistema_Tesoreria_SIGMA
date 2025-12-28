// PAGO CREATE - SECCIÓN 1 (DEUDA INDIVIDUAL)

document.addEventListener('DOMContentLoaded', () => {

    // Inicializa la funcionalidad de vouchers
    function inicializarVouchersDeudaIndividual() {
        const vouchers = [
            {
                metodo: document.getElementById('metodo_pago_1'),
                voucherGroup: document.getElementById('voucher_group_1'),
                voucherInput: document.getElementById('voucher_path_1'),
                voucherLabel: document.getElementById('voucher_label_1'),
                voucherBtn: document.getElementById('btnUploadVoucher_1'),
                reciboInput: document.getElementById('detalle_recibo_1')
            },
            {
                metodo: document.getElementById('metodo_pago_2'),
                voucherGroup: document.getElementById('voucher_group_2'),
                voucherInput: document.getElementById('voucher_path_2'),
                voucherLabel: document.getElementById('voucher_label_2'),
                voucherBtn: document.getElementById('btnUploadVoucher_2'),
                reciboInput: document.getElementById('detalle_recibo_2')
            }
        ];

        vouchers.forEach((v, index) => {
            if (!v.metodo || !v.voucherGroup) {
                return;
            }

            // Mostrar/ocultar voucher según método de pago
            v.metodo.addEventListener('change', function() {
                const metodo = this.value.toLowerCase();

                if (metodo === 'transferencia' || metodo === 'yape') {
                    v.voucherGroup.classList.remove('hidden');
                } else {
                    v.voucherGroup.classList.add('hidden');
                    if (v.voucherInput) v.voucherInput.value = '';
                    if (v.voucherLabel) v.voucherLabel.textContent = 'Seleccionar archivo';
                }

                // Habilitar/deshabilitar input de número de operación
                if (v.reciboInput) {
                    if (metodo && metodo !== '') {
                        v.reciboInput.disabled = false;
                        // ⚠️ LIMPIAR el número de operación al cambiar método de pago
                        v.reciboInput.value = '';
                        
                        // Aplicar restricciones según método de pago
                        switch(metodo) {
                            case 'yape':
                            case 'plin':
                                v.reciboInput.maxLength = 8;
                                v.reciboInput.pattern = '[0-9]{6,8}';
                                v.reciboInput.placeholder = 'Ingrese 6 a 8 dígitos';
                                v.reciboInput.setAttribute('inputmode', 'numeric');
                                v.reciboInput.setAttribute('type', 'text');
                                // Validar solo dígitos en tiempo real
                                v.reciboInput.addEventListener('input', function(e) {
                                    this.value = this.value.replace(/[^0-9]/g, '');
                                });
                                break;
                            case 'transferencia':
                                v.reciboInput.maxLength = 11;
                                v.reciboInput.pattern = '[0-9]{11}';
                                v.reciboInput.placeholder = 'Ingrese 11 dígitos';
                                v.reciboInput.setAttribute('inputmode', 'numeric');
                                v.reciboInput.setAttribute('type', 'text');
                                // Validar solo dígitos en tiempo real
                                v.reciboInput.addEventListener('input', function(e) {
                                    this.value = this.value.replace(/[^0-9]/g, '');
                                });
                                break;
                            case 'tarjeta':
                                v.reciboInput.maxLength = 13;
                                v.reciboInput.pattern = '[0-9]{13}';
                                v.reciboInput.placeholder = 'Ingrese 13 dígitos';
                                v.reciboInput.setAttribute('inputmode', 'numeric');
                                v.reciboInput.setAttribute('type', 'text');
                                // Validar solo dígitos en tiempo real
                                v.reciboInput.addEventListener('input', function(e) {
                                    this.value = this.value.replace(/[^0-9]/g, '');
                                });
                                break;
                            case 'paypal':
                                v.reciboInput.maxLength = 17;
                                v.reciboInput.pattern = '[A-Z0-9]{1,17}';
                                v.reciboInput.placeholder = 'ID de transacción PayPal (17 caracteres)';
                                v.reciboInput.setAttribute('inputmode', 'text');
                                v.reciboInput.setAttribute('type', 'text');
                                // Validar alfanumérico y convertir a mayúsculas en tiempo real
                                v.reciboInput.addEventListener('input', function(e) {
                                    this.value = this.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
                                });
                                break;
                            default:
                                v.reciboInput.removeAttribute('maxLength');
                                v.reciboInput.removeAttribute('pattern');
                                v.reciboInput.placeholder = 'Número de operación';
                                v.reciboInput.setAttribute('inputmode', 'text');
                                v.reciboInput.setAttribute('type', 'text');
                        }
                    } else {
                        v.reciboInput.disabled = true;
                        v.reciboInput.value = '';
                        v.reciboInput.placeholder = 'Seleccione un método de pago';
                        v.reciboInput.removeAttribute('maxLength');
                        v.reciboInput.removeAttribute('pattern');
                    }
                }
            });

            // Actualizar label con nombre del archivo
            if (v.voucherInput && v.voucherLabel) {
                v.voucherInput.addEventListener('change', function() {
                    const file = this.files && this.files[0];
                    if (file) {
                        v.voucherLabel.innerHTML = `<span class="text-green-600">✓ ${file.name}</span>`;
                    } else {
                        v.voucherLabel.textContent = 'Seleccionar archivo';
                    }
                });
            }

            // Abrir selector de archivo (asignar directamente para sobrescribir duplicados)
            if (v.voucherBtn && v.voucherInput) {
                v.voucherBtn.onclick = function() {
                    v.voucherInput.click();
                    return false;
                };
            }
        });
    }

    // Calcular monto pendiente automáticamente
    function calcularMontoPendiente() {
        const montoTotal = document.getElementById('monto_total_a_pagar');
        const montoPagado = document.getElementById('monto_pagado');
        const montoPendiente = document.getElementById('monto_pendiente');

        if (montoTotal && montoPagado && montoPendiente) {
            // Los campos pueden venir con formato "S/ 123.45" -> eliminar caracteres no numéricos
            const parseMonto = (v) => {
                if (!v && v !== 0) return 0;
                const cleaned = String(v).replace(/[^0-9.]/g, '');
                return parseFloat(cleaned) || 0;
            };

            const total = parseMonto(montoTotal.value);
            const pagado = parseMonto(montoPagado.value);
            const pendiente = total - pagado;

            montoPendiente.value = `S/ ${pendiente.toFixed(2)}`;
        }
    }

    // Validar montos de detalles de pago
    function validarMontos() {
        const montoTotalInput = document.getElementById('monto_total_a_pagar');
        const monto1Input = document.getElementById('detalle_monto_1');
        const monto2Input = document.getElementById('detalle_monto_2');

        if (!montoTotalInput || !monto1Input || !monto2Input) return;

        const parseMonto = (v) => {
            if (!v && v !== 0) return 0;
            const cleaned = String(v).replace(/[^0-9.]/g, '');
            return parseFloat(cleaned) || 0;
        };

        const montoTotal = parseMonto(montoTotalInput.value);
        const monto1 = parseMonto(monto1Input.value);
        const monto2 = parseMonto(monto2Input.value);

        // Calcular resto disponible para cada input
        const restoParaMonto1 = montoTotal - monto2;
        const restoParaMonto2 = montoTotal - monto1;

        // Actualizar placeholders dinámicamente
        if (monto2 > 0 && restoParaMonto1 >= 0) {
            monto1Input.placeholder = `Máximo disponible: S/ ${restoParaMonto1.toFixed(2)}`;
        } else {
            monto1Input.placeholder = '';
        }

        if (monto1 > 0 && restoParaMonto2 >= 0) {
            monto2Input.placeholder = `Máximo disponible: S/ ${restoParaMonto2.toFixed(2)}`;
        } else {
            monto2Input.placeholder = '';
        }

        // Limitar valores al máximo disponible (sin alertas)
        if (monto1 > restoParaMonto1 && restoParaMonto1 >= 0) {
            monto1Input.value = restoParaMonto1.toFixed(2);
        }

        if (monto2 > restoParaMonto2 && restoParaMonto2 >= 0) {
            monto2Input.value = restoParaMonto2.toFixed(2);
        }

        // Si la suma supera, ajustar el último valor modificado
        const sumaTotal = parseFloat(monto1Input.value || 0) + parseFloat(monto2Input.value || 0);
        if (sumaTotal > montoTotal) {
            // Determinar cuál input fue el último modificado y ajustarlo
            const target = document.activeElement;
            if (target === monto1Input) {
                monto1Input.value = (montoTotal - parseFloat(monto2Input.value || 0)).toFixed(2);
            } else if (target === monto2Input) {
                monto2Input.value = (montoTotal - parseFloat(monto1Input.value || 0)).toFixed(2);
            }
        }
    }

    // Formatear monto a dos decimales al salir del campo
    function formatearMonto(input) {
        const valor = parseFloat(input.value);
        if (!isNaN(valor) && valor >= 0) {
            input.value = valor.toFixed(2);
        } else if (input.value !== '') {
            input.value = '0.00';
        }
    }

    // Validar que solo se ingresen números y punto decimal
    function validarNumerico(input) {
        input.value = input.value.replace(/[^0-9.]/g, '');
        // Permitir solo un punto decimal
        const parts = input.value.split('.');
        if (parts.length > 2) {
            input.value = parts[0] + '.' + parts.slice(1).join('');
        }
    }

    window.inicializarVouchersDeudaIndividual = inicializarVouchersDeudaIndividual;

    inicializarVouchersDeudaIndividual();
    
    // Calcular monto pendiente al cargar y cuando cambien los montos
    calcularMontoPendiente();
    
    const montoTotalInput = document.getElementById('monto_total_a_pagar');
    const montoPagadoInput = document.getElementById('monto_pagado');
    
    if (montoTotalInput) {
        montoTotalInput.addEventListener('change', calcularMontoPendiente);
    }
    if (montoPagadoInput) {
        montoPagadoInput.addEventListener('change', calcularMontoPendiente);
    }

    // Agregar validación de montos en detalles de pago
    const monto1Input = document.getElementById('detalle_monto_1');
    const monto2Input = document.getElementById('detalle_monto_2');

    if (monto1Input) {
        monto1Input.addEventListener('input', function() {
            validarNumerico(this);
            validarMontos();
        });
        monto1Input.addEventListener('blur', function() {
            formatearMonto(this);
            validarMontos();
        });
    }
    
    if (monto2Input) {
        monto2Input.addEventListener('input', function() {
            validarNumerico(this);
            validarMontos();
        });
        monto2Input.addEventListener('blur', function() {
            formatearMonto(this);
            validarMontos();
        });
    }

    // Formatear inputs de fecha DD/MM/YYYY
    function formatearFecha(input) {
        let valor = input.value.replace(/\D/g, ''); // Solo números
        
        if (valor.length >= 2) {
            valor = valor.substring(0, 2) + '/' + valor.substring(2);
        }
        if (valor.length >= 5) {
            valor = valor.substring(0, 5) + '/' + valor.substring(5, 9);
        }
        
        input.value = valor;
    }

    const fecha1Input = document.getElementById('detalle_fecha_1');
    const fecha2Input = document.getElementById('detalle_fecha_2');

    if (fecha1Input) {
        fecha1Input.addEventListener('input', function() {
            formatearFecha(this);
        });
    }

    if (fecha2Input) {
        fecha2Input.addEventListener('input', function() {
            formatearFecha(this);
        });
    }

    // Convertir fechas DD/MM/YYYY a YYYY-MM-DD antes de enviar el formulario
    const formulario = document.querySelector('form[action*="pago"]');
    if (formulario) {
        formulario.addEventListener('submit', function(e) {
            // Convertir fecha 1
            if (fecha1Input && fecha1Input.value) {
                const partes1 = fecha1Input.value.split('/');
                if (partes1.length === 3) {
                    const fechaISO1 = `${partes1[2]}-${partes1[1]}-${partes1[0]}`;
                    fecha1Input.value = fechaISO1;
                }
            }
            
            // Convertir fecha 2
            if (fecha2Input && fecha2Input.value) {
                const partes2 = fecha2Input.value.split('/');
                if (partes2.length === 3) {
                    const fechaISO2 = `${partes2[2]}-${partes2[1]}-${partes2[0]}`;
                    fecha2Input.value = fechaISO2;
                }
            }
        });
    }
});
