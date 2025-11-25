document.addEventListener('DOMContentLoaded', function() {

    const formulario = document.querySelector('form[action="process.php"]');
    const nombreInput = document.getElementById('nombre');
    const experienciaInput = document.getElementById('experiencia_proyectos');
    const errorNombre = document.getElementById('error-nombre-js');
    const errorExperiencia = document.getElementById('error-experiencia-js');


    formulario.addEventListener('submit', function(event) {

        let errores = [];
        errorNombre.textContent = '';
        errorExperiencia.textContent = '';

        if (nombreInput.value.trim().length < 2) {
            const msg = 'El nombre es obligatorio (mín. 2 letras).';
            errorNombre.textContent = msg;
            errores.push(msg);
        }

        const experienciaNum = parseInt(experienciaInput.value);

        if (isNaN(experienciaNum) || experienciaNum < 0 || experienciaNum > 50) {
            const msg = 'La experiencia debe ser un número entre 0 y 50.';
            errorExperiencia.textContent = msg;
            errores.push(msg);
        }

        if (errores.length > 0) {
            event.preventDefault();
            console.warn('Formulario frenado por errores JS:', errores);
        }
    });
});