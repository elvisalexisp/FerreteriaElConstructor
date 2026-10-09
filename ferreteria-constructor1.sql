-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-10-2026 a las 06:36:30
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ferreteria-constructor1`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`, `descripcion`) VALUES
(1, 'Herramientas Eléctricas', 'Herramientas motorizadas y accesorios de alto rendimiento.'),
(2, 'Materiales de Construcción', 'Cementos, hierros, arenas y bases para obra gris.'),
(3, 'Plomería y Tubos', 'Tuberías, conexiones, válvulas y accesorios para agua.'),
(4, 'Pinturas y Acabados', 'Pinturas, impermeabilizantes, brochas y herramientas de acabado.'),
(5, 'Seguridad y EPP', 'Equipo de protección personal y seguridad industrial.'),
(6, 'Jardinería y Exteriores', 'Herramientas y accesorios para el cuidado de áreas verdes.'),
(7, 'Ferretería General', 'Tornillos, clavos, cerraduras y herramientas manuales.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id_detalle` int(11) NOT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_pedido`
--

INSERT INTO `detalle_pedido` (`id_detalle`, `id_pedido`, `id_producto`, `cantidad`, `precio`) VALUES
(1, 1, 202, 3, 95.00),
(2, 1, 201, 1, 25.00),
(3, 2, 202, 1, 95.00),
(4, 3, 202, 1, 95.00),
(5, 4, 200, 2, 290.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `direccion_envio` varchar(255) DEFAULT NULL,
  `nit` varchar(50) DEFAULT NULL,
  `nombre_factura` varchar(255) DEFAULT NULL,
  `estado` varchar(50) DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id_pedido`, `id_usuario`, `fecha`, `total`, `direccion_envio`, `nit`, `nombre_factura`, `estado`) VALUES
(1, 5, '2026-10-05 03:24:02', 310.00, 'zona 1 - Notas: dsfs', 'C/F', 'Consumidor Final', 'Procesando'),
(2, 5, '2026-10-07 06:29:22', 95.00, 'zona 11 la libertad', 'C/F', 'Consumidor Final', 'Pendiente'),
(3, 5, '2026-10-07 06:29:37', 95.00, 'Zona 1, Cobán', 'C/F', 'Consumidor Final', 'Pendiente'),
(4, 9, '2026-10-08 16:17:19', 580.00, 'Cobán, Alta Verapaz', 'C/F', 'Consumidor Final', 'Pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `imagen` varchar(255) DEFAULT 'default.png',
  `estado` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `id_categoria`, `nombre`, `descripcion`, `precio`, `cantidad`, `imagen`, `estado`) VALUES
(1, 1, 'Taladro Percutor 1/2\" 850W', 'Taladro de alta potencia ideal para concreto y mampostería.', 450.00, 15, 'prod_6ac72b8624a45.jpg', 'activo'),
(2, 1, 'Esmeril Angular 4-1/2\" 750W', 'Esmeriladora compacta para corte y desbaste.', 380.00, 12, 'prod_6ac72bcddc4fa.png', 'activo'),
(3, 1, 'Sierra Circular 7-1/4\" 1400W', 'Sierra circular profesional para cortes precisos en madera.', 720.00, 8, 'prod_6ac72c1ed4d7c.jpg', 'activo'),
(4, 1, 'Rotomartillo SDS Plus 800W', 'Rotomartillo demoledor con estuche y brocas.', 950.00, 6, 'prod_6ac72c6c32018.png', 'activo'),
(5, 1, 'Kit Inalámbrico Taladro + Atornillador', 'Set de herramientas a batería 18V.', 1250.00, 5, 'prod_6ac72cde4da1c.png', 'activo'),
(6, 1, 'Caladora Pendular 650W', 'Sierra caladora con guía láser.', 410.00, 10, 'prod_6ac7b2a5d304a.jpg', 'activo'),
(7, 1, 'Lijadora Orbital 300W', 'Lijadora para acabados finos en madera.', 320.00, 14, 'prod_6ac7b2e67c7dc.jpg', 'activo'),
(8, 1, 'Pistola de Calor 2000W', 'Pistola térmica para desprendimiento de pintura y plásticos.', 280.00, 11, 'prod_6ac7b3518c5dd.webp', 'activo'),
(9, 1, 'Router Rebajador de Madera 1200W', 'Fresadora profesional para carpintería.', 890.00, 4, 'prod_6ac7b3ae36afa.jpg', 'activo'),
(10, 1, 'Llave de Impacto Inalámbrica 20V', 'Llave de impacto de alto torque.', 1450.00, 7, 'prod_6ac7b3f07e803.jpg', 'activo'),
(11, 1, 'Soldadora Inverter 160A', 'Planta de soldar portátil MMA.', 1100.00, 9, 'prod_6ac7b458644d7.webp', 'activo'),
(12, 1, 'Minitorno Multiherramienta 135W', 'Kit rotativo con 100 accesorios.', 460.00, 13, 'prod_6ac7b4c5e5c41.webp', 'activo'),
(13, 1, 'Soporte para Taladro Vertical', 'Base estacionaria de columna.', 250.00, 10, 'prod_6ac7b51456e89.jpg', 'activo'),
(14, 1, 'Mezclador de Pintura y Mortero 1400W', 'Batidor eléctrico industrial.', 830.00, 5, 'prod_6ac7b5954a47c.jpg', 'activo'),
(15, 1, 'Sierra Sable 900W', 'Sierra reciprocante para demolición ligera.', 670.00, 8, 'prod_6ac7b5f20107d.jpg', 'activo'),
(16, 1, 'Compresor de Aire 24 Litros 2HP', 'Compresor portátil de pistón.', 1650.00, 3, 'prod_6ac7b63b41a32.jpg', 'activo'),
(17, 1, 'Hidrolavadora de Alta Presión 1500PSI', 'Lavadora a presión para limpieza profunda.', 980.00, 7, 'prod_6ac7b6dcb3d4c.jpg', 'activo'),
(18, 1, 'Aspiradora de Seco y Mojado 5 Galones', 'Aspiradora industrial compacta.', 750.00, 6, 'prod_6ac7b743173b3.jpg', 'activo'),
(19, 1, 'Garlopa Eléctrica 650W', 'Cepillo eléctrico para madera.', 620.00, 9, 'prod_6ac7b7730c6aa.webp', 'activo'),
(20, 1, 'Tronzadora de Metal 14\" 2000W', 'Cortadora sensitiva de metales.', 1350.00, 4, 'prod_6ac7b7f3d82ca.png', 'activo'),
(21, 1, 'Linterna LED Recargable de Trabajo', 'Lámpara portátil de alta intensidad.', 190.00, 20, 'prod_6ac7b8411403c.jpg', 'activo'),
(22, 1, 'Multímetro Digital Profesional', 'Instrumento de medición eléctrica.', 150.00, 25, 'prod_6ac7b894107d5.png', 'activo'),
(23, 1, 'Detector de Metales y Cables Pared', 'Escáner digital de muros.', 340.00, 12, 'prod_6ac7b8c6b8e96.jpg', 'activo'),
(24, 1, 'Nivel Láser Autonivelante 15m', 'Nivelación de líneas cruzadas.', 580.00, 8, 'prod_6ac7b92773faf.jpg', 'activo'),
(25, 1, 'Cargador de Baterías para Auto 12V', 'Cargador inteligente de acumuladores.', 410.00, 6, 'prod_6ac7b96c7e8ea.jpg', 'activo'),
(26, 1, 'Esmeril de Banco 6\" 370W', 'Amoladora de banco doble piedra.', 690.00, 5, 'prod_6ac7b9d7542c4.jpg', 'activo'),
(27, 1, 'Pistola para Silicona Caliente Industrial', 'Aplicador térmico de barras 11mm.', 120.00, 30, 'prod_6ac7bbf733164.jpg', 'activo'),
(28, 1, 'Taladro Angular a Batería', 'Taladro de cabeza compacta para espacios reducidos.', 990.00, 4, 'prod_6ac7bc4571619.jpg', 'activo'),
(29, 1, 'Sierra Ingletadora 10\" 1800W', 'Sierra de banco para ingletes y cortes transversales.', 1850.00, 3, 'prod_6ac7bd377d25e.jpg', 'activo'),
(30, 1, 'Generador de Corriente Gasolina 2.5KW', 'Planta eléctrica portátil de emergencia.', 3200.00, 2, 'prod_6ac7bd0e2e5cc.jpg', 'activo'),
(31, 2, 'Saco de Cemento Progreso 50kg', 'Cemento Portland gris estructural.', 85.00, 150, 'prod_6ac7bde536103.jpg', 'activo'),
(32, 2, 'Varilla de Hierro 3/8\" Grado 40', 'Varilla corrugada de refuerzo.', 65.00, 200, 'prod_6ac7be1fddc72.jpg', 'activo'),
(33, 2, 'Varilla de Hierro 1/2\" Grado 40', 'Varilla estructural pesada.', 115.00, 120, 'prod_6ac7be9f68fc2.jpg', 'activo'),
(34, 2, 'Varilla de Hierro 1/4\"', 'Varilla para eslabones y estribos.', 32.00, 180, 'prod_6ac7beecadb97.jpg', 'activo'),
(35, 2, 'Varilla de Hierro 5/8\"', 'Varilla de alta resistencia.', 180.00, 90, 'prod_6ac7bf7b6e997.jpg', 'activo'),
(36, 2, 'Metro Cúbico de Arena de Río', 'Arena fina para repellos y cernidos.', 160.00, 30, 'prod_6ac7bfeb54233.jpg', 'activo'),
(37, 2, 'Metro Cúbico de Piedrín', 'Agregado fino para concreto.', 180.00, 25, 'prod_6ac7c0697ade2.jpg', 'activo'),
(38, 2, 'Metro Cúbico de Selecto', 'Material de relleno y compactación.', 140.00, 40, 'prod_6ac7c0b970c0e.png', 'activo'),
(39, 2, 'Block de Pómez 15x20x40 cm', 'Block aligerado para muros.', 7.50, 500, 'prod_6ac7c0fdb05af.png', 'activo'),
(40, 2, 'Block de Pómez 10x20x40 cm', 'Block divisorio ligero.', 6.20, 400, 'prod_6ac7c14566cd6.jpg', 'activo'),
(41, 2, 'Block de Pómez 20x20x40 cm', 'Block pesado estructural.', 9.50, 300, 'prod_6ac7c1ff48bf0.jpg', 'activo'),
(42, 2, 'Ladrillo Terracota Estándar', 'Barro cocido para construcción.', 3.20, 800, 'prod_6ac7c238122f6.jpg', 'activo'),
(43, 2, 'Laminas de Zinc Alum 3.66m (12 pies)', 'Lámina acanalada galvanizada.', 95.00, 75, 'prod_6ac7c36e5e847.webp', 'activo'),
(44, 2, 'Lámina de Zinc Alum 3.00m (10 pies)', 'Lámina protectora para techos.', 80.00, 85, 'prod_6ac7c3a3b2c02.jpg', 'activo'),
(45, 2, 'Lámina Plastificada Termoacústica', 'Techo aislante térmico.', 220.00, 40, 'prod_6ac7c424d442e.jpg', 'activo'),
(46, 2, 'Malla Electrosoldada 6x6 10/10', 'Malla de acero para pisos y losas.', 195.00, 50, 'prod_6ac7c48c09e05.jpg', 'activo'),
(47, 2, 'Alambre de Amarrar (Quintal)', 'Alambre recocido blando.', 450.00, 15, 'prod_6ac7c4bf029da.webp', 'activo'),
(48, 2, 'Clavos para Concreto 2\" (Libra)', 'Clavo acerado de alta penetración.', 18.00, 100, 'prod_6ac7c5068e9c3.jpg', 'activo'),
(49, 2, 'Clavos con Cabeza 3\" (Libra)', 'Clavo común para madera.', 14.00, 120, 'prod_6ac7c53ed5597.jpg', 'activo'),
(50, 2, 'Cal Hidratada Saco 25kg', 'Cal para mezclas y albañilería.', 42.00, 60, 'prod_6ac7c588b179f.png', 'activo'),
(51, 2, 'Aditivo Impermeabilizante Sika 1 (Galón)', 'Resistencia a humedad en morteros.', 135.00, 25, 'prod_6ac7c5c874162.jpg', 'activo'),
(52, 2, 'Pegamento para Cerámica Pega-Piso Saco', 'Adhesivo en polvo para pisos.', 55.00, 90, 'prod_6ac7c60472ce9.jpg', 'activo'),
(53, 2, 'Tubo de Cartón para Columna (Casetón)', 'Molde tubular para columnas.', 85.00, 30, 'prod_6ac7c63f898cb.webp', 'activo'),
(54, 2, 'Curador para Concreto Balde', 'Sustancia selladora superficial.', 240.00, 12, 'prod_6ac7c66d5628d.jpg', 'activo'),
(55, 2, 'Fibra de Polipropileno para Concreto (Bol)', 'Refuerzo secundario anti-fisuras.', 35.00, 50, 'prod_6ac7c6b73ceb9.jpg', 'activo'),
(56, 2, 'Plástico Negro para Cimentación (Rollo)', 'Protección contra humedad de suelo.', 180.00, 20, 'prod_6ac7c6f4f4172.jpg', 'activo'),
(57, 2, 'Perfil C de Acero Purlin 2x4\"', 'Estructura metálica para techos.', 210.00, 35, 'prod_6ac7c73c5ec1d.jpg', 'activo'),
(58, 2, 'Angulo de Hierro 1\" x 1/8\"', 'Perfil estructural metálico.', 95.00, 45, 'prod_6ac7c76c8d7ba.png', 'activo'),
(59, 2, 'Plancha de Policarbonato Alveolar', 'Cubierta translúcida para iluminación.', 480.00, 15, 'prod_6ac7c7c0313aa.jpg', 'activo'),
(60, 2, 'Yeso Agrícola/Construcción Saco', 'Yeso en polvo de fraguado rápido.', 50.00, 40, 'prod_6ac7c80789fe5.jpg', 'activo'),
(61, 3, 'Tubo PVC 1/2\" C 40 (6m)', 'Tubería para agua potable a presión.', 45.00, 40, 'prod_6ac7c84742bfd.jpg', 'activo'),
(62, 3, 'Tubo PVC 3/4\" C 40 (6m)', 'Conducción hidráulica.', 65.00, 35, 'prod_6ac7c8923836a.jpg', 'activo'),
(63, 3, 'Tubo PVC 1\" C 40 (6m)', 'Tubería principal de agua.', 95.00, 30, 'prod_6ac7c8e243912.jpg', 'activo'),
(64, 3, 'Tubo PVC 2\" para Desagüe (6m)', 'Drenajes y aguas grises.', 75.00, 25, 'prod_6ac7c93222672.jpg', 'activo'),
(65, 3, 'Tubo PVC 3\" para Desagüe (6m)', 'Bajadas pluviales y sanitarias.', 120.00, 20, 'prod_6ac7c97623361.jpg', 'activo'),
(66, 3, 'Tubo PVC 4\" para Inodoro (6m)', 'Drenaje sanitario principal.', 175.00, 22, 'prod_6ac7ca2eddeb8.jpg', 'activo'),
(67, 3, 'Pegamento para PVC Frasco 1/4\"', 'Adhesivo solvente resistente.', 35.00, 50, 'prod_6ac7ca5ed7105.jpg', 'activo'),
(68, 3, 'Limpiador/Primario para PVC', 'Desengrasante disolvente.', 30.00, 40, 'prod_6ac7ca9b90886.jpg', 'activo'),
(69, 3, 'Llave de Esfera de Bronce 1/2\"', 'Válvula de paso roscada.', 42.00, 30, 'prod_6ac7cadfe5da5.jpg', 'activo'),
(70, 3, 'Llave de Esfera de Bronce 3/4\"', 'Válvula de paso mayor.', 60.00, 25, 'prod_6ac7cb18b5529.jpg', 'activo'),
(71, 3, 'Cinta de Teflon Profesional (10m)', 'Sellador de roscas denso.', 6.00, 120, 'prod_6ac7cb4783efc.webp', 'activo'),
(72, 3, 'Sifón Flexible para Fregadero', 'Desagüe ajustable para lavatrastos.', 28.00, 25, 'prod_6ac7cb7430a78.jpg', 'activo'),
(73, 3, 'Llave Mezcladora para Fregadero', 'Grifería de acero para cocina.', 180.00, 15, 'prod_6ac7cba260370.jpg', 'activo'),
(74, 3, 'Llave Mezcladora para Lavabo', 'Grifería de baño económica.', 150.00, 18, 'prod_6ac7cbcd8f86c.jpg', 'activo'),
(75, 3, 'Inodoro One Piece Cerámica Blanca', 'Sanitario ahorrador completo.', 950.00, 6, 'prod_6ac7cc3ee5d9d.jpg', 'activo'),
(76, 3, 'Registro Sanitario PVC 4\"', 'Tapa registro para inspección.', 45.00, 20, 'prod_6ac7cd2b602be.webp', 'activo'),
(77, 3, 'Codo PVC 1/2\" 90 Grados', 'Conexión hidráulica ángulo.', 2.50, 150, 'prod_6ac7cd5dd2551.jpg', 'activo'),
(78, 3, 'Tee PVC 1/2\" Roscada', 'Conexión en T.', 3.50, 130, 'prod_6ac7cd8f4db53.jpg', 'activo'),
(79, 3, 'Reducción bushing PVC 3/4\" a 1/2\"', 'Adaptador de medidas.', 3.00, 100, 'prod_6ac7cdb8c83a2.jpg', 'activo'),
(80, 3, 'Flotador para Tinaco 1/2\"', 'Válvula de boya automática.', 65.00, 15, 'prod_6ac7ce169e771.jpg', 'activo'),
(81, 3, 'Tinaco Rotoplas 1100 Litros Tricapa', 'Almacenamiento seguro de agua.', 1650.00, 4, 'prod_6ac7ce542d986.webp', 'activo'),
(82, 3, 'Bomba Periférica de Agua 0.5HP', 'Presurizador para vivienda.', 550.00, 8, 'prod_6ac7ce83eb78f.jpg', 'activo'),
(83, 3, 'Bomba Centrífuga 1HP', 'Bombeo de caudal alto.', 1150.00, 5, 'prod_6ac7ceb57788c.jpg', 'activo'),
(84, 3, 'Manguera Mallonada para Agua 1/2\" (Rollo 50m)', 'Conducción flexible.', 220.00, 10, 'prod_6ac7cef9da8cc.jpg', 'activo'),
(85, 3, 'Empaque de Cera para Inodoro', 'Sellador anti-olores.', 25.00, 30, 'prod_6ac7cf4dd187f.jpg', 'activo'),
(86, 3, 'Llave Angular Cromada 1/2\" x 3/8\"', 'Llave de gaveta para lavabo.', 38.00, 25, 'prod_6ac7cf7ba0fa0.jpg', 'activo'),
(87, 3, 'Flexometálico para Inodoro 40cm', 'Conector flexible de agua.', 32.00, 35, 'prod_6ac7cfa6e977d.jpg', 'activo'),
(88, 3, 'Sumidero / Coladera de Piso Acero', 'Rejilla desagüe de piso.', 22.00, 40, 'prod_6ac7cfe58fdfc.jpg', 'activo'),
(89, 3, 'Tubo PPR Termofusión 1/2\" (4m)', 'Tubería alta presión caliente/fría.', 55.00, 20, 'prod_6ac7cc2106755.webp', 'activo'),
(90, 3, 'Codo PPR 1/2\" Termofusión', 'Conexión fusión térmica.', 4.00, 80, 'prod_6ac7cbe88a82c.jpg', 'activo'),
(91, 4, 'Pintura de Aceite Blanca Galón', 'Esmalte sintético brillante duradero.', 120.00, 18, 'prod_6ac7cbc89e2f4.jpg', 'activo'),
(92, 4, 'Pintura de Aceite Negra Galón', 'Esmalte sintético negro.', 120.00, 15, 'prod_6ac7cbac693af.jpg', 'activo'),
(93, 4, 'Pintura de Aceite Roja Galón', 'Esmalte sintético tono rojo.', 125.00, 10, 'prod_6ac7cb769ad40.jpg', 'activo'),
(94, 4, 'Pintura Vinílica Blanca 5 Galones (Tipo A)', 'Pintura látex para paredes.', 650.00, 8, 'prod_6ac7cb46c9472.png', 'activo'),
(95, 4, 'Pintura Vinílica Blanco Hueso 5 Galones', 'Pintura interior/exterior.', 620.00, 10, 'prod_6ac7cafeb34df.jpg', 'activo'),
(96, 4, 'Impermeabilizante Rouzo 5 Galones', 'Membrana líquida acrílica techos.', 450.00, 10, 'prod_6ac7ca8ec68eb.jpg', 'activo'),
(97, 4, 'Impermeabilizante Elastomérico 3 Años (Galón)', 'Protección contra filtraciones.', 110.00, 20, 'prod_6ac7ca649b5d6.jpg', 'activo'),
(98, 4, 'Brocha de Cerdas de 2\"', 'Brocha económica acabados.', 12.00, 50, 'prod_6ac7ca3f2bbc3.jpg', 'activo'),
(99, 4, 'Brocha de Cerdas de 3\"', 'Brocha profesional aplicación.', 18.00, 60, 'prod_6ac7ca12af90c.jpg', 'activo'),
(100, 4, 'Brocha de Cerdas de 4\"', 'Brocha ancha para muros.', 25.00, 40, 'prod_6ac7c9e815b31.jpg', 'activo'),
(101, 4, 'Lijas para Madera Grano 80 (Paquete)', 'Pliegos flexibles desbaste.', 25.00, 40, 'prod_6ac7c9c0362aa.jpg', 'activo'),
(102, 4, 'Lijas para Madera Grano 120 (Paquete)', 'Pliegos acabado intermedio.', 25.00, 40, 'prod_6ac7c99b4d0a2.jpg', 'activo'),
(103, 4, 'Lija al Agua Grano 400 (Pliego)', 'Lijado de metales y pinturas.', 6.00, 60, 'prod_6ac7c63027df4.jpg', 'activo'),
(104, 4, 'Rodillo Antigota 9\" con Mango', 'Pintura vinílica sin salpicaduras.', 35.00, 22, 'prod_6ac7c60b6d791.jpg', 'activo'),
(105, 4, 'Rollo de Repuesto para Rodillo 9\"', 'Funda recambio.', 18.00, 30, 'prod_6ac7c5ef14089.jpg', 'activo'),
(106, 4, 'Masilla Plástica para Paredes 1 Galón', 'Compuesto alisador imperfecciones.', 65.00, 15, 'prod_6ac7c5c168c44.jpg', 'activo'),
(107, 4, 'Saka-Manchas / Sellador Anti-Hongo Galón', 'Preparación de superficies.', 95.00, 12, 'prod_6ac7c592178fb.png', 'activo'),
(108, 4, 'Thinner Acrílico Galón', 'Diluyente de esmaltes y limpieza.', 75.00, 20, 'prod_6ac7c5757e437.png', 'activo'),
(109, 4, 'Aguarrás / Solvente Mineral Galón', 'Disolvente estándar.', 60.00, 25, 'prod_6ac7c5519a152.jpg', 'activo'),
(110, 4, 'Cinta de Enmascarar / Masking Tape 3/4\"', 'Protección de bordes.', 15.00, 80, 'prod_6ac7c52fc84aa.jpg', 'activo'),
(111, 4, 'Lana de Acero (Viruta) Paquete', 'Pulido y limpieza de superficies.', 14.00, 45, 'prod_6ac7c511d74a6.png', 'activo'),
(112, 4, 'Espátula de Acero 3\"', 'Herramienta para masilla.', 18.00, 35, 'prod_6ac7c4df9a932.jpg', 'activo'),
(113, 4, 'Espátula de Acero 5\"', 'Herramienta ancha.', 24.00, 30, 'prod_6ac7c4c311fec.jpg', 'activo'),
(114, 4, 'Llana Metálica Dentada', 'Aplicación de adhesivos de cerámica.', 42.00, 20, 'prod_6ac7c4a268dd6.jpg', 'activo'),
(115, 4, 'Bandeja Plástica para Pintura', 'Contenedor para rodillo.', 28.00, 25, 'prod_6ac7c473aabf2.jpg', 'activo'),
(116, 4, 'Pintura en Spray Negro Mate 400ml', 'Esmalte aerosol rápido.', 38.00, 40, 'prod_6ac7c45519ce4.jpg', 'activo'),
(117, 4, 'Pintura en Spray Aluminio / Metálico', 'Aerosol acabado brillante.', 40.00, 35, 'prod_6ac7c42685f45.jpg', 'activo'),
(118, 4, 'Pintura para Tráfico / Pisos Galón', 'Esmalte alta resistencia abrasión.', 160.00, 10, 'prod_6ac7c3f07b5c5.jpg', 'activo'),
(119, 4, 'Barniz Marino Transparente Galón', 'Protección madera exterior.', 140.00, 14, 'prod_6ac7c3c5758ab.jpg', 'activo'),
(120, 4, 'Tinte para Madera Nogal 1 Litro', 'Colorante penetrante.', 50.00, 18, 'prod_6ac7c3a34dd43.jpg', 'activo'),
(121, 5, 'Casco de Seguridad Industrial con Suspensión', 'Protección de polietileno.', 45.00, 35, 'prod_6ac7c37b994d7.jpg', 'activo'),
(122, 5, 'Guantes de Carnaza Reforzados', 'Protección para carga pesada.', 22.00, 50, 'prod_6ac7c0b277aaf.jpg', 'activo'),
(123, 5, 'Guantes de Nitrilo Antideslizantes (Par)', 'Manipulación fina y aceites.', 15.00, 70, 'prod_6ac7c08bbfef6.jpg', 'activo'),
(124, 5, 'Lentes de Protección Transparentes', 'Gafas anti-rayones UV.', 18.00, 45, 'prod_6ac7c06302cbe.jpg', 'activo'),
(125, 5, 'Lentes de Protección Oscuros', 'Gafas solares industriales.', 18.00, 40, 'prod_6ac7c0356c045.jpg', 'activo'),
(126, 5, 'Mascarilla contra Polvo N95 (Caja 10)', 'Protección partículas y cemento.', 75.00, 20, 'prod_6ac7c012d5a4f.jpg', 'activo'),
(127, 5, 'Respirador de Doble Vía con Filtros', 'Protección gases y vapores.', 190.00, 12, 'prod_6ac7bfd8bb4e0.jpg', 'activo'),
(128, 5, 'Botas de Hule Industriales con Puntera', 'Calzado impermeable acero.', 140.00, 14, 'prod_6ac7bfb6f19d8.jpg', 'activo'),
(129, 5, 'Botas de Cuero Industriales con Puntera', 'Calzado dieléctrico.', 320.00, 10, 'prod_6ac7bf89a850c.jpg', 'activo'),
(130, 5, 'Arnés de Seguridad para Alturas', 'Protección caídas certificado.', 290.00, 8, 'prod_6ac7bf5629ae5.jpg', 'activo'),
(131, 5, 'Eslinga / Banda de Anclaje con Gancho', 'Línea de vida.', 180.00, 10, 'prod_6ac7bf2065db6.jpg', 'activo'),
(132, 5, 'Chaleco Reflectivo Alta Visibilidad', 'Seguridad vial y obra.', 35.00, 50, 'prod_6ac7beeb94050.jpg', 'activo'),
(133, 5, 'Protectores Auditivos de Copesa / Orejeras', 'Atenuación de ruido.', 65.00, 22, 'prod_6ac7bec7b8e5c.jpg', 'activo'),
(134, 5, 'Tapones Auditivos de Silicona (Par)', 'Protección reutilizable.', 12.00, 60, 'prod_6ac7b76d61d16.jpg', 'activo'),
(135, 5, 'Cono de Seguridad Vial Naranja 70cm', 'Delimitación de áreas.', 75.00, 25, 'prod_6ac7b750cde85.jpg', 'activo'),
(136, 5, 'Cinta de Precaución Peligro (Rollo 300m)', 'Señalización de obras.', 55.00, 30, 'prod_6ac7b7115bf6f.jpg', 'activo'),
(137, 5, 'Careta para Soldar Electrónica', 'Protección facial fotosensible.', 310.00, 7, 'prod_6ac7b6ef16380.jpg', 'activo'),
(138, 5, 'Guantes de Soldador Carnaza Larga', 'Protección térmica.', 45.00, 20, 'prod_6ac7b6b829fa6.jpg', 'activo'),
(139, 5, 'Rodilleras Protectoras para Albañil', 'Confort en pisos.', 85.00, 15, 'prod_6ac7b699ea5cf.jpg', 'activo'),
(140, 5, 'Faja Lumbar de Soporte Ergonómico', 'Prevención lesiones de columna.', 95.00, 18, 'prod_6ac7b67790cbd.jpg', 'activo'),
(141, 6, 'Machete Gavilán 22\"', 'Hoja acero al carbono campo.', 65.00, 25, 'prod_6ac7a5ed340de.webp', 'activo'),
(142, 6, 'Lima Triangular para Machete 8\"', 'Afilado de herramientas.', 18.00, 40, 'prod_6ac7a5c712492.jpg', 'activo'),
(143, 6, 'Azadón Ojo Redondo 3 Libras', 'Labranza de tierra.', 95.00, 12, 'prod_6ac7a5a5be367.webp', 'activo'),
(144, 6, 'Pala Metálica Redonda con Mango', 'Excavación y jardinería.', 110.00, 15, 'prod_6ac7a572aff54.jpg', 'activo'),
(145, 6, 'Pala Metálica Cuadrada con Mango', 'Movimiento de arena/tierra.', 110.00, 14, 'prod_6ac7a552ef3a0.jpg', 'activo'),
(146, 6, 'Carretilla de Mano Rueda Maciza', 'Transporte de materiales obra.', 380.00, 8, 'prod_6ac7a52fe2126.jpg', 'activo'),
(147, 6, 'Manguera de Riego 1/2\" (Rollo 25m)', 'Jardinería doméstica.', 130.00, 18, 'prod_6ac7a4e717d09.jpg', 'activo'),
(148, 6, 'Pistola Rociadora de Agua Multichorro', 'Riego para jardín.', 45.00, 25, 'prod_6ac7a4c49a768.jpg', 'activo'),
(149, 6, 'Tijera de Podar Manual para Plantas', 'Corte de ramas y arbustos.', 55.00, 20, 'prod_6ac7a49cbee4b.jpg', 'activo'),
(150, 6, 'Arco de Sierra para Poda con Hoja', 'Corte de madera verde.', 85.00, 12, 'prod_6ac7a479df744.jpg', 'activo'),
(151, 6, 'Rastrillo Metálico de 14 Dientes', 'Limpieza de hojas y césped.', 75.00, 15, 'prod_6ac7a459171d5.jpg', 'activo'),
(152, 6, 'Bomba de Fumigación Manual 16 Litros', 'Aspersión agrícola.', 280.00, 9, 'prod_6ac7a4341081d.jpg', 'activo'),
(153, 6, 'Guadaña / Desmalezadora a Gasolina 2T', 'Corte de maleza extensiva.', 1850.00, 3, 'prod_6ac7a40dccc66.jpg', 'activo'),
(154, 6, 'Machete Tramontina 18\"', 'Herramienta agrícola.', 58.00, 30, 'prod_6ac7a3e31910f.jpg', 'activo'),
(155, 6, 'Hacha de Leñador 3.5 Libras', 'Corte de troncos.', 195.00, 7, 'prod_6ac7a3b5e4521.jpg', 'activo'),
(156, 6, 'Machete Cacha de Plástico 20\"', 'Herramienta de corte.', 60.00, 22, 'prod_6ac7a38ab1482.jpg', 'activo'),
(157, 6, 'Aspersor de Impacto para Riego', 'Aspersión de jardines grandes.', 65.00, 10, 'prod_6ac75e401264e.jpg', 'activo'),
(158, 6, 'Malla Sombra Negra 80% (Metro lineal)', 'Protección solar cultivos.', 25.00, 50, 'prod_6ac72af0bae13.webp', 'activo'),
(159, 6, 'Fumigadora Manual de Presión 2 Litros', 'Jardinería menor.', 42.00, 20, 'prod_6ac729f6acb9b.png', 'activo'),
(160, 6, 'Tijera Cortasetos de Manos Largas', 'Diseño paisajístico.', 140.00, 8, 'prod_6ac70b7d21d79.jpg', 'activo'),
(161, 7, 'Candado Acero Templado 50mm', 'Alta seguridad anti-corte.', 55.00, 30, 'prod_6ac70b62cfa2a.webp', 'activo'),
(162, 7, 'Candado Acero Templado 40mm', 'Seguridad mediana.', 42.00, 35, 'prod_6ac70b4ae22fa.jpg', 'activo'),
(163, 7, 'Cerradura de Embutir para Puerta Madera', 'Chapa principal.', 180.00, 15, 'prod_6ac70b345acce.jpg', 'activo'),
(164, 7, 'Cerradura de Bola para Baño / Recámara', 'Chapa cilíndrica.', 110.00, 20, 'prod_6ac70b18eb8f2.jpg', 'activo'),
(165, 7, 'Juego de Llaves Allen Milimétricas (Set 9)', 'Hexagonales.', 35.00, 40, 'prod_6ac6e944c41e2.jpg', 'activo'),
(166, 7, 'Juego de Llaves Allen Standard (Set 9)', 'Pulgadas.', 35.00, 40, 'prod_6ac6e8f92d625.jpg', 'activo'),
(167, 7, 'Juego de Destornilladores Pro (Set 6)', 'Planos y de estrella.', 65.00, 30, 'prod_6ac6e8d1a7699.jpg', 'activo'),
(168, 7, 'Martillo uña 16 oz Mango de Fibra', 'Golpeteo y extracción clavos.', 55.00, 45, 'prod_6ac6e878ad112.jpg', 'activo'),
(169, 7, 'Martillo uña 20 oz Profesional', 'Alto impacto.', 75.00, 25, 'prod_6ac6e85556d57.jpg', 'activo'),
(170, 7, 'Pinza de Presión C-Clamp 10\"', 'Mordaza de sujeción.', 65.00, 25, 'prod_6ac6e83a01073.webp', 'activo'),
(171, 7, 'Alicate de Corte Diagonal 6\"', 'Corte de cables.', 45.00, 30, 'prod_6ac6e8145c1dc.webp', 'activo'),
(172, 7, 'Pinza de Electricista Universal 8\"', 'Herramienta aislada.', 55.00, 35, 'prod_6ac6e7fbcea5b.webp', 'activo'),
(173, 7, 'Llave Inglesa / Ajustable 8\"', 'Mordaza móvil.', 48.00, 30, 'prod_6ac6e7e5d80ef.jpg', 'activo'),
(174, 7, 'Llave Inglesa / Ajustable 12\"', 'Tamaño grande.', 85.00, 18, 'prod_6ac6e7cd3548d.jpg', 'activo'),
(175, 7, 'Flexómetro / Cinta Métrica 5 Metros', 'Medición precisa.', 32.00, 50, 'prod_6ac6e7b196209.jpg', 'activo'),
(176, 7, 'Flexómetro / Cinta Métrica 8 Metros', 'Medición larga.', 50.00, 40, 'prod_6ac6e79784fbf.webp', 'activo'),
(177, 7, 'Nivel de Aluminio con Gotas 24\"', 'Plomada y nivel.', 65.00, 20, 'prod_6ac6e77f8f096.webp', 'activo'),
(178, 7, 'Arco de Sierra para Metales Profesional', 'Corte perfiles.', 52.00, 25, 'prod_6ac6e76740df2.jpg', 'activo'),
(179, 7, 'Cuchilla / Cutter Retráctil con 3 Cuchillas', 'Corte cartón y plásticos.', 18.00, 60, 'prod_6ac6e74fb4f79.jpg', 'activo'),
(180, 7, 'Juego de Brocas para Concreto (Set 5)', 'Perforación mampostería.', 45.00, 30, 'prod_6ac6e728a97eb.jpg', 'activo'),
(181, 7, 'Juego de Brocas para Madera (Set 5)', 'Guías espirales.', 40.00, 30, 'prod_6ac6e70dc13e7.webp', 'activo'),
(182, 7, 'Juego de Brocas para Metal HSS (Set 13)', 'Acero rápido.', 75.00, 25, 'prod_6ac6e6f6e0158.webp', 'activo'),
(183, 7, 'Tornillos para Madera 1-1/2\" (Caja 100)', 'Fijación.', 25.00, 40, 'prod_6ac6e6dbe278a.jpg', 'activo'),
(184, 7, 'Tuz / Tarugos Plásticos 1/4\" (Caja 100)', 'Anclajes de pared.', 18.00, 50, 'prod_6ac6e6bda08ad.webp', 'activo'),
(185, 7, 'Pijas / Tornillos Autorroscantes (Caja 100)', 'Unión láminas y metales.', 28.00, 45, 'prod_6ac6e6a3627a6.webp', 'activo'),
(186, 7, 'Grasa Multiusos Chasis (Tarro 450g)', 'Lubricación mecánica.', 32.00, 25, 'prod_6ac7298a3e23f.jpg', 'activo'),
(187, 7, 'Aceite Lubricante Aflojatodo WD-40 400ml', 'Desoxidante.', 45.00, 50, 'prod_6ac6e62fa4770.jpg', 'activo'),
(188, 7, 'Lija de Esmeril Grano 60 (Hoja)', 'Desbaste áspero.', 6.00, 70, 'prod_6ac6e617459fa.jpg', 'activo'),
(189, 7, 'Pistola Aplicadora de Silicón en Cartucho', 'Calafateo.', 35.00, 30, 'prod_6ac6e5f1af322.webp', 'activo'),
(190, 7, 'Silicón Antihongo Transparente Cartucho', 'Sellador sanitario.', 38.00, 40, 'prod_6ac6e5da2c1a2.jpg', 'activo'),
(191, 7, 'Cinta Aislar / Eléctrica Negra 3M', 'Aislamiento cables.', 12.00, 90, 'prod_6ac6e5c5a42e0.webp', 'activo'),
(192, 7, 'Precintos / Cinchos Plásticos 20cm (Paquete 100)', 'Sujeción cables.', 22.00, 60, 'prod_6ac6e5ad29af6.webp', 'activo'),
(193, 7, 'Gafas de Foco / Lupa de Mano', 'Inspección detalle.', 25.00, 20, 'prod_6ac6e584e3818.jpg', 'activo'),
(194, 7, 'Imán Telescópico Recoge-Tornillos', 'Recuperación de piezas.', 30.00, 15, 'prod_6ac728633cb6a.jpg', 'activo'),
(195, 7, 'Escuadra Metálica de Carpintero 12\"', 'Trazo 90 y 45 grados.', 45.00, 30, 'prod_6ac6e539d4292.webp', 'activo'),
(196, 7, 'Cincel Plano de Acero para Concreto 8\"', 'Demolición manual.', 35.00, 25, 'prod_6ac6e5214cc52.webp', 'activo'),
(197, 7, 'Punta de Demolición / Barreno Hexagonal', 'Cincel pesado.', 120.00, 10, 'prod_6ac6e4ff3b7a4.jpg', 'activo'),
(198, 7, 'Gato Hidráulico de Botella 2 Toneladas', 'Elevación carga.', 160.00, 12, 'prod_6ac6e4e2c90ab.jpg', 'activo'),
(199, 7, 'Llave de Rueda en Cruz para Auto', 'Desapriete de birlos.', 110.00, 15, 'prod_6ac6e4bfea111.jpg', 'activo'),
(200, 7, 'Juego de Copas / Dados Llave Crique (Set 40)', 'Mecánica general.', 290.00, 14, 'prod_6ac6e49f093be.png', 'activo'),
(201, 7, 'Cinta Doble Faz Espuma (Rollo)', 'Adhesivo de montaje.', 25.00, 35, 'prod_6ac7274637fb7.webp', 'activo'),
(202, 7, 'Rueda para Carretilla de Hule Macizo', 'Repuesto carretilla.', 95.00, 10, 'prod_6ac6e48baabf5.png', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resenas`
--

CREATE TABLE `resenas` (
  `id_resena` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `calificacion` int(11) DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `resenas`
--

INSERT INTO `resenas` (`id_resena`, `id_usuario`, `id_producto`, `calificacion`, `comentario`, `fecha`) VALUES
(1, 5, 198, 5, 'Producto de muy buena calidad.', '2026-10-05 02:25:05'),
(3, 6, 200, 5, 'Excelente producto de muy buena calidad.', '2026-10-07 05:51:58'),
(4, 9, 149, 5, 'Producto de muy buena calidad, recomendado.', '2026-10-08 16:16:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `tipo_usuario` enum('admin','subadmin','cliente') DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `correo`, `foto`, `password`, `telefono`, `direccion`, `tipo_usuario`) VALUES
(1, 'Administrador', 'General', 'admin@ferreteria.com', 'perfil_1_1791468281.jpg', '$2y$10$9oJ2lfAGkBlsUja1WCOaee.BjM9GZk.QRHZgOJyfZfeVMvgRxwPxS', '55555555', 'Oficina Central Cobán', 'admin'),
(2, 'Carlos', 'Subadmin', 'subadmin@ferreteria.com', NULL, '$2y$10$9oJ2lfAGkBlsUja1WCOaee.BjM9GZk.QRHZgOJyfZfeVMvgRxwPxS', '44444444', 'Bodega Central Cobán', 'subadmin'),
(3, 'Elvis', 'Poou', 'elvisalexisp@gmail.com', NULL, '$2y$10$BDmrwPEJQQ5Uc4LzYMLEy.5ca2BpQbIto4VbYJgKguPpdHM6VOep2', '31379670', 'Cobán Centro', 'cliente'),
(5, 'Alexis', 'Poou', 'alexis@gmail.com', 'perfil_5_1791176921.png', '$2y$10$c4p.llJOvH7PHWPGVDMyVe40V.LOJNdvcWtTylQeVFSKzBrZiQ0sm', '33334444', 'ciudad', 'cliente'),
(6, 'Luis', 'Macz', 'luismacz@gmail.com', 'perfil_6_1791352497.jpg', '$2y$10$oA07hY6RQTrx/b35kb4inu6pl4dzu3TEX0jxR70CaWzJ2Qfk5To.q', '34543465', 'zona 12', 'cliente'),
(7, 'Juan', 'Ochoa', 'jochoa@gmail.com', NULL, '$2y$10$OctRHVQ3cKFsYI.Y.dJLSOjpkUxatnq7Ml2hOh0G6Yf1f9nSLJpqC', '45565445', 'zona 2', 'cliente'),
(8, 'Maggy', 'Flores', 'maggyf@ferreteria.com', NULL, '$2y$12$C1Lizs0p5F1EUvapya4bEec6TObt4/noyMEUwN6hwrWc/731VG7o6', '45565445', 'zona 2 COBAN', 'admin'),
(9, 'user', 'u', 'user@ferreteria.com', NULL, '$2y$12$lbPqLpWSc8zc6U0qbqfMZ.NQiqGcQgTzS0e9.OwoWSqcrVxb0nzX2', '34534533', 'zona 1, Coban', 'cliente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wishlist`
--

CREATE TABLE `wishlist` (
  `id_wishlist` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `wishlist`
--

INSERT INTO `wishlist` (`id_wishlist`, `id_usuario`, `id_producto`) VALUES
(4, 5, 200),
(6, 6, 171),
(7, 5, 197),
(9, 1, 185),
(10, 1, 182);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `resenas`
--
ALTER TABLE `resenas`
  ADD PRIMARY KEY (`id_resena`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id_wishlist`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_producto` (`id_producto`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=203;

--
-- AUTO_INCREMENT de la tabla `resenas`
--
ALTER TABLE `resenas`
  MODIFY `id_resena` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id_wishlist` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_pedido_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `resenas`
--
ALTER TABLE `resenas`
  ADD CONSTRAINT `resenas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `resenas_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
