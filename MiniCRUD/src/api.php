<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
function responder_json_exito(mixed $contenidoDatos = [], int $codigoHttp = 200): void
{
    http_response_code($codigoHttp);
    echo json_encode(
        ['ok' => true, 'data' => $contenidoDatos],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

function responder_json_error(string $mensajeError, int $codigoHttp = 400): void
{
    http_response_code($codigoHttp);
    echo json_encode(
        ['ok' => false, 'error' => $mensajeError],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}
$rutaArchivoDatosJson = __DIR__ . '/data.json';

if (!file_exists($rutaArchivoDatosJson)) {
    file_put_contents($rutaArchivoDatosJson, json_encode([]) . "\n");
}

$listaUsuarios = json_decode((string) file_get_contents($rutaArchivoDatosJson), true);
if (!is_array($listaUsuarios)) {
    $listaUsuarios = [];
}
$metodoHttpRecibido = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$accionSolicitada = $_GET['action'] ?? $_POST['action'] ?? 'list';
if ($metodoHttpRecibido === 'GET' && $accionSolicitada === 'list') {
    responder_json_exito($listaUsuarios); // 200 OK
}
if ($metodoHttpRecibido === 'POST' && $accionSolicitada === 'create') {
    $cuerpoBruto = (string) file_get_contents('php://input');
    $datosDecodificados = $cuerpoBruto !== '' ? (json_decode($cuerpoBruto, true) ?? []) : [];
    $nombreUsuarioNuevo = trim((string) ($datosDecodificados['nombre'] ?? $_POST['nombre'] ?? ''));
    $correoUsuarioNuevo = trim((string) ($datosDecodificados['email'] ?? $_POST['email'] ?? ''));
    $correoUsuarioNormalizado = mb_strtolower($correoUsuarioNuevo);
    $rolUsuarioNuevo = trim((string) ($datosDecodificados['rol'] ?? $_POST['rol'] ?? ''));
    $passwordUsuarioNuevo = trim((string) ($datosDecodificados['password'] ?? $_POST['password'] ?? ''));
    if ($nombreUsuarioNuevo === '' || $correoUsuarioNuevo === '') {
        responder_json_error('Los campos "nombre" y "email" son obligatorios.', 422);
    }
    if (!filter_var($correoUsuarioNuevo, FILTER_VALIDATE_EMAIL)) {
        responder_json_error('El campo "email" no tiene un formato válido.', 422);
    }
    if (mb_strlen($nombreUsuarioNuevo) > 60) {
        responder_json_error('El campo "nombre" excede los 60 caracteres.', 422);
    }
    if (mb_strlen($correoUsuarioNuevo) > 120) {
        responder_json_error('El campo "email" excede los 120 caracteres.', 422);
    }
    if (existeEmailDuplicado($listaUsuarios, $correoUsuarioNormalizado)) {
        responder_json_error('Ya existe un usuario con ese email.', 409);
    }
    if (mb_strlen($rolUsuarioNuevo) > 30) {
        responder_json_error('El campo "rol" excede los 30 caracteres.', 422);
    }
    if (mb_strlen($passwordUsuarioNuevo) > 255) {
        responder_json_error('El campo "password" excede los 255 caracteres.', 422);
    }
    if ($rolUsuarioNuevo === '') {
        $rolUsuarioNuevo = 'usuario'; // valor por defecto
    }
    // Agregamos y persistimos (guardamos el email normalizado)
    $listaUsuarios[] = [
        'nombre' => $nombreUsuarioNuevo,
        'email' => $correoUsuarioNormalizado,
        'password' => password_hash($passwordUsuarioNuevo, PASSWORD_DEFAULT),
        'rol' => $rolUsuarioNuevo,
    ];
    file_put_contents(
        $rutaArchivoDatosJson,
        json_encode($listaUsuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n"
    );
    responder_json_exito($listaUsuarios, 201);

}


// 5.5) ACTUALIZAR usuario: POST /api.php?action=update
// Body JSON esperado: { "index": 0, "nombre": "...", ... }
if ($metodoHttpRecibido === 'POST' && $accionSolicitada === 'update') {
    $cuerpoBruto = (string) file_get_contents('php://input');
    $datosDecodificados = $cuerpoBruto !== '' ? (json_decode($cuerpoBruto, true) ?? []) : [];

    $indice = $datosDecodificados['index'] ?? $_POST['index'] ?? null;
    
    // Validar índice
    if ($indice === null || !isset($listaUsuarios[$indice])) {
        responder_json_error('El usuario a editar no existe.', 404);
    }

    // Recoger datos nuevos
    $nombreNuevo = trim((string) ($datosDecodificados['nombre'] ?? ''));
    $emailNuevo = trim((string) ($datosDecodificados['email'] ?? ''));
    $rolNuevo = trim((string) ($datosDecodificados['rol'] ?? ''));
    $passwordNuevo = trim((string) ($datosDecodificados['password'] ?? ''));

    // Validaciones básicas (igual que en create)
    if ($nombreNuevo === '' || $emailNuevo === '') {
        responder_json_error('Nombre y Email son obligatorios.', 422);
    }

    // Actualizamos los campos
    $listaUsuarios[$indice]['nombre'] = $nombreNuevo;
    $listaUsuarios[$indice]['email'] = mb_strtolower($emailNuevo);
    $listaUsuarios[$indice]['rol'] = $rolNuevo ?: 'usuario';

    // Solo actualizamos la contraseña si el usuario escribió algo nuevo
    if ($passwordNuevo !== '') {
        $listaUsuarios[$indice]['password'] = password_hash($passwordNuevo, PASSWORD_DEFAULT);
    }

    // Guardar en disco
    file_put_contents(
        $rutaArchivoDatosJson,
        json_encode($listaUsuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n"
    );

    responder_json_exito($listaUsuarios); // 200 OK
}


// 6) ELIMINAR usuario: POST /api.php?action=delete
// Body JSON esperado: { "index": 0 }
// Nota: podríamos usar método DELETE; aquí lo simplificamos a POST.
if (
    ($metodoHttpRecibido === 'POST' || $metodoHttpRecibido === 'DELETE') && $accionSolicitada ===
    'delete'
) {
    // 6.1) Intentamos obtener el índice por distintos canales
    $indiceEnQuery = $_GET['index'] ?? null;
    if ($indiceEnQuery === null) {
        $cuerpoBruto = (string) file_get_contents('php://input');
        if ($cuerpoBruto !== '') {
            $datosDecodificados = json_decode($cuerpoBruto, true) ?? [];
            $indiceEnQuery = $datosDecodificados['index'] ?? null;
        } else {
            $indiceEnQuery = $_POST['index'] ?? null;
        }
    }
    // 6.2) Validaciones de existencia del parámetro
    if ($indiceEnQuery === null) {
        responder_json_error('Falta el parámetro "index" para eliminar.', 422);
    }
    $indiceUsuarioAEliminar = (int) $indiceEnQuery;
    if (!isset($listaUsuarios[$indiceUsuarioAEliminar])) {
        responder_json_error('El índice indicado no existe.', 404);
    }
    // 6.3) Eliminamos y reindexamos para mantener la continuidad
    unset($listaUsuarios[$indiceUsuarioAEliminar]);
    $listaUsuarios = array_values($listaUsuarios);
    // 6.4) Guardamos el nuevo estado en disco
    file_put_contents(
        $rutaArchivoDatosJson,
        json_encode($listaUsuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n"
    );
    // 6.5) Devolvemos el listado actualizado
    responder_json_exito($listaUsuarios); // 200 OK
}
// 7) Si llegamos aquí, la acción solicitada no está soportada
responder_json_error('Acción no soportada. Use list | create | delete', 400);
 
/**
 * Comprueba si ya existe un usuario con el email dado (comparación exacta).
 *
 * @param array $usuarios Lista actual en memoria.
 * @param string $emailNormalizado Email normalizado en minúsculas.
 */
function existeEmailDuplicado(array $usuarios, string $emailNormalizado): bool
{
    foreach ($usuarios as $u) {
        if (
            isset($u['email']) && is_string($u['email']) && mb_strtolower($u['email']) ===
            $emailNormalizado
        ) {
            return true;
        }
    }
    return false;
}
?>