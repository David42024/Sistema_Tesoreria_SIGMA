<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    private $apiKey;
    private $model;

    public function __construct()
    {
        // API Key de Groq - agrégala al .env
        $this->apiKey = env('GROQ_API_KEY', '');
        $this->model = 'llama-3.3-70b-versatile'; // Modelo más reciente de Groq
    }

    /**
     * Analizar un voucher de pago usando el texto extraído
     * 
     * @param string $textoVoucher Texto extraído del voucher por OCR
     * @param float $montoEsperado Monto que se espera en el pago
     * @param string $fechaEsperada Fecha esperada del pago
     * @param array $datosAdicionales Datos adicionales como transacción de pasarela
     * @return array ['porcentaje' => int, 'recomendacion' => string, 'razon' => string]
     */
    public function analizarVoucher($textoVoucher, $montoEsperado, $fechaEsperada, $datosAdicionales = [])
    {
        try {
            if (empty($this->apiKey)) {
                return [
                    'porcentaje' => 50,
                    'recomendacion' => 'pendiente',
                    'razon' => 'API Key de Groq no configurada'
                ];
            }

            // Crear el prompt para Groq
            $prompt = $this->crearPromptAnalisis($textoVoucher, $montoEsperado, $fechaEsperada, $datosAdicionales);

            // Llamar a la API de Groq
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un experto en validación de comprobantes de pago y transacciones bancarias. Tu tarea es analizar vouchers y determinar su autenticidad y validez, priorizando el número de operación y monto sobre la fecha.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.3,
                'max_tokens' => 500,
            ]);

            if (!$response->successful()) {
                Log::error('Error en Groq API: ' . $response->body());
                
                return [
                    'porcentaje' => 50,
                    'recomendacion' => 'pendiente',
                    'razon' => 'Error al conectar con Groq'
                ];
            }

            $data = $response->json();
            $respuesta = $data['choices'][0]['message']['content'] ?? '';

            // Parsear la respuesta de Groq
            return $this->parsearRespuesta($respuesta);

        } catch (\Exception $e) {
            Log::error('Error en GroqService: ' . $e->getMessage());
            
            return [
                'porcentaje' => 50,
                'recomendacion' => 'pendiente',
                'razon' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Crear el prompt para el análisis
     */
    private function crearPromptAnalisis($textoVoucher, $montoEsperado, $fechaEsperada, $datosAdicionales = [])
    {
        $numeroOperacion = $datosAdicionales['numero_operacion'] ?? 'No proporcionado';
        $metodoPago = $datosAdicionales['metodo_pago'] ?? 'No especificado';
        $tieneTransaccion = $datosAdicionales['tiene_transaccion_pasarela'] ?? false;
        
        $infoTransaccion = '';
        if ($tieneTransaccion) {
            $transaccionMonto = $datosAdicionales['transaccion_monto'] ?? 0;
            $transaccionFecha = $datosAdicionales['transaccion_fecha'] ?? 'N/A';
            $infoTransaccion = "
✅ TRANSACCIÓN ENCONTRADA EN SISTEMA:
- Monto registrado: S/ {$transaccionMonto}
- Fecha registrada: {$transaccionFecha}
- Estado: Confirmada en pasarela de pagos";
        } else {
            $infoTransaccion = "
⚠️ TRANSACCIÓN NO ENCONTRADA EN SISTEMA:
No se encontró este número de operación en nuestra base de datos de transacciones.";
        }
        
        return <<<PROMPT
Analiza el siguiente voucher de pago y determina si es válido y auténtico:

TEXTO EXTRAÍDO DEL VOUCHER (OCR):
{$textoVoucher}

DATOS DEL PAGO REGISTRADO:
- Número de operación: {$numeroOperacion}
- Método de pago: {$metodoPago}
- Monto esperado: S/ {$montoEsperado}
- Fecha registrada: {$fechaEsperada}
{$infoTransaccion}

CRITERIOS DE VALIDACIÓN (EN ORDEN DE PRIORIDAD):

1. 🔑 NÚMERO DE OPERACIÓN (PRIORIDAD MÁXIMA):
   - DEBE existir en el texto del voucher
   - DEBE coincidir con el número registrado
   - Si existe transacción en sistema: +40% confianza automática
   - Si NO existe transacción pero el número aparece en voucher: investigar estructura

2. 💵 MONTO (PRIORIDAD ALTA):
   - DEBE coincidir con el monto esperado (tolerancia ±0.50 soles)
   - Si hay transacción en sistema y montos coinciden: +30% confianza

3. 🖼️ ESTRUCTURA VISUAL (PRIORIDAD MEDIA):
   - Verifica que sea un voucher legítimo de {$metodoPago}
   - Busca elementos característicos: logos, formatos, tipografía
   - Detecta señales de manipulación o falsificación

4. 📅 FECHA (PRIORIDAD BAJA - FLEXIBLE):
   - La fecha puede diferir 2-7 días por error humano
   - NO rechaces solo por diferencia de fecha si otros criterios son correctos
   - Si fecha difiere pero número de operación y monto coinciden: VALIDAR

🚨 IMPORTANTE:
- Si el número de operación coincide Y el monto coincide: VALIDAR (incluso si fecha difiere)
- Si existe transacción confirmada en sistema: Alta probabilidad de validación
- Solo RECHAZAR si hay evidencia clara de falsificación o montos no coinciden
- Error de fecha NO es motivo de rechazo si otros datos son correctos

RESPONDE EN EL SIGUIENTE FORMATO EXACTO:
PORCENTAJE: [número entre 0 y 100]
RECOMENDACION: [validado o rechazado]
RAZON: [explicación breve de máximo 150 caracteres explicando la decisión]

Ejemplo de respuesta:
PORCENTAJE: 90
RECOMENDACION: validado
RAZON: Número de operación y monto coinciden con transacción confirmada. Fecha difiere 3 días pero es aceptable.
PROMPT;
    }

    /**
     * Parsear la respuesta de Groq
     */
    private function parsearRespuesta($respuesta)
    {
        // Valores por defecto
        $porcentaje = 50;
        $recomendacion = 'pendiente';
        $razon = 'No se pudo analizar';

        // Extraer PORCENTAJE
        if (preg_match('/PORCENTAJE:\s*(\d+)/i', $respuesta, $matches)) {
            $porcentaje = intval($matches[1]);
            $porcentaje = max(0, min(100, $porcentaje)); // Limitar entre 0-100
        }

        // Extraer RECOMENDACION
        if (preg_match('/RECOMENDACION:\s*(validado|rechazado)/i', $respuesta, $matches)) {
            $recomendacion = strtolower($matches[1]);
        }

        // Extraer RAZON (permite hasta 200 caracteres)
        if (preg_match('/RAZON:\s*(.+?)(?:\n|$)/is', $respuesta, $matches)) {
            $razon = trim($matches[1]);
            // Limitar a 200 caracteres si es muy larga
            if (strlen($razon) > 200) {
                $razon = substr($razon, 0, 197) . '...';
            }
        }

        return [
            'porcentaje' => $porcentaje,
            'recomendacion' => $recomendacion,
            'razon' => $razon
        ];
    }
}
