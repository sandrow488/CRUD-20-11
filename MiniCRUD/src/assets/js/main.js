// -----------------------------------------------------------------------------
// Mini CRUD AJAX — Lado cliente
// -----------------------------------------------------------------------------
const URL_API_SERVIDOR = '/public/api.php'; // Asegúrate de que la ruta sea correcta según tu estructura

const nodoCuerpoTablaUsuarios = document.getElementById('tbody');
const nodoFilaEstadoVacio = document.getElementById('fila-estado-vacio');
const formularioAltaUsuario = document.getElementById('formCreate');
const nodoZonaMensajesEstado = document.getElementById('msg');
const nodoBotonAgregarUsuario = document.getElementById('boton-agregar-usuario');
const nodoIndicadorCargando = document.getElementById('indicador-cargando');

// Variable para guardar los datos localmente y poder rellenar el formulario
let listaUsuariosCache = [];

// ... (Funciones auxiliares: mostrarMensajeDeEstado, activarEstadoCargando, etc. siguen igual) ...

function mostrarMensajeDeEstado(tipoEstado, textoMensaje) {
    nodoZonaMensajesEstado.className = tipoEstado;
    nodoZonaMensajesEstado.textContent = textoMensaje;
    if (tipoEstado !== '') {
        setTimeout(() => {
            nodoZonaMensajesEstado.className = '';
            nodoZonaMensajesEstado.textContent = '';
        }, 3000);
    }
}

function activarEstadoCargando() {
    if (nodoBotonAgregarUsuario) nodoBotonAgregarUsuario.disabled = true;
}
function desactivarEstadoCargando() {
    if (nodoBotonAgregarUsuario) nodoBotonAgregarUsuario.disabled = false;
}

function convertirATextoSeguro(entrada) {
    return String(entrada).replaceAll('<', '&lt;').replaceAll('>', '&gt;');
}

// -----------------------------------------------------------------------------
// BLOQUE: Renderizado (Con clases para distinguir botones)
// -----------------------------------------------------------------------------
function renderizarTablaDeUsuarios(arrayUsuarios) {
    listaUsuariosCache = arrayUsuarios || [];
    
    nodoCuerpoTablaUsuarios.innerHTML = '';

    if (Array.isArray(arrayUsuarios) && arrayUsuarios.length > 0) {
        if (nodoFilaEstadoVacio) nodoFilaEstadoVacio.hidden = true;
    } else {
        if (nodoFilaEstadoVacio) nodoFilaEstadoVacio.hidden = false;
        return;
    }

    arrayUsuarios.forEach((usuario, index) => {
        const nodoFila = document.createElement('tr');
        nodoFila.innerHTML = `
            <td>${index + 1}</td>
            <td>${convertirATextoSeguro(usuario?.nombre ?? '')}</td>
            <td>${convertirATextoSeguro(usuario?.email ?? '')}</td>
            <td>${convertirATextoSeguro(usuario?.rol ?? '')}</td>
            <td>
                <button type="button" class="btn-eliminar" data-posicion="${index}">Eliminar</button>
            </td> 
            <td>
                <button type="button" class="btn-editar" data-posicion="${index}">Editar</button>
            </td>
        `;
        nodoCuerpoTablaUsuarios.appendChild(nodoFila);
    });
}

// -----------------------------------------------------------------------------
// BLOQUE: Carga inicial
// -----------------------------------------------------------------------------
async function obtenerYMostrarListadoDeUsuarios() {
    try {
        const respuestaHttp = await fetch(`${URL_API_SERVIDOR}?action=list`);
        const cuerpoJson = await respuestaHttp.json();
        if (!cuerpoJson.ok) throw new Error(cuerpoJson.error);
        renderizarTablaDeUsuarios(cuerpoJson.data);
    } catch (error) {
        mostrarMensajeDeEstado('error', error.message);
    }
}

// -----------------------------------------------------------------------------
// BLOQUE: Alta de usuario (Siempre es CREATE)
// -----------------------------------------------------------------------------
formularioAltaUsuario?.addEventListener('submit', async (evento) => {
    evento.preventDefault();
    const datosFormulario = new FormData(formularioAltaUsuario);
    
    // Obtenemos los valores
    const datosUsuarioNuevo = {
        nombre: String(datosFormulario.get("nombre") || "").trim(),
        email: String(datosFormulario.get("email") || "").trim(),
        password: String(datosFormulario.get("password") || "").trim(),
        rol: String(datosFormulario.get("rol") || "").trim(),
    };

    if (!datosUsuarioNuevo.nombre || !datosUsuarioNuevo.email || !datosUsuarioNuevo.password) {
        mostrarMensajeDeEstado('error', 'Todos los campos son obligatorios.');
        return;
    }

    try {
        activarEstadoCargando();
        // Siempre usamos 'create' porque al editar ya habremos borrado el anterior
        const respuestaHttp = await fetch(`${URL_API_SERVIDOR}?action=create`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datosUsuarioNuevo),
        });

        const cuerpoJson = await respuestaHttp.json();
        if (!cuerpoJson.ok) throw new Error(cuerpoJson.error);

        renderizarTablaDeUsuarios(cuerpoJson.data);
        formularioAltaUsuario.reset();
        mostrarMensajeDeEstado('ok', 'Usuario agregado correctamente.');
    } catch (error) {
        mostrarMensajeDeEstado('error', error.message);
    } finally {
        desactivarEstadoCargando();
    }
});

// -----------------------------------------------------------------------------
// BLOQUE: Delegación de Eventos (Eliminar Y Editar)
// -----------------------------------------------------------------------------
nodoCuerpoTablaUsuarios?.addEventListener('click', async (evento) => {
    const elementoClick = evento.target;

    // A) LOGICA DE ELIMINAR (Borrador normal)
    if (elementoClick.closest('.btn-eliminar')) {
        const boton = elementoClick.closest('.btn-eliminar');
        const index = parseInt(boton.dataset.posicion, 10);
        
        if (!confirm('¿Deseas eliminar este usuario permanentemente?')) return;
        eliminarUsuarioServidor(index);
    }

    // B) LOGICA DE EDITAR ("Rescatar datos y borrar")
    else if (elementoClick.closest('.btn-editar')) {
        const boton = elementoClick.closest('.btn-editar');
        const index = parseInt(boton.dataset.posicion, 10);

        const usuarioAEditar = listaUsuariosCache[index];
        if (!usuarioAEditar) return;

        if (!confirm(`Se moverán los datos de "${usuarioAEditar.nombre}" al formulario y se borrará de la tabla para que lo crees de nuevo modificado. ¿Seguir?`)) {
            return;
        }

        const form = formularioAltaUsuario;
        if (form.elements['nombre']) form.elements['nombre'].value = usuarioAEditar.nombre;
        if (form.elements['email']) form.elements['email'].value = usuarioAEditar.email;
        if (form.elements['rol']) form.elements['rol'].value = usuarioAEditar.rol;
        if (form.elements['password']) form.elements['password'].value = ""; 

        await eliminarUsuarioServidor(index, true); 
   
        formularioAltaUsuario.scrollIntoView({ behavior: 'smooth' });
 
        if (form.elements['nombre']) form.elements['nombre'].focus();
    }
});

async function eliminarUsuarioServidor(index, esEdicion = false) {
    try {
        const respuestaHttp = await fetch(`${URL_API_SERVIDOR}?action=delete`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ index: index }),
        });
        const cuerpoJson = await respuestaHttp.json();
        if (!cuerpoJson.ok) throw new Error(cuerpoJson.error);

        renderizarTablaDeUsuarios(cuerpoJson.data);
        
        if (esEdicion) {
            mostrarMensajeDeEstado('ok', 'Datos cargados. Modifica y pulsa "Agregar usuario".');
        } else {
            mostrarMensajeDeEstado('ok', 'Usuario eliminado correctamente.');
        }

    } catch (error) {
        mostrarMensajeDeEstado('error', error.message);
    }
}

// Inicialización
obtenerYMostrarListadoDeUsuarios();