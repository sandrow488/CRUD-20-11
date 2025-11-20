<?php
declare(strict_types=1);
header("Location: public/Login.php");
/**
* Esta página solo verifica que el servidor PHP 8.4 funciona.
* Más adelante, la Parte 1 mostrará el CRUD clásico (sin AJAX).
*/
?>
<!doctype html>
<html lang="es">
<head>
<style>
body {
    font-family: Arial, sans-serif;
    margin: 2em;
    background-color: #f9f9f9;
    color: #333;
}
h1 {
    color: #007BFF;
}
a {
    color: #007BFF;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
button {
    padding: 0.5em 1em;
    background-color: #007BFF;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
button:hover {
    background-color: #0056b3;
}

</style>
<meta charset="utf-8">
<title>Mini CRUD (Parte 1)</title>
</head>
    <body>
        <h1>Mini CRUD en JSON (sin Base de Datos) — Parte 1</h1>
        <p>Servidor PHP 8.4 funcionando dentro de Docker.</p>
        <p><a href="public/login.php">Iniciar Sesión</a></p>

        <a href="/public/logout.php">Cerrar sesión</a>
    </body>

    
</html>