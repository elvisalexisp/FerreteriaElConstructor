function agregarCarrito(idProducto) {
  let carrito = JSON.parse(localStorage.getItem("carrito")) || [];
  let item = carrito.find((p) => p.id === idProducto);
  if (item) {
    item.cantidad += 1;
  } else {
    carrito.push({ id: idProducto, cantidad: 1 });
  }
  localStorage.setItem("carrito", JSON.stringify(carrito));
  alert("✅ Producto agregado al carrito con éxito");
}

function abrirModal(id, nombre) {
  document.getElementById("edit_id").value = id;
  document.getElementById("edit_nombre").value = nombre;

  document.getElementById("modalEditar").style.display = "flex";
}

function cerrarModal() {
  document.getElementById("modalEditar").style.display = "none";
}

window.onclick = function (event) {
  let modal = document.getElementById("modalEditar");
  if (event.target === modal) {
    cerrarModal();
  }
};
