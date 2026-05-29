<?php 
require_once '../app/views/includes/header.php'; 
require_once '../app/views/includes/sidebar.php'; 
?>

<div class="content-principal-contenedor">
    <div class="row g-4">
        
        <!-- COLUMNA IZQUIERDA: CATÁLOGO -->
        <div class="col-12 col-xl-7">
            <div class="card-erp h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-light flex-wrap gap-2">
                    <h5 class="mb-0 text-uppercase tracking-wider fs-6" style="color: #1E3A5F; font-weight: 600;">
                        <i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i> Catálogo de Operaciones
                    </h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-light text-primary border px-2 py-1 fw-bold" style="font-size: 11px;">
                            <?php echo count($productos); ?> Ítems
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="mostrarAtajos()" title="Atajos de teclado">
                            <i class="bi bi-keyboard"></i>
                        </button>
                    </div>
                </div>
                
                <!-- FILTRO RÁPIDO POR CATEGORÍA (DINÁMICO) -->
                <div class="mb-3">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-tags-fill text-primary" style="font-size: 14px;"></i>
                        <label class="text-muted fw-semibold small mb-0">Filtrar por categoría:</label>
                    </div>
                    <select id="selectCategoria" class="form-select form-select-sm" style="border-radius: 20px; background-color: #F8FAFC; border-color: #E2E8F0;">
                        <option value="todos">🏠 Todos los productos</option>
                        <?php foreach($categorias as $cat): ?>
                            <?php 
                            $icono = '📦';
                            $nombreCat = strtolower($cat['nombre']);
                            if(strpos($nombreCat, 'aceite') !== false) $icono = '🛢️';
                            elseif(strpos($nombreCat, 'filtro') !== false) $icono = '🔧';
                            elseif(strpos($nombreCat, 'aditivo') !== false) $icono = '🧪';
                            elseif(strpos($nombreCat, 'lubricante') !== false) $icono = '⚙️';
                            elseif(strpos($nombreCat, 'herramienta') !== false) $icono = '🔨';
                            elseif(strpos($nombreCat, 'liquido') !== false) $icono = '💧';
                            elseif(strpos($nombreCat, 'grasa') !== false) $icono = '🧈';
                            elseif(strpos($nombreCat, 'refrigerante') !== false) $icono = '❄️';
                            ?>
                            <option value="<?php echo htmlspecialchars($cat['nombre']); ?>">
                                <?php echo $icono; ?> <?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted" style="font-size: 10px;">
                        📋 <?php echo count($categorias); ?> categorías disponibles
                    </small>
                </div>
                
                <ul class="nav nav-pills nav-fill mb-3 bg-light p-1 rounded-3 border" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active text-uppercase fw-bold py-2" id="pills-productos-tab" data-bs-toggle="pill" data-bs-target="#pills-productos" type="button" role="tab">
                            <i class="bi bi-droplet-half me-2"></i>Aceites y Filtros
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-muted text-uppercase fw-bold py-2" id="pills-servicios-tab" data-bs-toggle="pill" data-bs-target="#pills-servicios" type="button" role="tab">
                            <i class="bi bi-wrench-adjustable me-2"></i>Mano de Obra
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="pills-tabContent">
                    <!-- TAB PRODUCTOS -->
                    <div class="tab-pane fade show active" id="pills-productos" role="tabpanel">
                        <form action="index.php" method="GET" class="mb-3" id="formBuscar">
                            <input type="hidden" name="route" value="nueva_venta">
                            <div class="input-group">
                                <input type="text" name="buscar" id="buscarInput" class="form-control-erp flex-grow-1" style="border-radius: 10px 0 0 10px;" placeholder="🔍 Buscar por viscosidad, marca o SKU... (F2 para enfocar)" value="<?php echo htmlspecialchars($_GET['buscar'] ?? ''); ?>">
                                <button class="btn btn-erp-primary px-3" style="border-radius: 0 10px 10px 0; background-color: #2563EB;" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>

                        <!-- CONTENEDOR DE PRODUCTOS CON PAGINACIÓN -->
                        <div id="productosGrid">
                            <div class="row g-2" id="productosContainer" style="max-height: 380px; overflow-y: auto; padding-right: 4px;">
                                <?php if(!empty($productos)): ?>
                                    <?php foreach($productos as $index => $prod): 
                                        $stockMinimo = $prod['stock_minimo'] ?? 5; // Valor por defecto si no existe
                                        $stockClass = ($prod['stock'] <= $stockMinimo) ? 'bg-danger' : (($prod['stock'] <= 10) ? 'bg-warning' : 'bg-light text-success border');
                                        $stockPorcentaje = $stockMinimo > 0 ? min(100, ($prod['stock'] / $stockMinimo) * 100) : 100;
                                        if($stockPorcentaje > 100) $stockPorcentaje = 100;
                                    ?>
                                        <div class="col-12 col-sm-6 col-lg-4 producto-item" data-categoria="<?php echo htmlspecialchars($prod['categoria'] ?? 'Otros'); ?>" data-index="<?php echo $index; ?>">
                                            <div class="card bg-white border border-light h-100 position-relative shadow-sm rounded-3 transition-all" style="border-radius: 12px !important;">
                                                <div class="card-body p-3 d-flex flex-column">
                                                    <span class="badge <?php echo $stockClass; ?> position-absolute top-0 end-0 m-2" style="font-size: 10px; font-weight: 600;">
                                                        <?php echo $prod['stock']; ?> disp.
                                                    </span>
                                                    <small class="text-uppercase text-muted fw-bold font-monospace" style="font-size: 10px;"><?php echo htmlspecialchars($prod['marca'] ?? 'Generico'); ?></small>
                                                    <h6 class="fw-bold text-dark mb-1 small text-truncate-2" style="min-height: 36px; line-height: 1.3; font-size: 13px; color: #1E3A5F !important;">
                                                        <?php echo htmlspecialchars($prod['nombre']); ?>
                                                    </h6>
                                                    
                                                    <!-- Barra de stock (solo si stock_minimo existe) -->
                                                    <?php if(isset($prod['stock_minimo']) && $prod['stock_minimo'] > 0): ?>
                                                    <div class="mb-2 mt-1">
                                                        <div class="progress" style="height: 4px;">
                                                            <div class="progress-bar <?php echo $prod['stock'] <= $stockMinimo ? 'bg-danger' : 'bg-success'; ?>" role="progressbar" style="width: <?php echo $stockPorcentaje; ?>%"></div>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>
                                                    
                                                    <p class="text-muted mb-2" style="font-size: 11px;"><i class="bi bi-box me-1"></i><?php echo htmlspecialchars($prod['unidad_medida'] ?? 'Unidad'); ?></p>
                                                    <div class="fw-bold text-dark mb-2" style="font-size: 18px;">S/ <?php echo number_format($prod['precio_venta'], 2); ?></div>
                                                    
                                                    <form action="index.php?route=agregar_carrito" method="POST" class="mt-auto" onsubmit="return validarStock(this, <?php echo $prod['stock']; ?>)">
                                                        <input type="hidden" name="id_producto" value="<?php echo $prod['id_producto']; ?>">
                                                        <input type="hidden" name="tipo_item" value="PRODUCTO">
                                                        <input type="hidden" name="precio" value="<?php echo $prod['precio_venta']; ?>">
                                                        <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($prod['nombre'] . ' [' . ($prod['marca'] ?? '') . ']'); ?>">
                                                        <div class="input-group input-group-sm">
                                                            <button type="button" class="btn btn-outline-secondary btn-cantidad-menos" style="border-radius: 8px 0 0 8px;">-</button>
                                                            <input type="number" name="cantidad" value="1" min="1" max="<?php echo $prod['stock']; ?>" class="form-control form-control-erp text-center fw-bold text-dark border-start-0 border-end-0 py-1 cantidad-input" style="font-size: 13px; max-width: 50px;">
                                                            <button type="button" class="btn btn-outline-secondary btn-cantidad-mas" style="border-radius: 0 8px 8px 0;">+</button>
                                                            <button type="submit" class="btn btn-erp-primary text-white fw-bold" style="border-radius: 8px; background-color: #2563EB; font-size: 12px; margin-left: 5px;">
                                                                <i class="bi bi-plus-lg me-1"></i>
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12 text-center py-5 text-muted small">
                                        <i class="bi bi-droplet-x fs-2 opacity-25 d-block mb-2"></i>
                                        No se hallaron insumos con la coincidencia buscada.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- PAGINACIÓN -->
                        <?php 
                        $totalProductos = count($productos);
                        $itemsPorPagina = 12;
                        $totalPaginas = ceil($totalProductos / $itemsPorPagina);
                        ?>
                        <?php if($totalPaginas > 1): ?>
                        <div class="mt-3 d-flex justify-content-center">
                            <nav>
                                <ul class="pagination pagination-sm" id="paginacionProductos">
                                    <li class="page-item disabled" id="prevPageBtn"><a class="page-link" href="#">Anterior</a></li>
                                    <?php for($i = 1; $i <= $totalPaginas; $i++): ?>
                                        <li class="page-item <?php echo $i == 1 ? 'active' : ''; ?>" data-page="<?php echo $i; ?>">
                                            <a class="page-link" href="#"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item" id="nextPageBtn"><a class="page-link" href="#">Siguiente</a></li>
                                </ul>
                            </nav>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB SERVICIOS -->
                    <div class="tab-pane fade" id="pills-servicios" role="tabpanel">
                        <div class="row g-2 overflow-auto" style="max-height: 440px; padding-right: 4px;">
                            <?php if(!empty($serviciosManoObra)): ?>
                                <?php foreach($serviciosManoObra as $ser): ?>
                                    <div class="col-12 col-md-6">
                                        <div class="card bg-white border border-light h-100 shadow-sm" style="border-radius: 12px !important;">
                                            <div class="card-body p-3 d-flex flex-column">
                                                <h6 class="fw-bold mb-1 text-truncate" style="color: #1E3A5F; font-size: 13.5px;">
                                                    <i class="bi bi-tools text-primary me-2" style="font-size: 12px;"></i><?php echo htmlspecialchars($ser['nombre']); ?>
                                                </h6>
                                                <p class="text-muted mb-2 text-truncate-2" style="font-size: 11.5px; min-height: 32px; line-height: 1.3;">
                                                    <?php echo htmlspecialchars($ser['descripcion'] ?: 'Operación estándar ejecutada por el equipo mecánico.'); ?>
                                                </p>
                                                <div class="fw-bold text-dark mb-2" style="font-size: 18px;">S/ <?php echo number_format($ser['precio'], 2); ?></div>
                                                
                                                <form action="index.php?route=agregar_carrito" method="POST" class="mt-auto">
                                                    <input type="hidden" name="id_producto" value="<?php echo $ser['id_servicio']; ?>">
                                                    <input type="hidden" name="tipo_item" value="SERVICIO">
                                                    <input type="hidden" name="precio" value="<?php echo $ser['precio']; ?>">
                                                    <input type="hidden" name="nombre" value="🔧 <?php echo htmlspecialchars($ser['nombre']); ?>">
                                                    <input type="hidden" name="cantidad" value="1">
                                                    <button type="submit" class="btn btn-sm btn-outline-primary w-100 fw-semibold rounded-2 py-2" style="font-size: 12px;">
                                                        <i class="bi bi-plus-circle me-1"></i> Añadir Servicio
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12 text-center py-5 text-muted">
                                    <i class="bi bi-tools fs-2 opacity-25 d-block mb-2"></i>
                                    No hay servicios registrados
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: CARRITO (mantiene igual) -->
        <div class="col-12 col-xl-5">
            <div class="card-erp h-100 d-flex flex-column" style="border-left: 4px solid #10B981;">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-light">
                    <h5 class="mb-0 text-uppercase tracking-wider fs-6" style="color: #10B981; font-weight: 600;">
                        <i class="bi bi-receipt me-2"></i> Resumen de Liquidación
                        <span id="itemCount" class="badge bg-primary ms-2"><?php echo count($_SESSION['carrito'] ?? []); ?></span>
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-danger px-3 rounded-pill fw-medium" style="font-size: 11px;" onclick="confirmarVaciarCola()">
                        <i class="bi bi-trash3 me-1"></i>Vaciar
                    </button>
                </div>
                
                <div class="overflow-auto border rounded-3 mb-3 bg-light shadow-inner" style="max-height: 250px;">
                    <table class="table align-middle mb-0 small" style="font-size: 12.5px;">
                        <thead class="bg-white text-muted sticky-top">
                            <tr class="border-bottom">
                                <th class="ps-3 py-2 text-center" width="50">Cant.</th>
                                <th class="py-2">Detalle</th>
                                <th class="text-end py-2" width="90">Subtotal</th>
                                <th class="py-2" width="40"></th>
                            </tr>
                        </thead>
                        <tbody id="carritoBody">
                            <?php if(!empty($_SESSION['carrito'])): ?>
                                <?php foreach($_SESSION['carrito'] as $idx => $item): ?>
                                    <tr class="bg-white border-bottom border-light">
                                        <td class="text-center ps-2">
                                            <span class="badge bg-primary text-white fw-bold px-2 py-1" style="font-size: 11px; border-radius: 4px;">
                                                <?php echo $item['cantidad']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark" style="font-size: 12.5px; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo $item['nombre']; ?></div>
                                            <small class="text-muted">S/ <?php echo number_format($item['precio'], 2); ?> c/u</small>
                                        </td>
                                        <td class="text-end fw-bold text-dark">S/ <?php echo number_format($item['subtotal'], 2); ?></td>
                                        <td class="text-center">
                                            <a href="index.php?route=quitar_carrito&idx=<?php echo $idx; ?>" class="text-danger" style="font-size: 14px;"><i class="bi bi-trash-fill"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr id="carritoVacio">
                                    <td colspan="4" class="text-center py-5 text-muted small fw-medium">
                                        <i class="bi bi-cart-x fs-3 text-muted opacity-25 d-block mb-1"></i>
                                        Carrito vacío
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <form action="index.php?route=finalizar_venta" method="POST" autocomplete="off" id="formFinalizarVenta">
                    <input type="hidden" id="subtotal_hidden" value="<?php echo $total_venta; ?>">

                    <div class="mb-3">
                        <label class="label-erp fw-bold">1. Cliente <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-person-badge"></i></span>
                            <input type="text" id="cliente_buscar" class="form-control form-control-erp" 
                                placeholder="Escriba nombre o DNI..." autocomplete="off">
                            <input type="hidden" name="id_cliente" id="id_cliente">
                            <button type="button" class="btn btn-outline-secondary" onclick="limpiarSeleccionCliente()">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <div id="sugerencias_clientes" class="list-group position-absolute shadow-sm" style="display:none; z-index:1050; max-height:250px; overflow-y:auto; background:white; border-radius:8px;"></div>
                    </div>

                    <div class="card p-3 border border-light mb-3" style="background-color: #F8FAFC; border-radius: 12px;">
                        <h6 class="fw-bold text-dark text-uppercase tracking-wider mb-2" style="font-size: 11px;">
                            <i class="bi bi-car-front-fill text-primary me-2"></i>Datos del Servicio
                        </h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="text-muted" style="font-size: 11px;">Vehículo</label>
                                <select name="id_vehiculo" id="id_vehiculo" class="form-select form-select-sm">
                                    <option value="">-- Sin vehículo --</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="text-muted" style="font-size: 11px;">Kilometraje</label>
                                <input type="number" name="km_actual" id="km_actual" class="form-control form-control-sm" placeholder="Ej: 84200">
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-5">
                            <label class="text-muted small">Comprobante</label>
                            <select name="tipo_comprobante" class="form-select form-select-sm">
                                <option value="03">Boleta</option>
                                <option value="01">Factura</option>
                            </select>
                        </div>
                        <div class="col-5">
                            <label class="text-muted small">Método Pago</label>
                            <select name="metodo_pago" class="form-select form-select-sm" id="metodo_pago">
                                <option value="EFECTIVO">💵 Efectivo</option>
                                <option value="YAPE">📱 Yape / Plin</option>
                                <option value="TARJETA">💳 Tarjeta</option>
                            </select>
                        </div>
                        <div class="col-2">
                            <label class="text-danger small">Dscto.</label>
                            <input type="number" name="descuento" id="input_descuento" value="0" min="0" step="1" class="form-control form-control-sm text-center text-danger">
                        </div>
                    </div>

                    <div class="alert alert-success bg-light border-success text-center py-2 mb-3" style="border-radius: 12px;">
                        <small class="text-muted text-uppercase fw-bold">Total a Pagar</small>
                        <h2 class="fw-bold text-success mb-0" id="total_display" style="font-size: 32px;">S/ <?php echo number_format($total_venta, 2); ?></h2>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary flex-grow-1" onclick="confirmarVaciarCola()">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-erp-success flex-grow-1 fw-bold" style="background-color: #10B981;" <?php echo empty($_SESSION['carrito']) ? 'disabled' : ''; ?> id="btnFinalizar">
                            <i class="bi bi-cash-coin"></i> Cobrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ==================== VARIABLES GLOBALES ====================
let listaClientes = <?php echo json_encode($clientes); ?>;
let productos = <?php echo json_encode($productos); ?>;
let itemsPorPagina = 12;
let paginaActual = 1;
let productosFiltradosGlobal = null;

// ==================== FUNCIÓN MOSTRAR ATAJOS ====================
function mostrarAtajos() {
    Swal.fire({
        title: '⌨️ Atajos de teclado',
        html: `
            <div class="text-start" style="font-family: 'Segoe UI', sans-serif;">
                <div class="mb-3 p-2 rounded" style="background-color: #F8FAFC; border-left: 4px solid #2563EB;">
                    <p class="mb-1"><kbd style="background-color: #1E3A5F; color: white; padding: 4px 10px; border-radius: 6px; font-weight: bold;">F2</kbd> 
                    <span style="color: #334155;">→ Enfocar búsqueda de productos</span></p>
                </div>
                <div class="mb-3 p-2 rounded" style="background-color: #F8FAFC; border-left: 4px solid #10B981;">
                    <p class="mb-1"><kbd style="background-color: #1E3A5F; color: white; padding: 4px 10px; border-radius: 6px; font-weight: bold;">Ctrl + F5</kbd> 
                    <span style="color: #334155;">→ Finalizar venta (cobrar)</span></p>
                </div>
                <div class="mb-3 p-2 rounded" style="background-color: #F8FAFC; border-left: 4px solid #F59E0B;">
                    <p class="mb-1"><kbd style="background-color: #1E3A5F; color: white; padding: 4px 10px; border-radius: 6px; font-weight: bold;">+ / -</kbd> 
                    <span style="color: #334155;">→ Ajustar cantidades</span></p>
                </div>
                <div class="mb-3 p-2 rounded" style="background-color: #F8FAFC; border-left: 4px solid #EF4444;">
                    <p class="mb-1"><kbd style="background-color: #1E3A5F; color: white; padding: 4px 10px; border-radius: 6px; font-weight: bold;">ESC</kbd> 
                    <span style="color: #334155;">→ Cerrar ventanas emergentes</span></p>
                </div>
            </div>
        `,
        icon: 'info',
        confirmButtonText: '✓ Entendido',
        confirmButtonColor: '#2563EB',
        background: '#FFFFFF',
        borderRadius: '16px',
        customClass: {
            popup: 'shadow-lg',
            title: 'fw-bold text-dark'
        }
    });
}

// ==================== FUNCIÓN DE PAGINACIÓN ====================
function mostrarPagina(pagina) {
    paginaActual = pagina;
    const productosAMostrar = productosFiltradosGlobal || productos;
    const totalItems = productosAMostrar.length;
    const totalPaginasCalc = Math.ceil(totalItems / itemsPorPagina);
    
    const inicio = (pagina - 1) * itemsPorPagina;
    const fin = inicio + itemsPorPagina;
    const productosPagina = productosAMostrar.slice(inicio, fin);
    
    const container = document.getElementById('productosContainer');
    if(!container) return;
    
    container.innerHTML = '';
    
    if(productosPagina.length === 0) {
        container.innerHTML = '<div class="col-12 text-center py-5 text-muted small">No hay productos en esta categoría</div>';
        actualizarPaginacion(totalPaginasCalc, pagina);
        return;
    }
    
    productosPagina.forEach((prod) => {
        const stockMinimo = prod.stock_minimo ?? 5;
        const stockClass = (prod.stock <= stockMinimo) ? 'bg-danger' : ((prod.stock <= 10) ? 'bg-warning' : 'bg-light text-success border');
        let stockPorcentaje = stockMinimo > 0 ? Math.min(100, (prod.stock / stockMinimo) * 100) : 100;
        if(stockPorcentaje > 100) stockPorcentaje = 100;
        
        const productoHtml = `
            <div class="col-12 col-sm-6 col-lg-4 producto-item" data-categoria="${prod.categoria || 'Otros'}">
                <div class="card bg-white border border-light h-100 position-relative shadow-sm rounded-3" style="border-radius: 12px !important;">
                    <div class="card-body p-3 d-flex flex-column">
                        <span class="badge ${stockClass} position-absolute top-0 end-0 m-2" style="font-size: 10px;">
                            ${prod.stock} disp.
                        </span>
                        <small class="text-uppercase text-muted fw-bold font-monospace" style="font-size: 10px;">${prod.marca || 'Generico'}</small>
                        <h6 class="fw-bold text-dark mb-1 small" style="min-height: 36px; font-size: 13px;">${escapeHtml(prod.nombre)}</h6>
                        ${prod.stock_minimo ? `<div class="mb-2 mt-1"><div class="progress" style="height: 4px;"><div class="progress-bar ${prod.stock <= stockMinimo ? 'bg-danger' : 'bg-success'}" style="width: ${stockPorcentaje}%"></div></div></div>` : ''}
                        <p class="text-muted mb-2" style="font-size: 11px;"><i class="bi bi-box me-1"></i>${prod.unidad_medida || 'Unidad'}</p>
                        <div class="fw-bold text-dark mb-2" style="font-size: 18px;">S/ ${parseFloat(prod.precio_venta).toFixed(2)}</div>
                        <form action="index.php?route=agregar_carrito" method="POST" class="mt-auto" onsubmit="return validarStock(this, ${prod.stock})">
                            <input type="hidden" name="id_producto" value="${prod.id_producto}">
                            <input type="hidden" name="tipo_item" value="PRODUCTO">
                            <input type="hidden" name="precio" value="${prod.precio_venta}">
                            <input type="hidden" name="nombre" value="${escapeHtml(prod.nombre)} [${prod.marca || ''}]">
                            <div class="input-group input-group-sm">
                                <button type="button" class="btn btn-outline-secondary btn-cantidad-menos" style="border-radius: 8px 0 0 8px;">-</button>
                                <input type="number" name="cantidad" value="1" min="1" max="${prod.stock}" class="form-control text-center fw-bold border-start-0 border-end-0 py-1 cantidad-input" style="font-size: 13px; max-width: 50px;">
                                <button type="button" class="btn btn-outline-secondary btn-cantidad-mas" style="border-radius: 0 8px 8px 0;">+</button>
                                <button type="submit" class="btn text-white fw-bold" style="border-radius: 8px; background-color: #2563EB; font-size: 12px; margin-left: 5px;">
                                    <i class="bi bi-plus-lg me-1"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
        container.innerHTML += productoHtml;
    });
    
    actualizarPaginacion(totalPaginasCalc, pagina);
    reasignarEventosCantidad();
}

function actualizarPaginacion(totalPaginas, paginaActualNum) {
    const paginacionUl = document.querySelector('#paginacionProductos');
    if(!paginacionUl) return;
    
    const prevBtn = document.getElementById('prevPageBtn');
    const nextBtn = document.getElementById('nextPageBtn');
    
    // Eliminar items viejos
    const itemsAEliminar = [];
    paginacionUl.querySelectorAll('.page-item').forEach(item => {
        if(item.id !== 'prevPageBtn' && item.id !== 'nextPageBtn') {
            itemsAEliminar.push(item);
        }
    });
    itemsAEliminar.forEach(item => item.remove());
    
    // Crear nuevos items
    for(let i = 1; i <= totalPaginas; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === paginaActualNum ? 'active' : ''}`;
        li.dataset.page = i;
        li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
        li.addEventListener('click', function(e) {
            e.preventDefault();
            const pagina = parseInt(this.dataset.page);
            if(!isNaN(pagina)) mostrarPagina(pagina);
        });
        paginacionUl.insertBefore(li, nextBtn);
    }
    
    if(prevBtn) {
        if(paginaActualNum === 1) prevBtn.classList.add('disabled');
        else prevBtn.classList.remove('disabled');
    }
    if(nextBtn) {
        if(paginaActualNum === totalPaginas) nextBtn.classList.add('disabled');
        else nextBtn.classList.remove('disabled');
    }
}

function reasignarEventosCantidad() {
    document.querySelectorAll('.btn-cantidad-menos').forEach(btn => {
        btn.onclick = function() {
            let input = this.parentElement.querySelector('.cantidad-input');
            let val = parseInt(input.value) || 1;
            if(val > 1) input.value = val - 1;
        };
    });
    document.querySelectorAll('.btn-cantidad-mas').forEach(btn => {
        btn.onclick = function() {
            let input = this.parentElement.querySelector('.cantidad-input');
            let max = parseInt(input.max) || 999;
            let val = parseInt(input.value) || 0;
            if(val < max) input.value = val + 1;
        };
    });
}

function escapeHtml(str) {
    if(!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if(m === '&') return '&amp;';
        if(m === '<') return '&lt;';
        if(m === '>') return '&gt;';
        return m;
    });
}

// ==================== FILTRO POR CATEGORÍA ====================
function filtrarPorCategoria(categoria) {
    if(categoria === 'todos') {
        productosFiltradosGlobal = null;
    } else {
        productosFiltradosGlobal = productos.filter(p => (p.categoria || 'Otros') === categoria);
    }
    paginaActual = 1;
    mostrarPagina(1);
}

// ==================== INICIALIZACIÓN ====================
document.addEventListener("DOMContentLoaded", function() {
    if(productos.length > 0) mostrarPagina(1);
    
    // Botones anterior/siguiente
    const prevBtn = document.getElementById('prevPageBtn');
    const nextBtn = document.getElementById('nextPageBtn');
    if(prevBtn) {
        prevBtn.onclick = function(e) {
            e.preventDefault();
            const productosAMostrar = productosFiltradosGlobal || productos;
            const totalPaginasCalc = Math.ceil(productosAMostrar.length / itemsPorPagina);
            if(paginaActual > 1) mostrarPagina(paginaActual - 1);
        };
    }
    if(nextBtn) {
        nextBtn.onclick = function(e) {
            e.preventDefault();
            const productosAMostrar = productosFiltradosGlobal || productos;
            const totalPaginasCalc = Math.ceil(productosAMostrar.length / itemsPorPagina);
            if(paginaActual < totalPaginasCalc) mostrarPagina(paginaActual + 1);
        };
    }
    
    // Preseleccionar cliente por defecto
    const clienteDefault = listaClientes.find(c => c.numero_documento === '00000000' || (c.nombre && c.nombre.toUpperCase().includes('PUBLICO')));
    if(clienteDefault) {
        const clienteBuscar = document.getElementById('cliente_buscar');
        const idCliente = document.getElementById('id_cliente');
        if(clienteBuscar) clienteBuscar.value = clienteDefault.nombre + ' [' + clienteDefault.numero_documento + ']';
        if(idCliente) idCliente.value = clienteDefault.id_cliente;
        cargarVehiculosDelCliente();
    }
    
    // Atajo F2
    document.addEventListener('keydown', function(e) {
        if(e.key === 'F2') {
            e.preventDefault();
            const buscarInput = document.getElementById('buscarInput');
            if(buscarInput) buscarInput.focus();
        }
    });
    
    // Eventos de filtro
    document.querySelectorAll('.filtro-categoria').forEach(btn => {
        btn.onclick = function() {
            document.querySelectorAll('.filtro-categoria').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            filtrarPorCategoria(this.dataset.categoria);
        };
    });
});

// ==================== FUNCIONES VARIAS ====================
function validarStock(form, stockMaximo) {
    const cantidad = form.querySelector('input[name="cantidad"]').value;
    if(parseInt(cantidad) > stockMaximo) {
        Swal.fire('Stock insuficiente', `Solo hay ${stockMaximo} unidades disponibles`, 'warning');
        return false;
    }
    return true;
}

function cargarVehiculosDelCliente() {
    const idCliente = document.getElementById('id_cliente').value;
    const selectVehiculo = document.getElementById('id_vehiculo');
    if(!idCliente || !selectVehiculo) return;
    selectVehiculo.innerHTML = '<option value="">⏳ Cargando...</option>';
    fetch(`index.php?route=buscar_vehiculos_cliente&id_cliente=${idCliente}`)
        .then(res => res.json())
        .then(autos => {
            selectVehiculo.innerHTML = '<option value="">-- Sin vehículo --</option>';
            if(autos.length > 0) {
                autos.forEach(auto => {
                    selectVehiculo.innerHTML += `<option value="${auto.id_vehicle}">🚗 ${auto.placa} (${auto.marca} ${auto.modelo}) - ${auto.kilometraje} km</option>`;
                });
            }
        })
        .catch(err => {
            console.error("Error:", err);
            selectVehiculo.innerHTML = '<option value="">-- Error --</option>';
        });
}

function calcularTotalLiquidado() {
    const subtotal = parseFloat(document.getElementById('subtotal_hidden').value) || 0;
    const descuento = parseFloat(document.getElementById('input_descuento').value) || 0;
    let total = subtotal - descuento;
    if(total < 0) total = 0;
    const totalDisplay = document.getElementById('total_display');
    if(totalDisplay) totalDisplay.innerHTML = "S/ " + total.toFixed(2);
}

const inputDescuento = document.getElementById('input_descuento');
if(inputDescuento) inputDescuento.addEventListener('input', calcularTotalLiquidado);

function confirmarVaciarCola() {
    const itemCount = <?php echo count($_SESSION['carrito'] ?? []); ?>;
    if(itemCount === 0) return;
    Swal.fire({
        title: '¿Vaciar carrito?',
        text: 'Se perderán todos los items agregados',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        confirmButtonText: 'Sí, vaciar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) window.location.href = "index.php?route=limpiar_carrito";
    });
}

// Reemplaza TODO el event listener del formFinalizar
const formFinalizar = document.getElementById('formFinalizarVenta');
if (formFinalizar) {
    formFinalizar.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const totalCarrito = <?php echo $total_venta; ?>;
        if (totalCarrito <= 0) {
            Swal.fire('Error', 'No hay items en el carrito', 'warning');
            return;
        }
        
        Swal.fire({
            title: 'Procesando venta...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        const formData = new FormData(formFinalizar);
        
        fetch('index.php?route=finalizar_venta', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                Swal.fire({
                    title: '✅ ¡Venta exitosa!',
                    html: `
                        <div class="text-start">
                            <p><strong>ID Venta:</strong> #${data.id_venta}</p>
                            <p><strong>Total:</strong> S/ ${totalCarrito.toFixed(2)}</p>
                        </div>
                    `,
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: '🖨️ Ver Ticket',
                    cancelButtonText: '🏠 Continuar vendiendo'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Abrir ticket en nueva pestaña
                        window.open('index.php?route=ver_ticket&id=' + data.id_venta, '_blank');
                    }
                    // Recargar la página de venta (limpia el carrito)
                    window.location.href = 'index.php?route=nueva_venta';
                });
            } else {
                Swal.fire('Error', data.error || 'Error al procesar venta', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error de conexión', error.message, 'error');
        });
    });
}

// Buscador de clientes
const buscadorCliente = document.getElementById('cliente_buscar');
const sugerenciasDiv = document.getElementById('sugerencias_clientes');
const inputIdCliente = document.getElementById('id_cliente');

if(buscadorCliente && sugerenciasDiv && inputIdCliente) {
    buscadorCliente.addEventListener('input', function() {
        const term = this.value.toLowerCase();
        if(term.length < 2) {
            sugerenciasDiv.style.display = 'none';
            return;
        }
        const filtrados = listaClientes.filter(c => 
            (c.nombre && c.nombre.toLowerCase().includes(term)) || 
            (c.numero_documento && c.numero_documento.includes(term))
        ).slice(0, 8);
        if(filtrados.length > 0) {
            sugerenciasDiv.innerHTML = filtrados.map(c => 
                `<a href="#" class="list-group-item list-group-item-action py-2" 
                    data-id="${c.id_cliente}"
                    data-nombre="${c.nombre} [${c.numero_documento}]">
                    <div class="fw-bold">${escapeHtml(c.nombre)}</div>
                    <small class="text-muted">${c.numero_documento || 'Sin DNI'}</small>
                </a>`
            ).join('');
            sugerenciasDiv.style.display = 'block';
        } else {
            sugerenciasDiv.style.display = 'none';
        }
    });
    
    sugerenciasDiv.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if(link) {
            e.preventDefault();
            inputIdCliente.value = link.dataset.id;
            buscadorCliente.value = link.dataset.nombre;
            sugerenciasDiv.style.display = 'none';
            cargarVehiculosDelCliente();
        }
    });
}

function limpiarSeleccionCliente() {
    const buscador = document.getElementById('cliente_buscar');
    const inputId = document.getElementById('id_cliente');
    const sugerencias = document.getElementById('sugerencias_clientes');
    if(buscador) buscador.value = '';
    if(inputId) inputId.value = '';
    if(sugerencias) sugerencias.style.display = 'none';
}
const selectCategoria = document.getElementById('selectCategoria');
if(selectCategoria) {
    selectCategoria.addEventListener('change', function() {
        const categoria = this.value;
        
        // Actualizar visualmente los botones (opcional, para mantener coherencia)
        document.querySelectorAll('.filtro-categoria').forEach(btn => {
            btn.classList.remove('active');
            if(btn.dataset.categoria === categoria) {
                btn.classList.add('active');
            }
        });
        
        // Aplicar filtro
        if(categoria === 'todos') {
            productosFiltradosGlobal = null;
        } else {
            productosFiltradosGlobal = productos.filter(p => (p.categoria || 'Otros') === categoria);
        }
        paginaActual = 1;
        mostrarPagina(1);
    });
}


document.getElementById('selectCategoria').addEventListener('change', function() {
    filtrarPorCategoria(this.value);
});

</script>

<style>
.hover-primary:hover { color: #2563EB !important; }
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
/* Estilo para teclas */
kbd {
    background-color: #1E3A5F !important;
    color: white !important;
    padding: 4px 10px !important;
    border-radius: 6px !important;
    font-weight: bold !important;
    font-family: monospace !important;
    font-size: 12px !important;
    display: inline-block !important;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;
}
.filtro-categoria.active {
    background-color: #2563EB;
    color: white;
    border-color: #2563EB;
}
/* Scroll horizontal para categorías */
.categorias-scroll-container {
    scrollbar-width: thin;
    scrollbar-color: #2563EB #E2E8F0;
}

.categorias-scroll-container::-webkit-scrollbar {
    height: 6px;
}

.categorias-scroll-container::-webkit-scrollbar-track {
    background: #E2E8F0;
    border-radius: 10px;
}

.categorias-scroll-container::-webkit-scrollbar-thumb {
    background: #2563EB;
    border-radius: 10px;
}

.categorias-scroll-container::-webkit-scrollbar-thumb:hover {
    background: #1E3A5F;
}

/* Botones de categorías en desktop/tablet/móvil */
@media (max-width: 768px) {
    .filtro-categoria {
        font-size: 11px !important;
        padding: 4px 10px !important;
    }
}
#selectCategoria {
    cursor: pointer;
    transition: all 0.2s ease;
}

#selectCategoria:hover {
    border-color: #2563EB;
    background-color: #EFF6FF;
}

#selectCategoria:focus {
    border-color: #2563EB;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}
</style>

<?php 
require_once '../app/views/includes/footer.php'; 
?>