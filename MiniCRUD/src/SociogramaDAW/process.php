<?php
require_once 'includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
header('Location: index.php');
exit;
}

$input = [
// Identificación
    'nombre' => trim($_POST['nombre'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'puesto' => trim($_POST['puesto'] ?? ''),
    'departamento' => trim($_POST['departamento'] ?? ''),
    'fecha_respuesta' => trim($_POST['fecha_respuesta'] ?? ''),
    
    // Sociograma
    'positivo' => trim($_POST['positivo'] ?? ''),
    'negativo' => trim($_POST['negativo'] ?? ''),
    'motivo' => trim($_POST['motivo'] ?? ''),

    // Habilidades y preferencias
    'fortalezas' => trim($_POST['fortalezas'] ?? ''),
    'debilidades' => trim($_POST['debilidades'] ?? ''),
    'estilo_comunicacion' => trim($_POST['estilo_comunicacion'] ?? ''),
    'ambiente_trabajo' => trim($_POST['ambiente_trabajo'] ?? ''),
    'horario_preferido_inicio' => trim($_POST['horario_preferido_inicio'] ?? ''),
    'horario_preferido_fin' => trim($_POST['horario_preferido_fin'] ?? ''),
    'herramientas' => $_POST['herramientas'] ?? [], 
    'tipo_proyecto_favorito' => trim($_POST['tipo_proyecto_favorito'] ?? ''),
    'experiencia_proyectos' => trim($_POST['experiencia_proyectos'] ?? ''),

    // Motivación y Metas
    'motivacion' => trim($_POST['motivacion'] ?? ''),
    'frustracion' => trim($_POST['frustracion'] ?? ''),
    'metas_corto_plazo' => trim($_POST['metas_corto_plazo'] ?? ''),
    'metas_largo_plazo' => trim($_POST['metas_largo_plazo'] ?? ''),

    // Opcional
    'aficiones' => trim($_POST['aficiones'] ?? ''),
    'color_perfil' => trim($_POST['color_perfil'] ?? ''),
    'comentario_adicional' => trim($_POST['comentario_adicional'] ?? '')
];


$errors = [];


$allowed_departamentos = ['Desarrollo', 'Sistemas', 'Marketing', 'Otro'];
$allowed_comunicacion = ['Email', 'Chat', 'Reunión'];
$allowed_ambiente = ['Silencioso', 'Colaborativo'];


if (strlen($input['nombre']) < 2) {
    $errors['nombre'] = 'El nombre debe tener al menos 2 caracteres.';
}
if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Introduce un correo electrónico válido.';
}
if (strlen($input['puesto']) < 2) {
    $errors['puesto'] = 'Indica tu puesto de trabajo (mín. 2 caracteres).';
}

if (empty($input['departamento']) || !in_array($input['departamento'], $allowed_departamentos)) {
    $errors['departamento'] = 'Selecciona un departamento válido.';
}


$date = DateTime::createFromFormat('Y-m-d', $input['fecha_respuesta']);
if (empty($input['fecha_respuesta']) || !$date || $date->format('Y-m-d') !== $input['fecha_respuesta']) {
    $errors['fecha_respuesta'] = 'La fecha de respuesta es obligatoria y debe tener un formato válido (DD-MM-YYYY).';
} elseif ($date > new DateTime()) {
    $errors['fecha_respuesta'] = 'La fecha debe ser la fecha de hoy.';
}

if (strlen($input['positivo']) < 2) {
    $errors['positivo'] = 'Indica al menos una persona (mín. 2 caracteres).';
}
if (strlen($input['negativo']) < 2) {
    $errors['negativo'] = 'Indica al menos una persona (mín. 2 caracteres).';
}
if (empty($input['motivo']) || strlen($input['motivo']) > 500) { 
    $errors['motivo'] = 'El motivo es obligatorio y no puede exceder 500 caracteres.';
}

if (empty($input['fortalezas']) || strlen($input['fortalezas']) > 500) {
    $errors['fortalezas'] = 'Las fortalezas son obligatorias y no pueden exceder 500 caracteres.';
}
if (empty($input['debilidades']) || strlen($input['debilidades']) > 500) {
    $errors['debilidades'] = 'Las debilidades son obligatorias y no pueden exceder 500 caracteres.';
}
if (empty($input['estilo_comunicacion']) || !in_array($input['estilo_comunicacion'], $allowed_comunicacion)) {
    $errors['estilo_comunicacion'] = 'Selecciona un estilo de comunicación.';
}
if (empty($input['ambiente_trabajo']) || !in_array($input['ambiente_trabajo'], $allowed_ambiente)) {
    $errors['ambiente_trabajo'] = 'Selecciona un ambiente de trabajo.';
}
$inicio_time = null;
$fin_time = null;

if (empty($input['horario_preferido_inicio'])) {
    $errors['horario_preferido_inicio'] = 'Indica tu horario preferido de inicio.';
} 
if (empty($input['horario_preferido_fin'])) {
    $errors['horario_preferido_fin'] = 'Indica tu horario preferido de fin.';
} 


if ($inicio_time && $fin_time && $fin_time <= $inicio_time) {
    $errors['horario_preferido_fin'] = 'La hora de fin debe ser posterior a la hora de inicio.';
}


if (empty($input['herramientas'])) {
    $errors['herramientas'] = 'Debes seleccionar al menos una herramienta.';
}

if (empty($input['tipo_proyecto_favorito'])) {
    $errors['tipo_proyecto_favorito'] = 'El tipo de proyecto favorito es obligatorio.';
} elseif (strlen($input['tipo_proyecto_favorito']) > 100) {
    $errors['tipo_proyecto_favorito'] = 'El tipo de proyecto no puede exceder 100 caracteres.';
}

if ($input['experiencia_proyectos'] === '') {
     $errors['experiencia_proyectos'] = 'Los años de experiencia son obligatorios.';
} elseif (!is_numeric($input['experiencia_proyectos']) || (int)$input['experiencia_proyectos'] < 0 || (int)$input['experiencia_proyectos'] > 50) {
     $errors['experiencia_proyectos'] = 'Los años de experiencia deben ser un número entre 0 y 50.';
}

if (empty($input['motivacion'])) {
    $errors['motivacion'] = 'La motivación es obligatoria.';
} elseif (strlen($input['motivacion']) > 500)  {
    $errors['motivacion'] = 'La motivación no puede exceder 500 caracteres.';
}

if (empty($input['frustracion'])) {
    $errors['frustracion'] = 'La frustración es obligatoria.';
} elseif (strlen($input['frustracion']) > 500) {
    $errors['frustracion'] = 'La frustración no puede exceder 500 caracteres.';
}

if (empty($input['metas_corto_plazo'])) {
    $errors['metas_corto_plazo'] = 'Las metas a corto plazo son obligatorias.';
} elseif (strlen($input['metas_corto_plazo']) > 500) {
    $errors['metas_corto_plazo'] = 'Las metas a corto plazo no pueden exceder 500 caracteres.';
}

if (empty($input['metas_largo_plazo'])) {
    $errors['metas_largo_plazo'] = 'Las metas a largo plazo son obligatorias.';
} elseif (strlen($input['metas_largo_plazo']) > 500) {
    $errors['metas_largo_plazo'] = 'Las metas a largo plazo no pueden exceder 500 caracteres.';
}
if (empty($input['aficiones'])) {
    $errors['aficiones'] = 'Las aficiones son obligatorias.';
} elseif (strlen($input['aficiones']) > 500) {
    $errors['aficiones'] = 'Las aficiones no pueden exceder 500 caracteres.';
}
if (empty($input['color_perfil']) || !preg_match('/^#[0-9A-Fa-f]{6}$/i', $input['color_perfil'])) {
    $errors['color_perfil'] = 'Debes seleccionar un color de perfil válido (formato hex #RRGGBB).';
}
if (strlen($input['comentario_adicional']) > 100) {
    $errors['comentario_adicional'] = 'El comentario adicional no puede exceder 100 caracteres.';
}


if ($errors) {
    $old_field = $input;
    
    include 'includes/header.php';
    include 'index.php'; // Reutilizamos el index
    include 'includes/footer.php';
    exit; // Detenemos la ejecución
}


$file = 'data/sociograma.json';
$data = load_json($file);


$registro = $input;
$registro['fecha_guardado_servidor'] = date('Y-m-d H:i:s');

$data[] = $registro;


if (!save_json($file, $data)) {
    http_response_code(500);
    echo "Error interno del servidor: No se pudo guardar el archivo JSON.";
    exit;
}

include 'includes/header.php';
?>

<main class="container">
    <h2>¡Gracias, <?= htmlspecialchars($input['nombre']) ?>!</h2>
    <p>Tu respuesta se ha guardado correctamente.</p>

    <br>
    <p><a href="index.php">Volver al formulario</a></p>
    <p>Puedes ver todas las respuestas en JSON aquí: <a href="api.php" target="_blank">api.php</a></p>
</main>

