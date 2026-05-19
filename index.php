<?php
require_once 'db.php';

// --- BACKEND: Procesar Creación de Orden ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['crear_orden'])) {
    $sql = "INSERT INTO ordenes (proveedor, email_contacto, producto_nombre, sku, cantidad, precio_unitario, fecha_entrega, prioridad) 
            VALUES (:proveedor, :email_contacto, :producto_nombre, :sku, :cantidad, :precio_unitario, :fecha_entrega, :prioridad)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':proveedor' => $_POST['proveedor'],
        ':email_contacto' => $_POST['email_contacto'],
        ':producto_nombre' => $_POST['producto_nombre'],
        ':sku' => $_POST['sku'],
        ':cantidad' => $_POST['cantidad'],
        ':precio_unitario' => $_POST['precio_unitario'],
        ':fecha_entrega' => $_POST['fecha_entrega'],
        ':prioridad' => $_POST['prioridad']
    ]);
    
    header("Location: index.php?mensaje=creado");
    exit;
}

// --- BACKEND: Procesar Eliminación de Orden ---
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM ordenes WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    
    header("Location: index.php?mensaje=eliminado");
    exit;
}

// --- BACKEND: Obtener Registros ---
$stmt = $pdo->query("SELECT * FROM ordenes ORDER BY fecha_creacion DESC");
$ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procurement Ledger | Nueva Solicitud</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f7f9fb; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 24px; }
        .section-icon { background-color: #e8f0fe; color: #0d6efd; padding: 10px; border-radius: 8px; font-size: 1.2rem; }
        .form-control, .form-select { border-radius: 6px; background-color: #f8fafc; border: 1px solid #e2e8f0; }
        .form-control:focus, .form-select:focus { box-shadow: none; border-color: #0d6efd; background-color: #fff; }
        label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; }
    </style>
</head>
<body>

<div class="container py-5 max-w-4xl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Nueva Solicitud de Compra</h2>
            <p class="text-muted small">Registra los detalles técnicos y financieros para la orden.</p>
        </div>
        <span class="badge bg-primary text-white p-2">Usuario: Luis</span>
    </div>

    <?php if(isset($_GET['mensaje'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
                echo $_GET['mensaje'] == 'creado' ? 'Orden creada exitosamente.' : 'Orden eliminada del registro.'; 
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php">
        
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center mb-4">
                <i class="bi bi-building section-icon me-3"></i>
                <h5 class="mb-0 fw-bold">Información del Proveedor</h5>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Proveedor</label>
                    <select name="proveedor" class="form-select" required>
                        <option value="">Seleccionar proveedor...</option>
                        <option value="Global Logistics Corp">Global Logistics Corp</option>
                        <option value="Precision Hardware Ltd">Precision Hardware Ltd</option>
                        <option value="Apex Industrial">Apex Industrial</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="email_contacto" class="form-control" placeholder="contacto@proveedor.com" required>
                </div>
            </div>
        </div>

        <div class="card card-custom p-4">
            <div class="d-flex align-items-center mb-4">
                <i class="bi bi-box-seam section-icon me-3"></i>
                <h5 class="mb-0 fw-bold">Detalle del Producto</h5>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre del Producto</label>
                    <input type="text" name="producto_nombre" class="form-control" placeholder="Ej: MacBook Pro M3" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">SKU / Código</label>
                    <input type="text" name="sku" class="form-control" placeholder="STR-09923-X" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Cantidad</label>
                    <input type="number" name="cantidad" id="cantidad" class="form-control" min="1" value="1" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Precio Unitario (USD)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">$</span>
                        <input type="number" step="0.01" name="precio_unitario" id="precio" class="form-control border-start-0 pl-0" placeholder="0.00" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom p-4">
            <div class="d-flex align-items-center mb-4">
                <i class="bi bi-truck section-icon me-3"></i>
                <h5 class="mb-0 fw-bold">Logística de Entrega</h5>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Fecha de Entrega Solicitada</label>
                    <input type="date" name="fecha_entrega" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Prioridad de Envío</label>
                    <select name="prioridad" class="form-select" required>
                        <option value="Estándar (5-7 días)">Estándar (5-7 días)</option>
                        <option value="Exprés (2-3 días)">Exprés (2-3 días)</option>
                        <option value="Urgente (24 hrs)">Urgente (24 hrs)</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-5">
            <button type="submit" name="crear_orden" class="btn btn-primary px-5 py-2 fw-bold">
                Enviar Solicitud <i class="bi bi-send ms-2"></i>
            </button>
        </div>
    </form>

    <h4 class="fw-bold mb-4">Historial de Órdenes</h4>
    <div class="card card-custom overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th>ID</th>
                        <th>Proveedor</th>
                        <th>Producto (SKU)</th>
                        <th>Cant.</th>
                        <th>Subtotal</th>
                        <th>Entrega</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($ordenes) > 0): ?>
                        <?php foreach($ordenes as $orden): ?>
                        <tr>
                            <td class="fw-bold text-primary">#<?= htmlspecialchars($orden['id']) ?></td>
                            <td>
                                <?= htmlspecialchars($orden['proveedor']) ?><br>
                                <small class="text-muted"><?= htmlspecialchars($orden['email_contacto']) ?></small>
                            </td>
                            <td>
                                <?= htmlspecialchars($orden['producto_nombre']) ?><br>
                                <small class="text-muted">SKU: <?= htmlspecialchars($orden['sku']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($orden['cantidad']) ?></td>
                            <td class="fw-bold">$<?= number_format($orden['subtotal'], 2) ?></td>
                            <td>
                                <?= htmlspecialchars($orden['fecha_entrega']) ?><br>
                                <span class="badge bg-secondary"><?= htmlspecialchars($orden['prioridad']) ?></span>
                            </td>
                            <td class="text-end">
                                <a href="index.php?eliminar=<?= $orden['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Estás seguro de eliminar esta orden?');">
                                    <i class="bi bi-trash3"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No hay órdenes registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>