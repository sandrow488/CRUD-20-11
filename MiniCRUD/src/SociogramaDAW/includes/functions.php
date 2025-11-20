<?php // Cargar un archivo JSON y devolverlo como array
function load_json($path)
{
if (!file_exists($path)) return [];
$raw = file_get_contents($path);
$data = json_decode($raw, true);
return is_array($data) ? $data : [];
}
// Guardar un array en un archivo JSON
function save_json($path, $data)
{
$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
return file_put_contents($path, $json) !== false;
}
// Valor anterior de un campo (rehidratación)
function old_field($name, $source = [])
{
return isset($source[$name]) ? $source[$name] : "";
}
// Error de un campo
function field_error($name, $errors = [])
{
return isset($errors[$name]) ? "<p style='color:red'>" . $errors[$name] .
"</p>" : "";
}
?>