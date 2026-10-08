/**
 * Script Principal - Ferretería El Constructor
 */

document.addEventListener("DOMContentLoaded", () => {
  // 1. Control del botón desplegable de categorías en el header
  const btnCategorias = document.getElementById("btnCategoriasToggle");

  if (btnCategorias) {
    let dropdownMenu = document.createElement("div");
    dropdownMenu.className = "categorias-dropdown-menu-flotante";

    const categorias = [
      { id: 1, nombre: "Herramientas Eléctricas", icono: "fa-bolt" },
      { id: 2, nombre: "Materiales de Construcción", icono: "fa-cubes" },
      { id: 3, nombre: "Plomería y Tubos", icono: "fa-faucet" },
      { id: 4, nombre: "Pinturas y Acabados", icono: "fa-paint-roller" },
      { id: 5, nombre: "Seguridad y EPP", icono: "fa-hard-hat" },
      { id: 6, nombre: "Jardinería y Exteriores", icono: "fa-seedling" },
      { id: 7, nombre: "Ferretería General", icono: "fa-tools" },
    ];

    let contenidoHTML = `<ul class="dropdown-lista-categorias">`;
    contenidoHTML += `<li><a href="index.php?vista=catalogo" class="dropdown-item-cat"><i class="fas fa-th-large"></i> Ver Todas</a></li>`;

    categorias.forEach((cat) => {
      contenidoHTML += `
        <li>
          <a href="index.php?vista=catalogo&categoria=${cat.id}" class="dropdown-item-cat">
            <i class="fas ${cat.icono}"></i> ${cat.nombre}
          </a>
        </li>
      `;
    });
    contenidoHTML += `</ul>`;

    dropdownMenu.innerHTML = contenidoHTML;

    const contenedorPadre = btnCategorias.parentElement;
    if (contenedorPadre) {
      contenedorPadre.classList.add("dropdown-contenedor-padre");
      contenedorPadre.appendChild(dropdownMenu);
    }

    btnCategorias.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdownMenu.classList.toggle("show");
    });

    document.addEventListener("click", () => {
      dropdownMenu.classList.remove("show");
    });

    dropdownMenu.addEventListener("click", (e) => {
      e.stopPropagation();
    });
  }

  // 2. Control de Carrusel
  const carruselSlides = document.querySelectorAll(".carrusel-slide");
  if (carruselSlides.length > 0) {
    let indiceActual = 0;
    setInterval(() => {
      carruselSlides[indiceActual].classList.remove("activo");
      indiceActual = (indiceActual + 1) % carruselSlides.length;
      carruselSlides[indiceActual].classList.add("activo");
    }, 5000);
  }

  // 3. Auto-cierre de alertas de éxito o error después de 4 segundos
  const alertas = document.querySelectorAll(".alert-success, .alert-error");
  if (alertas.length > 0) {
    setTimeout(() => {
      alertas.forEach((alerta) => {
        alerta.style.transition = "opacity 0.5s ease";
        alerta.style.opacity = "0";
        setTimeout(() => alerta.remove(), 500);
      });
    }, 4000);
  }

  // 4. Smart Header: Ocultar en scroll hacia abajo SOLO en el catálogo
  let lastScrollTop = 0;
  const header = document.querySelector(".cliente-header");
  const esCatalogo =
    window.location.search.includes("vista=catalogo") ||
    window.location.pathname.includes("catalogo");

  if (esCatalogo && header) {
    window.addEventListener(
      "scroll",
      function () {
        let scrollTop =
          window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop > lastScrollTop && scrollTop > 100) {
          header.classList.add("header-hidden");
        } else {
          header.classList.remove("header-hidden");
        }
        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
      },
      { passive: true },
    );
  }

  // 5. Control para contraer/expandir el Sidebar del Panel Admin (Escritorio)
  const btnToggleSidebar = document.getElementById("sidebarToggle");
  const adminWrapper = document.querySelector(".admin-wrapper");

  if (btnToggleSidebar && adminWrapper) {
    const sidebarState = localStorage.getItem("admin_sidebar_collapsed");
    if (sidebarState === "true") {
      adminWrapper.classList.add("sidebar-collapsed");
    }

    btnToggleSidebar.addEventListener("click", () => {
      adminWrapper.classList.toggle("sidebar-collapsed");
      const isCollapsed = adminWrapper.classList.contains("sidebar-collapsed");
      localStorage.setItem("admin_sidebar_collapsed", isCollapsed);
    });
  }

  // 6. Control del Menú Móvil para el Panel Admin (Hamburguesa y Overlay)
  const mobileToggle = document.getElementById("mobileSidebarToggle");
  const overlay = document.getElementById("sidebarOverlay");

  function toggleMobileMenu() {
    if (adminWrapper) {
      adminWrapper.classList.toggle("sidebar-active");
    }
  }

  function closeMobileMenu() {
    if (adminWrapper) {
      adminWrapper.classList.remove("sidebar-active");
    }
  }

  if (mobileToggle) {
    mobileToggle.addEventListener("click", toggleMobileMenu);
  }

  if (overlay) {
    overlay.addEventListener("click", closeMobileMenu);
  }

  // Cerrar el menú automáticamente al hacer clic en cualquier opción en móviles
  const menuLinks = document.querySelectorAll(".sidebar-menu a");
  menuLinks.forEach((link) => {
    link.addEventListener("click", () => {
      if (window.innerWidth <= 992) {
        closeMobileMenu();
      }
    });
  });
});

// ==========================================
// FUNCIONES GLOBALES Y FETCH DEL CARRITO
// ==========================================

window.actualizarContadorCarrito = function (cantidad) {
  const cartBadge = document.getElementById("cart-count");
  if (cartBadge) {
    cartBadge.textContent = cantidad;
  }
};

window.procesarRespuestaCarrito = function (data) {
  if (data.status === "success" && data.data && data.data.items) {
    let totalItems = 0;
    Object.values(data.data.items).forEach((item) => {
      totalItems += parseInt(item.cantidad || 0);
    });
    window.actualizarContadorCarrito(totalItems);
  }
};

// Sincronizar el contador del carrito al cargar la página
fetch("api/carrito.php")
  .then((response) => {
    if (response.ok) return response.json();
    throw new Error("No se pudo obtener el carrito");
  })
  .then((data) => {
    if (data.status === "success" && data.data && data.data.items) {
      let totalItems = 0;
      Object.values(data.data.items).forEach((item) => {
        totalItems += parseInt(item.cantidad || 0);
      });
      window.actualizarContadorCarrito(totalItems);
    }
  })
  .catch((error) => {
    console.log("Sesión no iniciada o carrito vacío.");
  });
