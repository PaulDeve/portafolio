<?php
/**
 * Funciones auxiliares del sistema
 * ComercioSys - Sistema de Gestión de Ventas
 */

require_once __DIR__ . '/../database/connection.php';

// Tasa de IGV (18%) - usar constante para facilidad de cambio
if (!defined('IGV_RATE')) {
    define('IGV_RATE', 0.18);
}

// Función para obtener estadísticas del dashboard
function getDashboardStats() {
    $conn = getDBConnection();
    
    // Obtener total de ventas
    $stmt = $conn->prepare("SELECT COUNT(*) as total_ventas, SUM(total) as monto_total FROM cobranza");
    $stmt->execute();
    $ventas_stats = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    // Obtener total de usuarios
    $stmt = $conn->prepare("SELECT COUNT(*) as total_usuarios FROM usuarios");
    $stmt->execute();
    $usuarios_stats = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    $conn->close();
    
    return [
        'total_ventas' => $ventas_stats['total_ventas'] ?? 0,
        'monto_total' => $ventas_stats['monto_total'] ?? 0,
        'total_usuarios' => $usuarios_stats['total_usuarios'] ?? 0
    ];
}

// Función para obtener todas las ventas
function getAllVentas($filtro_fecha = '', $filtro_concepto = '') {
    $conn = getDBConnection();
    
    $sql = "SELECT c.*, comp.nombre, comp.apellido 
            FROM cobranza c 
            INNER JOIN compradores comp ON c.id_comprador = comp.id";
    
    $params = [];
    $types = "";
    
    if (!empty($filtro_fecha)) {
        $sql .= " WHERE c.fecha = ?";
        $params[] = $filtro_fecha;
        $types .= "s";
    }
    
    if (!empty($filtro_concepto)) {
        $sql .= empty($filtro_fecha) ? " WHERE" : " AND";
        $sql .= " c.concepto LIKE ?";
        $params[] = "%$filtro_concepto%";
        $types .= "s";
    }
    
    $sql .= " ORDER BY c.fecha DESC, c.hora DESC";
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $ventas = [];
    while ($row = $result->fetch_assoc()) {
        $ventas[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    return $ventas;
}

// Función para obtener venta por ID
function getVentaById($id) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("SELECT * FROM cobranza WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $venta = $result->fetch_assoc();
    
    $stmt->close();
    $conn->close();
    
    return $venta;
}

// Función para crear nueva venta
function crearVenta($id_comprador, $concepto, $fecha, $hora, $cantidad, $precio_unitario) {
    $conn = getDBConnection();
    // Calcular total base y luego agregar IGV
    $total_base = $cantidad * $precio_unitario;
    $total = calcularTotalConIGV($total_base);
    
    $stmt = $conn->prepare("INSERT INTO cobranza (id_comprador, concepto, fecha, hora, cantidad, precio_unitario, total) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssidd", $id_comprador, $concepto, $fecha, $hora, $cantidad, $precio_unitario, $total);
    
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    
    return $result;
}

// Función para actualizar venta
function actualizarVenta($id, $concepto, $fecha, $hora, $cantidad, $precio_unitario) {
    $conn = getDBConnection();
    // Recalcular total base y agregar IGV
    $total_base = $cantidad * $precio_unitario;
    $total = calcularTotalConIGV($total_base);
    
    $stmt = $conn->prepare("UPDATE cobranza SET concepto = ?, fecha = ?, hora = ?, cantidad = ?, precio_unitario = ?, total = ? WHERE id = ?");
    $stmt->bind_param("sssiddi", $concepto, $fecha, $hora, $cantidad, $precio_unitario, $total, $id);
    
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    
    return $result;
}

// Función para eliminar venta
function eliminarVenta($id) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("DELETE FROM cobranza WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    
    return $result;
}

// Función para obtener todos los usuarios
function getAllUsuarios() {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("SELECT * FROM usuarios ORDER BY apellido, nombre");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $usuarios = [];
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    return $usuarios;
}

// Función para obtener usuario por ID
function getUsuarioById($id) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id_cod = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $usuario = $result->fetch_assoc();
    
    $stmt->close();
    $conn->close();
    
    return $usuario;
}

// Función para crear nuevo usuario
function crearUsuario($id_cod, $apellido, $nombre, $nick, $pass, $rol) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("INSERT INTO usuarios (id_cod, apellido, nombre, nick, pass, rol) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $id_cod, $apellido, $nombre, $nick, $pass, $rol);
    
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    
    return $result;
}

// Función para actualizar usuario
function actualizarUsuario($id_cod, $apellido, $nombre, $nick, $pass, $rol) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("UPDATE usuarios SET apellido = ?, nombre = ?, nick = ?, pass = ?, rol = ? WHERE id_cod = ?");
    $stmt->bind_param("sssssi", $apellido, $nombre, $nick, $pass, $rol, $id_cod);
    
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    
    return $result;
}

// Función para eliminar usuario
function eliminarUsuario($id_cod) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id_cod = ?");
    $stmt->bind_param("i", $id_cod);
    
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    
    return $result;
}

// Función para verificar si el nick existe
function nickExists($nick, $exclude_id = null) {
    $conn = getDBConnection();
    
    if ($exclude_id) {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM usuarios WHERE nick = ? AND id_cod != ?");
        $stmt->bind_param("si", $nick, $exclude_id);
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM usuarios WHERE nick = ?");
        $stmt->bind_param("s", $nick);
    }
    
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    $stmt->close();
    $conn->close();
    
    return $result['count'] > 0;
}

// Función para formatear fecha
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

// Función para formatear hora
function formatTime($time) {
    return date('H:i', strtotime($time));
}

// Función para formatear moneda
function formatCurrency($amount) {
    return '$' . number_format($amount, 2, '.', ',');
}

// ===== FUNCIONES PARA COMPRADORES/CLIENTES =====

// Función para obtener todos los compradores
function getAllCompradores() {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("SELECT * FROM compradores ORDER BY apellido, nombre");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $compradores = [];
    while ($row = $result->fetch_assoc()) {
        $compradores[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    return $compradores;
}

// Función para obtener comprador por ID
function getCompradorById($id) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("SELECT * FROM compradores WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $comprador = $result->fetch_assoc();
    
    $stmt->close();
    $conn->close();
    
    return $comprador;
}

// Función para crear nuevo comprador
function crearComprador($apellido, $nombre, $telefono, $email, $direccion) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("INSERT INTO compradores (apellido, nombre, telefono, email, direccion) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $apellido, $nombre, $telefono, $email, $direccion);
    
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    
    return $result;
}

// Función para actualizar comprador
function actualizarComprador($id, $apellido, $nombre, $telefono, $email, $direccion) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("UPDATE compradores SET apellido = ?, nombre = ?, telefono = ?, email = ?, direccion = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $apellido, $nombre, $telefono, $email, $direccion, $id);
    
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    
    return $result;
}

// Función para eliminar comprador
function eliminarComprador($id) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("DELETE FROM compradores WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    
    return $result;
}

// Función para verificar si el email existe
function emailExists($email, $exclude_id = null) {
    $conn = getDBConnection();
    
    if ($exclude_id) {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM compradores WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $exclude_id);
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM compradores WHERE email = ?");
        $stmt->bind_param("s", $email);
    }
    
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    $stmt->close();
    $conn->close();
    
    return $result['count'] > 0;
}

// Función para crear o encontrar comprador
function crearOEncontrarComprador($apellido, $nombre, $telefono, $email) {
    $conn = getDBConnection();
    
    // Primero buscar por email si se proporciona
    if (!empty($email)) {
        $stmt = $conn->prepare("SELECT id FROM compradores WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $comprador = $result->fetch_assoc();
            $stmt->close();
            $conn->close();
            return $comprador['id'];
        }
        $stmt->close();
    }
    
    // Si no se encuentra por email, buscar por nombre y apellido
    $stmt = $conn->prepare("SELECT id FROM compradores WHERE apellido = ? AND nombre = ?");
    $stmt->bind_param("ss", $apellido, $nombre);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $comprador = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $comprador['id'];
    }
    $stmt->close();
    
    // Si no se encuentra, crear nuevo comprador
    $stmt = $conn->prepare("INSERT INTO compradores (apellido, nombre, telefono, email) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $apellido, $nombre, $telefono, $email);
    
    if ($stmt->execute()) {
        $comprador_id = $conn->insert_id;
        $stmt->close();
        $conn->close();
        return $comprador_id;
    }
    
    $stmt->close();
    $conn->close();
    return false;
}

// Función para obtener productos únicos con sus precios
function getProductosUnicos() {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("SELECT DISTINCT concepto, precio_unitario FROM cobranza ORDER BY concepto");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $productos = [];
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    return $productos;
}

// Función para obtener precio de un producto
function getPrecioProducto($concepto) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("SELECT precio_unitario FROM cobranza WHERE concepto = ? LIMIT 1");
    $stmt->bind_param("s", $concepto);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $row['precio_unitario'];
    }
    
    $stmt->close();
    $conn->close();
    return 0;
}

// Función para calcular IGV (18%) - se suma al subtotal
function calcularIGV($subtotal) {
    return $subtotal * IGV_RATE;
}

// Función para calcular subtotal (monto base sin IGV)
function calcularSubtotal($total_con_igv) {
    return $total_con_igv / (1 + IGV_RATE);
}

// Función para calcular total con IGV (subtotal + IGV)
function calcularTotalConIGV($subtotal) {
    return $subtotal + calcularIGV($subtotal);
}
?>
