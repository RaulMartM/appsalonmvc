<h1 class="nombre-pagina">Actualizar</h1>

<p class="descripcion-pagina">Actualización de Servicios</p>

<?php
    // include_once __DIR__. '/../templates/barra.php';
    include_once __DIR__ . '/../templates/alertas.php';
?>

<form method="POST" class="formulario">
    <?php include_once __DIR__ . '/formulario.php'; ?>
    <div class="acciones"></div>
    <div class="acciones">
        <a href="/servicios" class="boton-eliminar">Cancelar</a>
        <input type="submit" class="boton" value="Actualizar">
    </div>
    
    
</form>