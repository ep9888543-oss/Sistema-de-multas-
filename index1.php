<?php
function calcularMultaBiblioteca(int $cantidadLibros, int $diasRetraso, bool $aplicarDescuento = false): array {
    $multaBasePorLibro = 5.00;
    $recargoPorDiaPorLibro = 2.00;
    $porcentajeDescuento = 0.20; 
    $montoDescuento = 0.00;
    $descuentoAplicado = false;

    $multaBase = $multaBasePorLibro * $cantidadLibros;
    $recargo = $recargoPorDiaPorLibro * $diasRetraso * $cantidadLibros;
    
    $totalSinDescuento = $multaBase + $recargo;
    $totalFinal = $totalSinDescuento;

    if ($diasRetraso === 0 && $aplicarDescuento) {
        $montoDescuento = $totalSinDescuento * $porcentajeDescuento;
        $totalFinal = $totalSinDescuento - $montoDescuento;
        $descuentoAplicado = true;
    }

    return [
        'total' => $totalFinal,
        'base' => $multaBase,
        'recargo' => $recargo,
        'sin_descuento' => $totalSinDescuento,
        'monto_descuento' => $montoDescuento,
        'descuento_aplicado' => $descuentoAplicado
    ];
}

$resultado = null;
$libros = isset($_POST['libros']) ? (int)$_POST['libros'] : 0;
$dias = isset($_POST['dias']) ? (int)$_POST['dias'] : 0;
$descuentoChecked = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $aplicarDescuento = isset($_POST['aplicar_descuento']);
    
    if ($aplicarDescuento) {
        $descuentoChecked = 'checked';
    }

    if ($libros >= 0 && $dias >= 0) {
        $resultado = calcularMultaBiblioteca($libros, $dias, $aplicarDescuento);
    } else {
        $error = "Error: Los valores deben ser números no negativos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Multas Sencillo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .contenedor { max-width: 400px; padding: 20px; border: 1px solid #ccc; background-color: #f9f9f9; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="number"] { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        .botones { margin-top: 20px; display: flex; justify-content: space-between; gap: 10px; }
        button { padding: 10px 15px; border: none; cursor: pointer; flex-grow: 1; }
        .boton-calcular { background-color: #007bff; color: white; }
        .boton-limpiar { background-color: #0ca984ff; color: white; }
        .resultado p { margin: 5px 0; }
        .total { font-size: 1.2em; font-weight: bold; color: #dc3545; border-top: 1px solid #ccc; padding-top: 10px; margin-top: 10px; }
        .descuento-mensaje { color: green; font-weight: bold; margin-bottom: 5px; }
    </style>
</head>
<body>

<div class="contenedor">
    <h2>Sistema de Multas de Biblioteca </h2>
    
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="libros">Cantidad de Libros Atrasados:</label>
        <input type="number" id="libros" name="libros" value="<?php echo htmlspecialchars($libros); ?>" required min="0">

        <label for="dias">Días de Retraso:</label>
        <input type="number" id="dias" name="dias" value="<?php echo htmlspecialchars($dias); ?>" required min="0">
        
        <div style="margin-top: 15px;">
            <input type="checkbox" name="aplicar_descuento" id="aplicarDescuento" 
                <?php echo $descuentoChecked; ?> 
                <?php echo ($dias !== 0) ? 'disabled' : ''; ?>
                value="true">
            <label for="aplicarDescuento" style="display: inline;">
                Aplicar **20% de Descuento** (Solo si Días de Retraso es 0) 
            </label>
        </div>
        
        <div class="botones">
            <button type="submit" class="boton-calcular">Calcular Multa</button>
            
            <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="boton-limpiar">Limpiar 🔄</a>
        </div>
    </form>

    <?php if ($resultado !== null): ?>
        <hr>
        <h3>Detalles del Cálculo</h3>
        <div class="resultado">
            <p>Multa Base ($5 x <?php echo $libros; ?>): $<?php echo number_format($resultado['base'], 2); ?></p>
            <p>Recargo ($2 x <?php echo $libros; ?> x <?php echo $dias; ?> días): $<?php echo number_format($resultado['recargo'], 2); ?></p>
            <p>Subtotal (Base + Recargo): $<?php echo number_format($resultado['sin_descuento'], 2); ?></p>

            <?php if ($resultado['descuento_aplicado']): ?>
                <p class="descuento-mensaje">Descuento aplicado (20%): - $<?php echo number_format($resultado['monto_descuento'], 2); ?></p>
            <?php endif; ?>
            
            <p class="total">TOTAL NETO A PAGAR: $<?php echo number_format($resultado['total'], 2); ?></p>
        </div>
    <?php endif; ?>

</div>
</body>
</html>