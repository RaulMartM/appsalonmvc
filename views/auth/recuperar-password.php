<h1 class="nombre-pagina">Reestablece tu password</h1>
<p class="descripcion-pagina">Reestablece tu password a continuación</p>

<?php include_once __DIR__ . "/../templates/alertas.php" ?>

<?php if($error) return; ?>

<form class="formulario" method="POST"> 
    <div class="campo">
        <label for="password">Password</label>
        <input type="password" id="password" placeholder="Ingresa tu nuevo password" name="password" >
    </div>

    <input type="submit" class="boton" value="Actualizar password">

</form>

<div class="acciones">
    <a href="/crear-cuenta">¿Aún no tienes una cuenta? Crea una aquí</a>
    <a href="/">¿Ya tienes una cuenta? Inicia Sesión</a>
</div>