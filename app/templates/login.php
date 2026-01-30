<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>INDEX Login</title>
    


<link rel="stylesheet" href="../../web/css/styleLogin.css">

<script src=""></script>
</head>
<body>




<div class="login">
    <div class="login-container">
        <h2>Iniciar sesión</h2>

        <form id="loginForm">
            <label> Usuario o email
                <input type="text" id="usuario" name="usuario" placeholder="Usuario">
                <p id="errorUsuario" class="error"></p>
            </label> 
           
            <label> Contraseña
                <input type="password" id="password" name="password" placeholder="Contraseña">
                <p id="errorPassword" class="error"></p>
            </label>
           
            <button type="submit">Entrar</button>
        </form>

        <div class="login-footer">
            <p>¿No tienes cuenta? <a href="registro.php">Regístrate</a></p> <!-- poner enlace a index-->
            <p>¿Olvidaste la contraseña? <a href="recupero.php">Recuperar la contraseña</a></p> <!-- poner enlace a index-->
        </div>
    </div>
</div>



<script src="web/js/validacionLogin.js"></script>


</style>

</body>
</html>
