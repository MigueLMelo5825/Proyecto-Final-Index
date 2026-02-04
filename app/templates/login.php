<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    
    <link rel="stylesheet" href="../../web/css/styleLogin.css">
</head>
<body>

<div class="login">
    <div class="login-container">
        <h2>Iniciar sesión</h2>

<form id="loginForm" method="POST" action="index.php?ctl=login">


            
            <label> Email o usuario
                <input type="text" id="email" name="email" placeholder="Email" required>
                <p id="errorUsuario" class="error"></p>
            </label> 
           
            <label> Contraseña
                <input type="password" id="password" name="password">
                <p id="errorPassword" class="error"></p>
            </label>
           
            <button type="submit">Entrar</button>
        </form>

        <div class="login-footer">
            <p>¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
            <p>¿Olvidaste la contraseña? <a href="recupero.php">Recuperar la contraseña</a></p>
        </div>
    </div>
</div>

<script src="../../web/js/validacionLogin.js"></script>

</body>
</html>
