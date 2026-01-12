<?php
/**
 * Test para verificar que las fotos aparecen correctamente en el header
 * Usuario: jeancito03
 * Password: jeanmarko
 * Debe mostrar la foto del usuario en el header
 */

// Configuración
$baseUrl = 'http://127.0.0.1:8000';
$username = 'jeancito03';
$password = 'jeanmarko';

// Inicializar cURL con cookies
$cookieFile = __DIR__ . '/cookies_test_foto.txt';
if (file_exists($cookieFile)) {
    unlink($cookieFile);
}

echo "=== TEST: Verificar foto en header ===\n\n";

// Paso 1: Obtener el formulario de login y el token CSRF
echo "1. Obteniendo formulario de login...\n";
$ch = curl_init($baseUrl . '/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$loginPage = curl_exec($ch);

if (!$loginPage) {
    die("Error: No se pudo cargar la página de login\n");
}

// Extraer token CSRF
preg_match('/<input type="hidden" name="_token" value="([^"]+)"/', $loginPage, $matches);
$csrfToken = $matches[1] ?? null;

if (!$csrfToken) {
    die("Error: No se pudo obtener el token CSRF\n");
}
echo "   Token CSRF obtenido: " . substr($csrfToken, 0, 10) . "...\n";

// Paso 2: Hacer login
echo "\n2. Intentando login con $username...\n";
$loginData = http_build_query([
    '_token' => $csrfToken,
    'username' => $username,
    'password' => $password
]);

curl_setopt($ch, CURLOPT_URL, $baseUrl . '/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $loginData);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode != 200) {
    die("Error: Login falló con código HTTP $httpCode\n");
}

echo "   Login exitoso!\n";

// Paso 3: Obtener la página principal (dashboard) y verificar la foto
echo "\n3. Obteniendo página principal del usuario familiar...\n";
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/familiar');
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_HEADER, false);
$dashboardPage = curl_exec($ch);

if (!$dashboardPage) {
    die("Error: No se pudo cargar la página principal\n");
}

// Verificar si hay foto en el header
echo "\n=== VERIFICACIÓN DE FOTO EN HEADER ===\n";

// Buscar la estructura de la foto
if (preg_match('/usuarios\/usuario_[^"\']+\.(jpg|png|jpeg)/i', $dashboardPage, $fotoMatch)) {
    echo "✓ ÉXITO: Se encontró una foto en el header\n";
    echo "   Archivo: " . $fotoMatch[0] . "\n";

    // Verificar que la foto esté dentro del elemento correcto
    if (preg_match('/<img[^>]+src="[^"]*storage\/' . preg_quote($fotoMatch[0], '/') . '"[^>]*>/i', $dashboardPage, $imgTag)) {
        echo "✓ Tag de imagen correcto encontrado\n";
        echo "   " . htmlspecialchars($imgTag[0]) . "\n";
    }

    // Verificar que NO aparezca el nombre junto a la foto
    if (preg_match('/<span class="text-theme-sm block font-medium">/i', $dashboardPage)) {
        // Extraer el contexto alrededor del span del nombre
        preg_match('/<span class="text-theme-sm block font-medium">.*?<\/span>/is', $dashboardPage, $nameSpan);
        if ($nameSpan) {
            echo "⚠ ADVERTENCIA: Se encontró un span de nombre en la página\n";
            echo "   Puede estar en dropdown o en otra parte\n";
        }
    }

} else {
    echo "✗ ERROR: NO se encontró foto en el header\n";
    echo "   Buscando el span del nombre...\n";

    if (preg_match('/<span class="text-theme-sm block font-medium">\s*([^<]+)\s*<\/span>/i', $dashboardPage, $nameMatch)) {
        echo "   Se encontró el nombre en lugar de la foto: " . trim($nameMatch[1]) . "\n";
    }
}

// Verificar la estructura del dropdown
echo "\n=== VERIFICACIÓN DEL DROPDOWN ===\n";
if (preg_match('/<a[^>]*class="[^"]*flex items-center gap-3[^"]*"[^>]*>(.*?)<\/a>/is', $dashboardPage, $dropdownLink)) {
    echo "✓ Dropdown link encontrado\n";

    // Buscar si hay img tag dentro
    if (strpos($dropdownLink[1], '<img') !== false) {
        echo "✓ Hay una imagen dentro del dropdown link\n";
    } else {
        echo "✗ No hay imagen dentro del dropdown link\n";
    }

    // Buscar si hay span con nombre
    if (preg_match('/<span class="text-theme-sm block font-medium">/i', $dropdownLink[1])) {
        echo "⚠ Hay un span de nombre en el dropdown link\n";
    }
}

// Paso 4: Verificar acceso directo a la imagen
echo "\n4. Verificando acceso a la foto en storage...\n";
if (isset($fotoMatch[0])) {
    $fotoUrl = $baseUrl . '/storage/' . $fotoMatch[0];
    $ch2 = curl_init($fotoUrl);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_HEADER, true);
    curl_setopt($ch2, CURLOPT_NOBODY, true);
    curl_exec($ch2);
    $httpCodeFoto = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
    curl_close($ch2);

    if ($httpCodeFoto == 200) {
        echo "✓ La foto es accesible en: $fotoUrl\n";
    } else {
        echo "✗ La foto NO es accesible (HTTP $httpCodeFoto): $fotoUrl\n";
    }
}

curl_close($ch);

echo "\n=== TEST COMPLETADO ===\n";

// Guardar el HTML para inspección
file_put_contents(__DIR__ . '/debug_header.html', $dashboardPage);
echo "HTML guardado en debug_header.html para inspección manual\n";
