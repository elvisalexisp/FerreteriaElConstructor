<?php
include_once __DIR__ . '/../../layouts/header_admin.php';
?>

<div id="toastNotificacion">
    <span>✅</span> <span id="toastTexto">Acción realizada con éxito</span>
</div>

<div class="admin-container">
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>📊 Centro Avanzado de Consultas y Reportes</h3>
            <div class="action-buttons">
                <button class="btn-action" style="background-color: #0d9488;" onclick="exportarCSV()">📥 Exportar
                    CSV</button>
                <button class="btn-action" style="background-color: #475569;" onclick="window.print()">🖨️
                    Imprimir</button>
            </div>
        </div>

        <div class="admin-card-body">
            <div class="query-filters-wrapper">
                <div class="filter-group">
                    <label for="filtroModulo" class="form-label">Módulo / Tabla a Consultar</label>
                    <select id="filtroModulo" class="form-input" onchange="cambiarModulo()">
                        <option value="productos">📦 Inventario de Productos</option>
                        <option value="stock_bajo">⚠️ Alertas de Bajo Stock (< 5)</option>
                        <option value="categorias">🏷️ Categorías</option>
                        <option value="usuarios">👥 Usuarios del Sistema</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="busquedaTexto" class="form-label">Búsqueda Rápida</label>
                    <input type="text" id="busquedaTexto" class="form-input"
                        placeholder="Escribe para filtrar resultados..." onkeyup="aplicarFiltrosLocales()">
                </div>

                <div class="filter-group" id="grupoFiltroSecundario">
                    <label for="filtroSecundario" class="form-label">Filtro Específico</label>
                    <select id="filtroSecundario" class="form-input" onchange="aplicarFiltrosLocales()">
                        <option value="">-- Todos --</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="filtroOrden" class="form-label">Ordenar por</label>
                    <select id="filtroOrden" class="form-input" onchange="aplicarFiltrosLocales()">
                        <option value="recientes">Más Recientes / ID Asc</option>
                        <option value="nombre_asc">Nombre (A - Z)</option>
                        <option value="nombre_desc">Nombre (Z - A)</option>
                        <option value="precio_desc">Mayor Precio / Stock / Total</option>
                        <option value="precio_asc">Menor Precio / Stock / Total</option>
                    </select>
                </div>
            </div>

            <div class="query-kpi-grid">
                <div class="kpi-card">
                    <span class="kpi-title">Registros Encontrados</span>
                    <h4 class="kpi-value" id="kpiTotalRegistros">0</h4>
                </div>
                <div class="kpi-card">
                    <span class="kpi-title">Valor Total del Inventario / Conteo</span>
                    <h4 class="kpi-value" id="kpiValorTotal">Q0.00</h4>
                </div>
                <div class="kpi-card">
                    <span class="kpi-title">Estado del Filtro</span>
                    <h4 class="kpi-value" id="kpiEstadoFiltro" style="font-size: 1rem; color: #0284c7;">Mostrando todo
                    </h4>
                </div>
            </div>

            <div class="table-responsive">
                <table class="admin-table" id="tablaConsultas">
                    <thead>
                        <tr id="encabezadosTabla">
                        </tr>
                    </thead>
                    <tbody id="cuerpoTablaConsultas">
                        <tr>
                            <td colspan="6" class="text-center py-4">Seleccione un módulo o espere mientras cargan los
                                datos...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    const baseUrl="<?php echo $base_url; ?>";
    let datosOriginales=[];
    let datosFiltrados=[];

    document.addEventListener("DOMContentLoaded",function() {
        cambiarModulo();
    });

    function mostrarMensaje(mensaje) {
        const toast=document.getElementById('toastNotificacion');
        document.getElementById('toastTexto').innerText=mensaje;
        toast.classList.add('mostrar');
        setTimeout(() => toast.classList.remove('mostrar'),3000);
    }

    function cambiarModulo() {
        const modulo=document.getElementById('filtroModulo').value;
        document.getElementById('busquedaTexto').value='';

        let endpoint='';
        if(modulo==='productos'||modulo==='stock_bajo') endpoint='api/productos.php';
        else if(modulo==='categorias') endpoint='api/categorias.php';
        else if(modulo==='usuarios') endpoint='api/usuarios.php';
        else if(modulo==='pedidos') endpoint='api/pedidos.php'; // NUEVO ENDPOINT AÑADIDO

        document.getElementById('cuerpoTablaConsultas').innerHTML=`<tr><td colspan="6" class="text-center py-4">Cargando datos del servidor...</td></tr>`;

        fetch(baseUrl+endpoint)
            .then(res => res.json())
            .then(data => {
                if(!Array.isArray(data)) data=[];

                if(modulo==='stock_bajo') {
                    datosOriginales=data.filter(item => (parseInt(item.stock??item.cantidad??0)<5));
                } else {
                    datosOriginales=data;
                }

                poblarFiltroSecundario(modulo,data);
                aplicarFiltrosLocales();
            })
            .catch(err => {
                console.error("Error al obtener datos:",err);
                document.getElementById('cuerpoTablaConsultas').innerHTML=`<tr><td colspan="6" class="text-center text-danger py-4">Error al conectar con la API correspondiente.</td></tr>`;
            });
    }

    function poblarFiltroSecundario(modulo,data) {
        const selectSecundario=document.getElementById('filtroSecundario');
        selectSecundario.innerHTML='<option value="">-- Todos --</option>';

        if(modulo==='productos'||modulo==='stock_bajo') {
            const categorias=[...new Set(data.map(item => item.categoria??item.nombre_categoria??'General'))];
            categorias.forEach(cat => {
                if(cat) selectSecundario.innerHTML+=`<option value="${cat}">${cat}</option>`;
            });
        } else if(modulo==='usuarios') {
            selectSecundario.innerHTML+=`<option value="admin">Administradores</option><option value="cliente">Clientes</option>`;
        } else if(modulo==='pedidos') {
            const estados=[...new Set(data.map(item => item.estado??'Pendiente'))];
            estados.forEach(est => {
                if(est) selectSecundario.innerHTML+=`<option value="${est}">${est.charAt(0).toUpperCase()+est.slice(1)}</option>`;
            });
        } else {
            selectSecundario.innerHTML+=`<option value="con_descripcion">Con Descripción</option>`;
        }
    }

    function aplicarFiltrosLocales() {
        const modulo=document.getElementById('filtroModulo').value;
        const textoBusqueda=document.getElementById('busquedaTexto').value.toLowerCase();
        const filtroSec=document.getElementById('filtroSecundario').value;
        const orden=document.getElementById('filtroOrden').value;

        datosFiltrados=datosOriginales.filter(item => {
            let cumpleTexto=false;
            for(let key in item) {
                if(item[key]&&String(item[key]).toLowerCase().includes(textoBusqueda)) {
                    cumpleTexto=true;
                    break;
                }
            }

            let cumpleSecundario=true;
            if(filtroSec) {
                if(modulo==='productos'||modulo==='stock_bajo') {
                    const catItem=item.categoria??item.nombre_categoria??'General';
                    cumpleSecundario=(catItem===filtroSec);
                } else if(modulo==='usuarios') {
                    cumpleSecundario=(item.rol===filtroSec);
                } else if(modulo==='pedidos') {
                    const estItem=item.estado??'Pendiente';
                    cumpleSecundario=(estItem.toLowerCase()===filtroSec.toLowerCase());
                } else if(modulo==='categorias') {
                    if(filtroSec==='con_descripcion') cumpleSecundario=(item.descripcion&&item.descripcion.trim()!=='');
                }
            }

            return cumpleTexto&&cumpleSecundario;
        });

        datosFiltrados.sort((a,b) => {
            if(orden==='nombre_asc') {
                let nameA=(a.nombre??a.nombre_cat??a.titulo??a.cliente_nombre??'').toLowerCase();
                let nameB=(b.nombre??b.nombre_cat??b.titulo??b.cliente_nombre??'').toLowerCase();
                return nameA.localeCompare(nameB);
            }
            if(orden==='nombre_desc') {
                let nameA=(a.nombre??a.nombre_cat??a.titulo??a.cliente_nombre??'').toLowerCase();
                let nameB=(b.nombre??b.nombre_cat??b.titulo??b.cliente_nombre??'').toLowerCase();
                return nameB.localeCompare(nameA);
            }
            if(orden==='precio_desc') {
                let valA=parseFloat(a.precio??a.stock??a.total??a.id_categoria??a.id_usuario??0);
                let valB=parseFloat(b.precio??b.stock??b.total??b.id_categoria??b.id_usuario??0);
                return valB-valA;
            }
            if(orden==='precio_asc') {
                let valA=parseFloat(a.precio??a.stock??a.total??a.id_categoria??a.id_usuario??0);
                let valB=parseFloat(b.precio??b.stock??b.total??b.id_categoria??b.id_usuario??0);
                return valA-valB;
            }
            let idA=parseInt(a.id_producto??a.id_categoria??a.id_usuario??a.id_pedido??a.id??0);
            let idB=parseInt(b.id_producto??b.id_categoria??b.id_usuario??b.id_pedido??b.id??0);
            return idA-idB;
        });

        renderizarTablaYMetricas(modulo);
    }

    function renderizarTablaYMetricas(modulo) {
        const thead=document.getElementById('encabezadosTabla');
        const tbody=document.getElementById('cuerpoTablaConsultas');

        let htmlHead='';
        let htmlRows='';
        let acumuladorValor=0;

        if(modulo==='productos'||modulo==='stock_bajo') {
            htmlHead=`<th>ID</th><th>Imagen</th><th>Nombre del Producto</th><th>Categoría</th><th>Precio</th><th>Stock</th>`;

            if(datosFiltrados.length===0) {
                htmlRows=`<tr><td colspan="6" class="text-center py-4">No se encontraron productos con estos criterios.</td></tr>`;
            } else {
                datosFiltrados.forEach(p => {
                    const id=p.id_producto??p.id??'';
                    const nombre=p.nombre??'Sin nombre';
                    const cat=p.categoria??p.nombre_categoria??'General';
                    const precio=parseFloat(p.precio??0);
                    const stock=parseInt(p.stock??p.cantidad??0);

                    let rutaImg=p.imagen? p.imagen.trim():'';
                    if(rutaImg.startsWith('/')) {
                        rutaImg=rutaImg.substring(1);
                    }
                    if(rutaImg&&!rutaImg.includes('assets/img/productos/')) {
                        rutaImg='assets/img/productos/'+rutaImg;
                    }
                    const img=rutaImg? baseUrl+rutaImg:'https://placehold.co/45x45?text=Img';

                    acumuladorValor+=(precio*stock);

                    htmlRows+=`
                        <tr>
                            <td>${id}</td>
                            <td><img src="${img}" class="admin-product-img" alt="Prod" onerror="this.src='https://placehold.co/45x45?text=Error'"></td>
                            <td style="font-weight: 500; color: #1e293b;">${nombre}</td>
                            <td><span style="background-color: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 12px;">${cat}</span></td>
                            <td style="font-weight: 600; color: #059669;">Q ${precio.toFixed(2)}</td>
                            <td><span class="badge-stock">${stock} un.</span></td>
                        </tr>
                    `;
                });
            }
            document.getElementById('kpiValorTotal').innerText=`Q ${acumuladorValor.toFixed(2)}`;

        } else if(modulo==='categorias') {
            htmlHead=`<th>ID</th><th>Nombre de Categoría</th><th>Descripción</th>`;

            if(datosFiltrados.length===0) {
                htmlRows=`<tr><td colspan="3" class="text-center py-4">No se encontraron categorías.</td></tr>`;
            } else {
                datosFiltrados.forEach(c => {
                    const id=c.id_categoria??c.id??'';
                    const nombre=c.nombre??'Sin nombre';
                    const desc=c.descripcion??'<span style="color: #94a3b8;">Sin descripción</span>';

                    htmlRows+=`
                        <tr>
                            <td>${id}</td>
                            <td style="font-weight: 500; color: #1e293b;">${nombre}</td>
                            <td>${desc}</td>
                        </tr>
                    `;
                });
            }
            document.getElementById('kpiValorTotal').innerText=`${datosFiltrados.length} Registros`;

        } else if(modulo==='usuarios') {
            htmlHead=`<th>ID</th><th>Nombre Completo</th><th>Correo Electrónico</th><th>Rol</th>`;

            if(datosFiltrados.length===0) {
                htmlRows=`<tr><td colspan="4" class="text-center py-4">No se encontraron usuarios.</td></tr>`;
            } else {
                datosFiltrados.forEach(u => {
                    const id=u.id_usuario??u.id??'';
                    const nombre=u.nombre??'Sin nombre';
                    const correo=u.correo??u.email??'Sin correo';
                    const rol=u.rol??'cliente';
                    const rolBadge=rol==='admin'
                        ? `<span style="background-color: #e0f2fe; color: #0369a1; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Administrador</span>`
                        :`<span style="background-color: #f1f5f9; color: #475569; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">Cliente</span>`;

                    htmlRows+=`
                        <tr>
                            <td>${id}</td>
                            <td style="font-weight: 500; color: #1e293b;">${nombre}</td>
                            <td>${correo}</td>
                            <td>${rolBadge}</td>
                        </tr>
                    `;
                });
            }
            document.getElementById('kpiValorTotal').innerText=`${datosFiltrados.length} Usuarios`;

        } else if(modulo==='pedidos') {
            htmlHead=`<th>ID Pedido</th><th>Cliente</th><th>Fecha</th><th>Dirección</th><th>Total</th><th>Estado</th>`;

            if(datosFiltrados.length===0) {
                htmlRows=`<tr><td colspan="6" class="text-center py-4">No se encontraron pedidos registrados.</td></tr>`;
            } else {
                datosFiltrados.forEach(ped => {
                    const id=ped.id_pedido??ped.id??'';
                    const cliente=ped.cliente_nombre??'Cliente #'+(ped.id_usuario??'N/D');
                    const fecha=ped.fecha??'';
                    const direccion=ped.direccion_envio??'Sin especificar';
                    const total=parseFloat(ped.total??0);
                    const estado=ped.estado??'Pendiente';

                    acumuladorValor+=total;

                    htmlRows+=`
                        <tr>
                            <td style="font-weight: bold; color: #0284c7;">#${id}</td>
                            <td style="font-weight: 500; color: #1e293b;">${cliente}</td>
                            <td>${fecha}</td>
                            <td style="font-size: 0.85rem; color: #475569;">${direccion}</td>
                            <td style="font-weight: 600; color: #059669;">Q ${total.toFixed(2)}</td>
                            <td><span style="background-color: #fef3c7; color: #d97706; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">${estado}</span></td>
                        </tr>
                    `;
                });
            }
            document.getElementById('kpiValorTotal').innerText=`Q ${acumuladorValor.toFixed(2)}`;
        }

        thead.innerHTML=htmlHead;
        tbody.innerHTML=htmlRows;
        document.getElementById('kpiTotalRegistros').innerText=datosFiltrados.length;
        document.getElementById('kpiEstadoFiltro').innerText=(datosFiltrados.length===datosOriginales.length)? "Mostrando todos":"Filtrado activo";
    }

    function exportarCSV() {
        if(datosFiltrados.length===0) {
            alert("No hay datos para exportar con los filtros actuales.");
            return;
        }

        let csvContent="data:text/csv;charset=utf-8,";
        const keys=Object.keys(datosFiltrados[0]);
        csvContent+=keys.join(",")+"\r\n";

        datosFiltrados.forEach(row => {
            let values=keys.map(k => {
                let val=row[k]!==null&&row[k]!==undefined? String(row[k]):"";
                return `"${val.replace(/"/g,'""')}"`;
            });
            csvContent+=values.join(",")+"\r\n";
        });

        const encodedUri=encodeURI(csvContent);
        const link=document.createElement("a");
        link.setAttribute("href",encodedUri);
        link.setAttribute("download",`reporte_${document.getElementById('filtroModulo').value}_${Date.now()}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        mostrarMensaje("Reporte CSV exportado correctamente 📥");
    }
</script>

<?php
include __DIR__ . '/../../layouts/footer.php';
?>