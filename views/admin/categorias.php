<?php
include_once __DIR__ . '/../layouts/header_admin.php';
?>

<div id="toastNotificacion">
    <span>✅</span> <span id="toastTexto">Operación realizada con éxito</span>
</div>

<div class="admin-container">
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>🏷️ Listado de Categorías</h3>
            <button class="btn-primary-admin" onclick="abrirModalCrear()">+ Nueva Categoría</button>
        </div>
        <div class="admin-card-body">
            <div class="table-responsive">
                <table class="admin-table" id="tablaCategorias">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoTablaCategorias">
                        <tr>
                            <td colspan="4" class="text-center py-3">Cargando categorías desde la API...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="modalCategoria" class="custom-modal-overlay" style="display: none;">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h5 class="modal-title" id="modalTitulo">✏️ Gestionar Categoría</h5>
            <button type="button" class="btn-close-modal" onclick="cerrarModal()">&times;</button>
        </div>

        <form id="formCategoria" onsubmit="guardarCategoria(event)">
            <div class="custom-modal-body">
                <input type="hidden" id="edit_id_categoria" name="id_categoria">

                <div class="form-group" style="margin-bottom: 12px;">
                    <label for="edit_nombre_cat" class="form-label">Nombre de la Categoría</label>
                    <input type="text" class="form-input" id="edit_nombre_cat" name="nombre"
                        placeholder="Ej: Herramientas Eléctricas" required>
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label for="edit_descripcion_cat" class="form-label">Descripción (Opcional)</label>
                    <textarea class="form-input" id="edit_descripcion_cat" name="descripcion" rows="3"
                        placeholder="Breve descripción..."></textarea>
                </div>
            </div>

            <div class="custom-modal-footer">
                <button type="button" class="btn-action" style="background-color: #64748b;"
                    onclick="cerrarModal()">Cancelar</button>
                <button type="submit" class="btn-primary-admin">Guardar Categoría</button>
            </div>
        </form>
    </div>
</div>

<script>
    const baseUrl="<?php echo $base_url; ?>";
    let categoriasCargadas=[];

    document.addEventListener("DOMContentLoaded",function() {
        cargarCategoriasAdmin();
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

    function cargarCategoriasAdmin() {
        const tbody=document.getElementById('cuerpoTablaCategorias');

        fetch(baseUrl+'api/categorias.php')
            .then(response => response.json())
            .then(data => {
                if(!data||data.length===0) {
                    tbody.innerHTML=`<tr><td colspan="4" class="text-center py-3">No hay categorías registradas.</td></tr>`;
                    return;
                }

                categoriasCargadas=data;
                let htmlRows='';

                data.forEach(cat => {
                    const id=cat.id_categoria??cat.id??'';
                    const nombre=cat.nombre??'Sin nombre';
                    const descripcion=cat.descripcion??'<span style="color: #94a3b8;">Sin descripción</span>';

                    htmlRows=htmlRows+`
                        <tr>
                            <td>${id}</td>
                            <td style="font-weight: 500; color: #1e293b;">${nombre}</td>
                            <td>${descripcion}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-edit" onclick="editarCategoria(${id})">Editar</button>
                                    <button class="btn-action btn-delete" onclick="eliminarCategoria(${id})">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                tbody.innerHTML=htmlRows;
            })
            .catch(error => {
                console.error('Error al cargar la API de categorías:',error);
                tbody.innerHTML=`<tr><td colspan="4" class="text-center" style="color: #dc2626; padding: 15px;">Error al conectar con la API de categorías.</td></tr>`;
            });
    }

    function abrirModalCrear() {
        document.getElementById('formCategoria').reset();
        document.getElementById('edit_id_categoria').value='';
        document.getElementById('modalTitulo').innerText='➕ Nueva Categoría';
        document.getElementById('modalCategoria').style.display='flex';
    }

    function editarCategoria(id) {
        const cat=categoriasCargadas.find(c => (c.id_categoria??c.id)==id);

        if(!cat) {
            alert("No se encontró la información de la categoría.");
            return;
        }

        document.getElementById('formCategoria').reset();
        document.getElementById('edit_id_categoria').value=id;
        document.getElementById('edit_nombre_cat').value=cat.nombre??'';
        document.getElementById('edit_descripcion_cat').value=cat.descripcion??'';

        document.getElementById('modalTitulo').innerText='✏️ Editar Categoría (ID: '+id+')';
        document.getElementById('modalCategoria').style.display='flex';
    }

    function cerrarModal() {
        document.getElementById('modalCategoria').style.display='none';
    }

    function guardarCategoria(e) {
        e.preventDefault();

        const id=document.getElementById('edit_id_categoria').value;
        const nombre=document.getElementById('edit_nombre_cat').value;
        const descripcion=document.getElementById('edit_descripcion_cat').value;

        const datosEnvio={
            nombre: nombre,
            descripcion: descripcion
        };

        let metodoHttp='POST';
        let url=baseUrl+'api/categorias.php';

        if(id) {
            metodoHttp='PUT';
            datosEnvio.id_categoria=id;
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
                    cargarCategoriasAdmin();
                    const mensajeExito=id? "¡Categoría actualizada con éxito! ✨":"¡Categoría creada con éxito! 🎉";
                    mostrarMensajeExito(mensajeExito);
                }
            })
            .catch(error => {
                console.error('Error en la petición:',error);
                alert("Ocurrió un error al guardar la categoría.");
            });
    }

    function eliminarCategoria(id) {
        if(confirm("¿Estás seguro de eliminar la categoría con ID "+id+"?")) {
            fetch(baseUrl+'api/categorias.php',{
                method: 'DELETE',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id_categoria: id})
            })
                .then(response => response.json())
                .then(data => {
                    if(data.error) {
                        alert("Error: "+data.error);
                    } else {
                        cargarCategoriasAdmin();
                        mostrarMensajeExito("Categoría eliminada correctamente 🗑️");
                    }
                })
                .catch(error => console.error('Error al eliminar:',error));
        }
    }

    window.onclick=function(event) {
        let modal=document.getElementById('modalCategoria');
        if(event.target===modal) {
            cerrarModal();
        }
    }
</script>

<?php
include __DIR__ . '/../layouts/footer.php';
?>