<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userForm = $_POST["usuario"];
    $passForm = $_POST["password"];
    $archivo = __DIR__ . "/../data.json"; 

    if (file_exists($archivo)) {
        $usuarios = json_decode(file_get_contents($archivo), true) ?? [];

        foreach ($usuarios as $usuario) {
            if (($usuario["nombre"] === $userForm || $usuario["email"] === $userForm) && 
                password_verify($passForm, $usuario["password"])) {
                
                $_SESSION["usuario_logueado"] = $usuario["nombre"];
                $_SESSION["rol"] = $usuario["rol"] ?? "usuario";

                if ($_SESSION["rol"] === "admin" || $_SESSION["rol"] === "Administrador") {
                    header("Location: /index_ajax.html");
                } else {
                    header("Location: /SociogramaDAW/index.php");
                }
                exit;
            }
        }
    }
    echo "<p style='color:red'>Usuario o contraseña incorrectos.</p>";
}
?>

<form method="POST">
    <label>Usuario o Email:</label>
    <input type="text" name="usuario" required>
    <br><br>
    <label>Contraseña:</label>
    <input type="password" name="password" required>
    <br><br>
    <button type="submit">Entrar</button>
</form>

<style>
    form {
        max-width: 300px;
        margin: auto;
        padding: 1em;
        border: 1px solid #CCC;
        border-radius: 1em;
    }
    label {
        margin-top: 1em;
        display: block;
    }
    input {
        width: 100%;
        padding: .5em;
        box-sizing: border-box;
    }
    button {
        margin-top: 1em;
        padding: 0.7em;
        width: 100%;
        background-color: #0c2ef0ff;
        color: white;
        border: none;
        border-radius: 0.3em;
        cursor: pointer;
    }
    button:hover {
        background-color: #0c2ff585;
    }
</style>