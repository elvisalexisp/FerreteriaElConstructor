<?php
include_once __DIR__ . '/../layouts/header_admin.php';
?>

<div id="toastNotificacion">
    <span>✅</span> <span id="toastTexto">Operación realizada con éxito</span>
</div>

<div class="admin-container">
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>👥 Listado de Usuarios</h3>
            <button class="btn-primary-admin" onclick="abrirModalCrear()">+ Nuevo Usuario</button>
        </div>
        <div class="admin-card-body">
            <div class="table-responsive">
                <table class="admin-table" id="tablaUsuarios">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo Electrónico</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoTablaUsuarios">
                        <tr>
                            <td colspan="5" class="text-center py-3">Cargando usuarios desde la API...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="modalUsuario" class="custom-modal-overlay" style="display: none;">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h5 class="modal-title" id="modalTitulo">👤 Gestionar Usuario</h5>
            <button type="button" class="btn-close-modal" onclick="cerrarModal()">&times;</button>
        </div>

        <form id="formUsuario" onsubmit="guardarUsuario(event)">
            <div class="custom-modal-body">
                <!-- ID Oculto -->
                <input type="hidden" id="edit_id_usuario" name="id_usuario">

                <div class="form-group" style="margin-bottom: 12px;">
                    <label for="edit_nombre_usu" class="form-label">Nombre Completo</label>
                    <input type="text" class="form-input" id="edit_nombre_usu" name="nombre"
                        placeholder="Ej: Juan Pérez" required>
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label for="edit_correo_usu" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-input" id="edit_correo_usu" name="correo"
                        placeholder="correo@ejemplo.com" required>
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label for="edit_password_usu" class="form-label">Contraseña <small id="passwordHelp"
                            style="color: #64748b; font-weight: normal;">(Déjalo en blanco para mantener la actual al
                            editar)</small></label>
                    <input type="password" class="form-input" id="edit_password_usu" name="password"
                        placeholder="••••••••">
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label for="edit_rol_usu" class="form-label">Rol de Usuario</label>
                    <select class="form-input" id="edit_rol_usu" name="rol" required>
                        <option value="cliente">Cliente</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
            </div>

            <div class="custom-modal-footer">
                <button type="button" class="btn-action" style="background-color: #64748b;"
                    onclick="cerrarModal()">Cancelar</button>
                <button type="submit" class="btn-primary-admin">Guardar Usuario</button>
            </div>
        </form>
    </div>
</div>

<script>
    const baseUrl="<?php echo $base_url; ?>";
    let usuariosCargados=[];

    document.addEventListener("DOMContentLoaded",function() {
        cargarUsuariosAdmin();
    });

    function mostrarMensajeExito(mensaje) {
        const toast=document.getElementById('toastNotificacion');
        const texto=document.getElementById('toastTexto');
        texto.innerText=mensaje;

        toast.classList.add('mostrar');
        setTimeout(() => {
            toast.classList.remove('mostrar');
        },3500);
    }

    function cargarUsuariosAdmin() {
        const tbody=document.getElementById('cuerpoTablaUsuarios');

        fetch(baseUrl+'api/usuarios.php')
            .then(response => response.json())
            .then(data => {
                if(!data||data.length===0) {
                    tbody.innerHTML=`<tr><td colspan="5" class="text-center py-3">No hay usuarios registrados.</td></tr>`;
                    return;
                }

                usuariosCargados=data;
                let htmlRows='';

                data.forEach(usu => {
                    const id=usu.id_usuario??usu.id??'';
                    const nombre=usu.nombre??'Sin nombre';
                    const correo=usu.correo??usu.email??'Sin correo';
                    const rol=usu.rol??'cliente';

                    // Etiqueta visual para distinguir roles con tus estilos limpios
                    const rolBadge=rol==='admin'
                        ? `<span style="background-color: #e0f2fe; color: #0369a1; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">Administrador</span>`
                        :`<span style="background-color: #f1f5f9; color: #475569; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">Cliente</span>`;

                    htmlRows=htmlRows+`
                        <tr>
                            <td>${id}</td>
                            <td style="font-weight: 500; color: #1e293b;">${nombre}</td>
                            <td>${correo}</td>
                            <td>${rolBadge}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-edit" onclick="editarUsuario(${id})">Editar</button>
                                    <button class="btn-action btn-delete" onclick="eliminarUsuario(${id})">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                tbody.innerHTML=htmlRows;
            })
            .catch(error => {
                console.error('Error al cargar la API de usuarios:',error);
                tbody.innerHTML=`<tr><td colspan="5" class="text-center" style="color: #dc2626; padding: 15px;">Error al conectar con la API de usuarios.</td></tr>`;
            });
    }

    function abrirModalCrear() {
        document.getElementById('formUsuario').reset();
        document.getElementById('edit_id_usuario').value='';
        document.getElementById('modalTitulo').innerText='➕ Nuevo Usuario';
        document.getElementById('passwordHelp').style.display='none';
        document.getElementById('edit_password_usu').setAttribute('required','true');
        document.getElementById('modalUsuario').style.display='flex';
    }

    function editarUsuario(id) {
        const usu=usuariosCargados.find(u => (u.id_usuario??u.id)==id);

        if(!usu) {
            alert("No se encontró la información del usuario.");
            return;
        }

        document.getElementById('formUsuario').reset();
        document.getElementById('edit_id_usuario').value=id;
        document.getElementById('edit_nombre_usu').value=usu.nombre??'';
        document.getElementById('edit_correo_usu').value=usu.correo??usu.email??'';
        document.getElementById('edit_rol_usu').value=usu.rol??'cliente';

        // Al editar, la contraseña no es obligatoria (por si no se desea cambiar)
        document.getElementById('passwordHelp').style.display='inline';
        document.getElementById('edit_password_usu').removeAttribute('required');

        document.getElementById('modalTitulo').innerText='✏️ Editar Usuario (ID: '+id+')';
        document.getElementById('modalUsuario').style.display='flex';
    }

    function cerrarModal() {
        document.getElementById('modalUsuario').style.display='none';
    }

    function guardarUsuario(e) {
        e.preventDefault();

        const id=document.getElementById('edit_id_usuario').value;
        const nombre=document.getElementById('edit_nombre_usu').value;
        const correo=document.getElementById('edit_correo_usu').value;
        const password=document.getElementById('edit_password_usu').value;
        const rol=document.getElementById('edit_rol_usu').value;

        const datosEnvio={
            nombre: nombre,
            correo: correo,
            rol: rol
        };

        // Solo enviar la contraseña si fue escrita
        if(password) {
            datosEnvio.password=password;
        }

        let metodoHttp='POST';
        let url=baseUrl+'api/usuarios.php';

        if(id) {
            metodoHttp='PUT';
            datosEnvio.id_usuario=id;
        }

        fetch(url,{
            method: metodoHttp,
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(datosEnvio)
        })
            .then(response => response.json())
            .then(data => {
                if(data.error) {
                    alert("Error: "+data.error);
                } else {
                    cerrarModal();
                    cargarUsuariosAdmin();
                    const mensajeExito=id? "¡Usuario actualizado con éxito! ✨":"¡Usuario creado con éxito! 🎉";
                    mostrarMensajeExito(mensajeExito);
                }
            })
            .catch(error => {
                console.error('Error en la petición:',error);
                alert("Ocurrió un error al guardar el usuario.");
            });
    }

    function eliminarUsuario(id) {
        if(confirm("¿Estás seguro de eliminar al usuario con ID "+id+"?")) {
            fetch(baseUrl+'api/usuarios.php',{
                method: 'DELETE',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id_usuario: id})
            })
                .then(response => response.json())
                .then(data => {
                    if(data.error) {
                        alert("Error: "+data.error);
                    } else {
                        cargarUsuariosAdmin();
                        mostrarMensajeExito("Usuario eliminado correctamente 🗑️");
                    }
                })
                .catch(error => console.error('Error al eliminar:',error));
        }
    }

    window.onclick=function(event) {
        let modal=document.getElementById('modalUsuario');
        if(event.target===modal) {
            cerrarModal();
        }
    }
</script>

<?php
include __DIR__ . '/../layouts/footer.php';
?>