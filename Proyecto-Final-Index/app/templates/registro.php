
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Registro</title>


  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="../../web/css/styleRegistro.css">
</head>


<body>


  <div class="container">
    <h1>Crear cuenta</h1>


    <form id="form" method="POST" action="../../index.php?ctl=registro">


      <div>
        <label for="name">Nombre</label>
        <input type="text" id="name" name="name" placeholder="Tu nombre" required />
      </div>


      <div>
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required />
      </div>




  <div>
        <label for="pais">Nacionalidad</label>
        <select id="pais" name="pais" required>
            <option value="">Selecciona tu país</option>
            <option value="España">España</option>
            <option value="México">México</option>
            <option value="Argentina">Argentina</option>
            <option value="Colombia">Colombia</option>
            <option value="Chile">Chile</option>
            <option value="Perú">Perú</option>
            <option value="Ecuador">Ecuador</option>
            <option value="Venezuela">Venezuela</option>
            <option value="Uruguay">Uruguay</option>
            <option value="Bolivia">Bolivia</option>
            <option value="Paraguay">Paraguay</option>
            <option value="Costa Rica">Costa Rica</option>
            <option value="Panamá">Panamá</option>
            <option value="Guatemala">Guatemala</option>
            <option value="Honduras">Honduras</option>
            <option value="El Salvador">El Salvador</option>
            <option value="Nicaragua">Nicaragua</option>
            <option value="República Dominicana">República Dominicana</option>
        </select>
      </div>


      <div>
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="Contraseña" required />
      </div>


      <div>
        <label for="password2">Repetir contraseña</label>
        <input type="password" id="password2" name="password2" placeholder="Vuelve a escribirla" required />
      </div>


   


      <input type="hidden" name="rol" value="usuario" />


      <button type="submit">Registrarme</button>
    </form>


    <div class="footer">
      ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
    </div>
  </div>


  <script src="../../web/js/validacionRegistro.js"></script>


</body>
</html>






