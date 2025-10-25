<?php
/**
 * Página para mostrar e imprimir boleta de venta
 * ComercioSys - Sistema de Gestión de Ventas
 */

require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Verificar que el usuario esté logueado
requireLogin();

$user = getCurrentUser();

// Obtener ID de la venta
$venta_id = $_GET['id'] ?? 0;

if (!$venta_id) {
    header('Location: ventas.php');
    exit();
}

// Obtener datos de la venta
$venta = getVentaById($venta_id);

if (!$venta) {
    header('Location: ventas.php');
    exit();
}

// Obtener datos del comprador
$comprador = getCompradorById($venta['id_comprador']);

// Calcular montos con IGV
// Interpretación: el subtotal (sin IGV) será la suma de los totales de las líneas
// (en este caso el valor almacenado en la venta). Si hubiera varias líneas, aquí
// habría que sumar cada $line['total'] para obtener el subtotal.
$subtotal = $venta['total'];
$igv = calcularIGV($subtotal);
$total_con_igv = $subtotal + $igv;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧾 ComercioSys - Boleta de Venta</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Estilos específicos para la boleta */
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            background: white;
        }
        
        .invoice-header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        
        .invoice-title {
            font-size: 2rem;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 0.5rem;
        }
        
        .invoice-subtitle {
            color: #64748b;
            font-size: 1.1rem;
        }
        
        .invoice-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .invoice-section {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 8px;
        }
        
        .invoice-section h3 {
            color: #2563eb;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }
        
        .invoice-details {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .invoice-table th {
            background: #2563eb;
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }
        
        .invoice-table td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .invoice-table tr:last-child td {
            border-bottom: none;
        }
        
        .invoice-total {
            background: #f8fafc;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .invoice-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
        }
        
        .print-actions {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .btn-print {
            background: #10b981;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            margin-right: 1rem;
        }
        
        .btn-print:hover {
            background: #059669;
        }
        
        .btn-back {
            background: #64748b;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-back:hover {
            background: #475569;
        }
        
        /* Estilos para impresión */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .print-actions {
                display: none;
            }
            
            .invoice-container {
                max-width: none;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="print-actions">
            <button onclick="window.print()" class="btn-print">
                <i class="fas fa-print"></i>
                Imprimir Boleta
            </button>
            <a href="ventas.php" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                Volver a Ventas
            </a>
        </div>
        
        <div class="invoice-header">
            <div class="invoice-title">🧾 ComercioSys</div>
            <div class="invoice-subtitle">Sistema de Gestión de Ventas</div>
        </div>
        
        <div class="invoice-info">
            <div class="invoice-section">
                <h3>Datos de la Empresa</h3>
                <p><strong>ComercioSys</strong></p>
                <p>Sistema de Gestión de Ventas</p>
                <p>Fecha de emisión: <?php echo date('d/m/Y H:i'); ?></p>
            </div>
            
            <div class="invoice-section">
                <h3>Datos del Cliente</h3>
                <p><strong><?php echo htmlspecialchars($comprador['nombre'] . ' ' . $comprador['apellido']); ?></strong></p>
                <p>Teléfono: <?php echo htmlspecialchars($comprador['telefono']); ?></p>
                <?php if (!empty($comprador['email'])): ?>
                    <p>Email: <?php echo htmlspecialchars($comprador['email']); ?></p>
                <?php endif; ?>
                <?php if (!empty($comprador['direccion'])): ?>
                    <p>Dirección: <?php echo htmlspecialchars($comprador['direccion']); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="invoice-details">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Producto/Servicio</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo htmlspecialchars($venta['concepto']); ?></td>
                        <td><?php echo $venta['cantidad']; ?></td>
                        <?php
                            // Mostrar precio unitario basado en el total almacenado dividido por cantidad
                            $cantidad = (int)($venta['cantidad'] ?? 1);
                            $precio_unitario_mostrar = $cantidad ? ((float)$venta['total'] / $cantidad) : (float)($venta['precio_unitario'] ?? 0);
                        ?>
                        <td><?php echo formatCurrency($precio_unitario_mostrar); ?></td>
                        <td><?php echo formatCurrency($venta['total']); ?></td>
                    </tr>
                    <tr class="invoice-total">
                        <td colspan="3"><strong>Subtotal (sin IGV):</strong></td>
                        <td><strong><?php echo formatCurrency($subtotal); ?></strong></td>
                    </tr>
                    <tr class="invoice-total">
                        <td colspan="3"><strong>IGV (18%):</strong></td>
                        <td><strong><?php echo formatCurrency($igv); ?></strong></td>
                    </tr>
                    <tr class="invoice-total" style="background: #2563eb; color: white; font-size: 1.2rem;">
                        <td colspan="3"><strong>TOTAL:</strong></td>
                        <td><strong><?php echo formatCurrency($total_con_igv); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="invoice-footer">
            <p><strong>Total de la Venta (con IGV): <?php echo formatCurrency($total_con_igv); ?></strong></p>
            <p>Fecha de la venta: <?php echo formatDate($venta['fecha']) . ' ' . formatTime($venta['hora']); ?></p>
            <p>Venta registrada por: <?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido']); ?></p>
            <p>¡Gracias por su compra!</p>
        </div>
    </div>
    
    <script>
        // Auto-imprimir si se pasa el parámetro print=1
        if (window.location.search.includes('print=1')) {
            window.onload = function() {
                window.print();
            };
        }
    </script>
</body>
</html>
