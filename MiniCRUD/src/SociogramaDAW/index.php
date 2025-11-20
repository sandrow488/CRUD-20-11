<?php
require_once 'includes/functions.php';
include 'includes/header.php';
$old_field = $old_field ?? [];
$errors = $errors ?? [];

$old_herramientas = old_field('herramientas', $old_field);
if (!is_array($old_herramientas)) {
    $old_herramientas = [];
}
?>

<h1>Formulario de Sociograma</h1>

<a href="/public/logout.php">Cerrar sesión</a>
<form method="POST" action="process.php" novalidate>
   <h2>1. Identificación y Puesto</h2>

    <label for="nombre">Tu nombre:</label>
    <input type="text" id="nombre" name="nombre" value="<?= old_field('nombre', $old_field) ?>" minlenghth="2" required>
    <?= field_error('nombre', $errors) ?>

    <label for="email">Tu email:</label>
    <input type="email" id="email" name="email" value="<?= old_field('email', $old_field) ?>" required>
    <?= field_error('email', $errors) ?>

    <label for="puesto">Puesto actual:</label>
    <input type="text" id="puesto" name="puesto" value="<?= old_field('puesto', $old_field) ?>" required>
    <?= field_error('puesto', $errors) ?>

    <label for="departamento">Departamento:</label>
    <select id="departamento" name="departamento" required>
        <option value="" <?= old_field('departamento', $old_field) == '' ? 'selected' : '' ?>>-- Selecciona --</option>
        <option value="Desarrollo" <?= old_field('departamento', $old_field) == 'Desarrollo' ? 'selected' : '' ?>>Desarrollo</option>
        <option value="Sistemas" <?= old_field('departamento', $old_field) == 'Sistemas' ? 'selected' : '' ?>>Sistemas</option>
        <option value="Marketing" <?= old_field('departamento', $old_field) == 'Marketing' ? 'selected' : '' ?>>Marketing</option>
        <option value="Otro" <?= old_field('departamento', $old_field) == 'Otro' ? 'selected' : '' ?>>Otro</option>
    </select>
    <?= field_error('departamento', $errors) ?>

    <label for="fecha_respuesta">Fecha de hoy:</label>
    <input type="date" id="fecha_respuesta" name="fecha_respuesta" value="<?= old_field('fecha_respuesta', $old_field) ?>" required>
    <?= field_error('fecha_respuesta', $errors) ?>


    <h2>2. Preferencias de Colaboración</h2>

    <label for="positivo">¿Con quién te gusta trabajar?</label>
    <input type="text" id="positivo" name="positivo" value="<?= old_field('positivo', $old_field) ?>" required>
    <?= field_error('positivo', $errors) ?>

    <label for="negativo">¿Con quién prefieres no trabajar?</label>
    <input type="text" id="negativo" name="negativo" value="<?= old_field('negativo', $old_field) ?>" required>
    <?= field_error('negativo', $errors) ?>

    <label for="motivo">Motivo (opcional):</label>
    <textarea id="motivo" name="motivo"><?= old_field('motivo', $old_field) ?></textarea>
    <?= field_error('motivo', $errors) ?>


    <h2>3. Habilidades y Preferencias</h2>

    <label for="fortalezas">Tus fortalezas principales:</label>
    <textarea id="fortalezas" name="fortalezas"><?= old_field('fortalezas', $old_field) ?></textarea>
    <?= field_error('fortalezas', $errors) ?>

    <label for="debilidades">Áreas a mejorar (debilidades):</label>
    <textarea id="debilidades" name="debilidades"><?= old_field('debilidades', $old_field) ?></textarea>
    <?= field_error('debilidades', $errors) ?>

    <label>Estilo de comunicación preferido:</label>
    <div>
        <input type="radio" id="com_email" name="estilo_comunicacion" value="Email" <?= old_field('estilo_comunicacion', $old_field) == 'Email' ? 'checked' : '' ?>>
        <label for="com_email">Email</label>
    </div>
    <div>
        <input type="radio" id="com_chat" name="estilo_comunicacion" value="Chat" <?= old_field('estilo_comunicacion', $old_field) == 'Chat' ? 'checked' : '' ?>>
        <label for="com_chat">Chat (Slack/Teams)</label>
    </div>
    <div>
        <input type="radio" id="com_reunion" name="estilo_comunicacion" value="Reunión" <?= old_field('estilo_comunicacion', $old_field) == 'Reunión' ? 'checked' : '' ?>>
        <label for="com_reunion">Reunión</label>
    </div>
    <?= field_error('estilo_comunicacion', $errors) ?>

    <label>Ambiente de trabajo ideal:</label>
    <div>
        <input type="radio" id="amb_silencio" name="ambiente_trabajo" value="Silencioso" <?= old_field('ambiente_trabajo', $old_field) == 'Silencioso' ? 'checked' : '' ?>>
        <label for="amb_silencio">Silencioso</label>
    </div>
    <div>
        <input type="radio" id="amb_colaborativo" name="ambiente_trabajo" value="Colaborativo" <?= old_field('ambiente_trabajo', $old_field) == 'Colaborativo' ? 'checked' : '' ?>>
        <label for="amb_colaborativo">Colaborativo (con música/ruido)</label>
    </div>
    <?= field_error('ambiente_trabajo', $errors) ?>

    <label for="horario_inicio">Horario preferido (Inicio):</label>
    <input type="time" id="horario_inicio" name="horario_preferido_inicio" value="<?= old_field('horario_preferido_inicio', $old_field) ?>">
    <?= field_error('horario_preferido_inicio', $errors) ?>

    <label for="horario_fin">Horario preferido (Fin):</label>
    <input type="time" id="horario_fin" name="horario_preferido_fin" value="<?= old_field('horario_preferido_fin', $old_field) ?>">
    <?= field_error('horario_preferido_fin', $errors) ?>


<h2>Herramientas o software favoritos:</h2>
    <label>¿Con qué herramientas has trabajado?</label> 
    <div>
        <input type="checkbox" id="git" name="herramientas[]" value="Git" <?= in_array('Git', $old_herramientas) ? 'checked' : '' ?>>
        <label for="git">Git</label>
    </div>
    <div>
        <input type="checkbox" id="docker" name="herramientas[]" value="Docker" <?= in_array('Docker', $old_herramientas) ? 'checked' : '' ?>>
        <label for="docker">Docker</label>
    </div>
    <div>
        <input type="checkbox" id="jira" name="herramientas[]" value="Jira" <?= in_array('Jira', $old_herramientas) ? 'checked' : '' ?>>
        <label for="jira">Jira</label>
    </div>
    <div>
        <input type="checkbox" id="trello" name="herramientas[]" value="Trello" <?= in_array('Trello', $old_herramientas) ? 'checked' : '' ?>>
        <label for="trello">Trello</label>
    </div>
    <div>
        <input type="checkbox" id="slack" name="herramientas[]" value="Slack" <?= in_array('Slack', $old_herramientas) ? 'checked' : '' ?>>
        <label for="slack">Slack</label>
    </div>
    <div>
        <input type="checkbox" id="notion" name="herramientas[]" value="Notion" <?= in_array('Notion', $old_herramientas) ? 'checked' : '' ?>>
        <label for="notion">Notion</label>
    </div>
    <br> <?= field_error('herramientas', $errors) ?>

    <label for="tipo_proyecto_favorito">Tipo de proyectos en los que prefieres participar:</label>
    <input type="text" id="tipo_proyecto_favorito" name="tipo_proyecto_favorito" value="<?= old_field('tipo_proyecto_favorito', $old_field) ?>">
    <?= field_error('tipo_proyecto_favorito', $errors) ?>

    <label for="experiencia_proyectos">Años de experiencia:</label>
    <input type="number" id="experiencia_proyectos" name="experiencia_proyectos" value="<?= old_field('experiencia_proyectos', $old_field) ?>" min="0" max="50">
    <?= field_error('experiencia_proyectos', $errors) ?>

    <h2>4. Motivación y Metas</h2>

    <label for="motivacion">¿Qué te motiva en el trabajo?</label>
    <textarea id="motivacion" name="motivacion"><?= old_field('motivacion', $old_field) ?></textarea>
    <?= field_error('motivacion', $errors) ?>

    <label for="frustracion">¿Qué te frustra o desmotiva?</label>
    <textarea id="frustracion" name="frustracion"><?= old_field('frustracion', $old_field) ?></textarea>
    <?= field_error('frustracion', $errors) ?>

    <label for="metas_corto_plazo">Metas a corto plazo:</label>
    <textarea id="metas_corto_plazo" name="metas_corto_plazo"><?= old_field('metas_corto_plazo', $old_field) ?></textarea>
    <?= field_error('metas_corto_plazo', $errors) ?>

    <label for="metas_largo_plazo">Metas a largo plazo:</label>
    <textarea id="metas_largo_plazo" name="metas_largo_plazo"><?= old_field('metas_largo_plazo', $old_field) ?></textarea>
    <?= field_error('metas_largo_plazo', $errors) ?>

    <h2>5. Opcional</h2>

    <label for="aficiones">Aficiones o hobbies (opcional):</label>
    <textarea id="aficiones" name="aficiones"><?= old_field('aficiones', $old_field) ?></textarea>
    <?= field_error('aficiones', $errors) ?>

    <label for="color_perfil">Elige un color para tu perfil:</label>
    <input type="color" id="color_perfil" name="color_perfil" value="<?= old_field('color_perfil', $old_field) ?? '#ffffff' ?>">
    <?= field_error('color_perfil', $errors) ?>

    <label for="comentario_adicional">Comentarios adicionales (opcional):</label>
    <textarea id="comentario_adicional" name="comentario_adicional"><?= old_field('comentario_adicional', $old_field) ?></textarea>
    <?= field_error('comentario_adicional', $errors) ?>

    
    <button type="submit">Enviar</button>
</form>

<?php

include 'includes/footer.php';
?>