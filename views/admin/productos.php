<?php
include_once __DIR__ . '/../layouts/header_admin.php';
?>

<div id="toastNotificacion">
    <span>✅</span> <span id="toastTexto">Operación realizada con éxito</span>
</div>

<div class="admin-container">
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>🧱 Listado de Productos</h3>
            <button class="btn-primary-admin" onclick="abrirModalCrear()">+ Nuevo Producto</button>
        </div>
        <div class="admin-card-body">
            <div class="table-responsive">
                <table class="admin-table" id="tablaProductos">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Categoría</th> <!-- Columna de categoría agregada -->
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoTablaProductos">
                        <tr>
                            <td colspan="8" class="text-center py-3">Cargando productos desde la API...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="modalProducto" class="custom-modal-overlay" style="display: none;">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h5 class="modal-title" id="modalTitulo">✏️ Gestionar Producto</h5>
            <button type="button" class="btn-close-modal" onclick="cerrarModal()">&times;</button>
        </div>

        <form id="formProducto" onsubmit="guardarProducto(event)" enctype="multipart/form-data">
            <div class="custom-modal-body">
                <!-- ID Oculto -->
                <input type="hidden" id="edit_id" name="id">

                <div style="margin-bottom: 12px;">
                    <label for="edit_nombre" class="form-label fw-bold">Nombre del Producto</label>
                    <input type="text" class="form-control" id="edit_nombre" name="nombre"
                        style="width: 100%; padding: 7px; margin-top: 4px;" required>
                </div>

                <div style="margin-bottom: 12px;">
                    <label for="edit_descripcion" class="form-label fw-bold">Descripción</label>
                    <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="2"
                        style="width: 100%; padding: 7px; margin-top: 4px;"></textarea>
                </div>

                <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                    <div style="flex: 1;">
                        <label for="edit_precio" class="form-label fw-bold">Precio ($)</label>
                        <input type="number" step="0.01" class="form-control" id="edit_precio" name="precio"
                            style="width: 100%; padding: 7px; margin-top: 4px;" required>
                    </div>
                    <div style="flex: 1;">
                        <label for="edit_stock" class="form-label fw-bold">Stock / Existencia</label>
                        <input type="number" class="form-control" id="edit_stock" name="stock"
                            style="width: 100%; padding: 7px; margin-top: 4px;" required>
                    </div>
                </div>

                <div style="margin-bottom: 12px;">
                    <label for="edit_categoria" class="form-label fw-bold">Categoría</label>
                    <select class="form-control" id="edit_categoria" name="categoria_id"
                        style="width: 100%; padding: 7px; margin-top: 4px;" required>
                        <option value="">Cargando categorías...</option>
                    </select>
                </div>

                <div style="margin-bottom: 12px;">
                    <label for="edit_imagen" class="form-label fw-bold">Imagen del Producto</label>
                    <input type="file" class="form-control" id="edit_imagen" name="imagen" accept="image/*"
                        style="width: 100%; padding: 5px; margin-top: 4px;">
                    <small style="color: #666; font-size: 12px;" id="helpImagen">Sube una imagen para el
                        producto.</small>
                </div>
            </div>

            <div class="custom-modal-footer">
                <button type="button" class="btn btn-secondary" onclick="cerrarModal()"
                    style="padding: 8px 15px; cursor: pointer;">Cancelar</button>
                <button type="submit" class="btn btn-primary"
                    style="padding: 8px 15px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Guardar
                    Producto</button>
            </div>
        </form>
    </div>
</div>

<script>
    const baseUrl="<?php echo $base_url; ?>";
    let productosCargados=[];
    let listaCategorias=[];

    document.addEventListener("DOMContentLoaded",function() {
        cargarCategorias();
        cargarProductos();
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

    function cargarCategorias() {
        fetch(baseUrl+'api/categorias.php')
            .then(response => response.json())
            .then(data => {
                listaCategorias=data;
                const select=document.getElementById('edit_categoria');
                let optionsHtml='<option value="">Seleccione una categoría</option>';

                data.forEach(cat => {
                    const catId=cat.id_categoria??cat.id;
                    const catNombre=cat.nombre??cat.nombre_categoria??'Categoría';
                    optionsHtml+=`<option value="${catId}">${catNombre}</option>`;
                });

                select.innerHTML=optionsHtml;
            })
            .catch(error => {
                console.error('Error al cargar categorías:',error);
                document.getElementById('edit_categoria').innerHTML='<option value="">Error al cargar categorías</option>';
            });
    }

    function cargarProductos() {
        const tbody=document.getElementById('cuerpoTablaProductos');

        fetch(baseUrl+'api/productos.php')
            .then(response => response.json())
            .then(data => {
                if(!data||data.length===0) {
                    tbody.innerHTML=`<tr><td colspan="8" class="text-center py-3">No hay productos registrados.</td></tr>`;
                    return;
                }

                productosCargados=data;
                let htmlRows='';

                data.forEach(producto => {
                    const id=producto.id_producto??producto.id??'';
                    const nombre=producto.nombre??'Sin nombre';
                    const descripcion=producto.descripcion??'';
                    const precio=producto.precio??'0.00';
                    const stock=producto.cantidad??producto.stock??'0';

                    const nombreImagen=producto.imagen??'';
                    let imagenSrc=nombreImagen? baseUrl+'assets/img/productos/'+nombreImagen:baseUrl+'assets/img/default.png';

                    htmlRows+=`
                        <tr>
                            <td class="text-center">
                                <img src="${imagenSrc}" alt="${nombre}" class="admin-product-img" width="45" height="45" style="object-fit: cover;" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'45\' height=\'45\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23ccc\' stroke-width=\'2\'><rect x=\'3\' y=\'3\' width=\'18\' height=\'18\' rx=\'2\'/><circle cx=\'8.5\' cy=\'8.5\' r=\'1.5\'/><path d=\'21 15l-5-5L5 21\'/></svg>';">
                            </td>
                            <td>${id}</td>
                            <td class="fw-bold">${nombre}</td>
                            <td>${descripcion}</td>
                            <td><span class="badge bg-light text-dark">${categoriaNombre}</span></td>
                            <td>$${parseFloat(precio).toFixed(2)}</td>
                            <td><span class="badge-stock">${stock}</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-edit" onclick="editarProducto(${id})">Editar</button>
                                    <button class="btn-action btn-delete" onclick="eliminarProducto(${id})">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                tbody.innerHTML=htmlRows;
            })
            .catch(error => {
                console.error('Error al cargar la API:',error);
                tbody.innerHTML=`<tr><td colspan="8" class="text-center text-danger py-3">Error al conectar con la API de productos.</td></tr>`;
            });
    }

    function abrirModalCrear() {
        document.getElementById('formProducto').reset();
        document.getElementById('edit_id').value='';
        document.getElementById('modalTitulo').innerText='➕ Nuevo Producto';
        document.getElementById('helpImagen').innerText='Selecciona una imagen para el producto.';
        document.getElementById('modalProducto').style.display='flex';
    }

    function editarProducto(id) {
        const producto=productosCargados.find(p => (p.id_producto??p.id)==id);

        if(!producto) {
            alert("No se encontró la información del producto.");
            return;
        }

        document.getElementById('formProducto').reset();
        document.getElementById('edit_id').value=id;
        document.getElementById('edit_nombre').value=producto.nombre??'';
        document.getElementById('edit_descripcion').value=producto.descripcion??'';
        document.getElementById('edit_precio').value=producto.precio??'';
        document.getElementById('edit_stock').value=producto.cantidad??producto.stock??'';
        document.getElementById('edit_categoria').value=producto.id_categoria??producto.categoria_id??'';

        document.getElementById('modalTitulo').innerText='✏️ Editar Producto (ID: '+id+')';
        document.getElementById('helpImagen').innerText='Deja este campo vacío si no deseas cambiar la imagen actual.';

        document.getElementById('modalProducto').style.display='flex';
    }

    function cerrarModal() {
        document.getElementById('modalProducto').style.display='none';
    }

    function guardarProducto(e) {
        e.preventDefault();

        const id=document.getElementById('edit_id').value;
        const formData=new FormData(document.getElementById('formProducto'));
        let url=baseUrl+'api/productos.php';

        if(id) {
            formData.append('_method','PUT');
            formData.append('id',id);
        }

        fetch(url,{
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if(data.error) {
                    alert("Error: "+data.error);
                } else {
                    cerrarModal();
                    cargarProductos();
                    const mensajeExito=id? "¡Producto actualizado con éxito! ✨":"¡Producto agregado con éxito! 🎉";
                    mostrarMensajeExito(mensajeExito);
                }
            })
            .catch(error => {
                console.error('Error en la petición:',error);
                alert("Ocurrió un error al guardar el producto.");
            });
    }

    function eliminarProducto(id) {
        if(confirm("¿Estás seguro de eliminar el producto con ID "+id+"?")) {
            fetch(baseUrl+'api/productos.php',{
                method: 'DELETE',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id: id})
            })
                .then(response => response.json())
                .then(data => {
                    if(data.error) {
                        alert("Error: "+data.error);
                    } else {
                        cargarProductos();
                        mostrarMensajeExito("Producto eliminado correctamente 🗑️");
                    }
                })
                .catch(error => console.error('Error al eliminar:',error));
        }
    }

    window.onclick=function(event) {
        let modal=document.getElementById('modalProducto');
        if(event.target===modal) {
            cerrarModal();
        }
    }
</script>

<?php
include __DIR__ . '/../layouts/footer.php';
?>