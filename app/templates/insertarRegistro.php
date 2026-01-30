<?php

require_once '../../app/libs/bGeneral.php'; 

function registro()
{
    $errores = [];

    $name     = recoge("name");
    $email    = recoge("email");
    $password = recoge("password");
    $password2 = recoge("password2");


    cTexto($name, "name", $errores, 30, 2, true, true);


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores["email"] = "El correo no es válido";
    }

    
    if (strlen($password) < 6) {
        $errores["password"] = "La contraseña debe tener al menos 6 caracteres";
    }

   
    if ($password !== $password2) {
        $errores["password2"] = "Las contraseñas no coinciden";
    }

    // Si hay errores → volver a la vista
    if (!empty($errores)) {
        $params = [
            "errores" => $errores,
            "name" => $name,
            "email" => $email
        ];
        require "app/templates/registro.php";
        return;
    }

    // Si todo está bien → encriptar contraseña
    $passwordHash = crypt_blowfish($password);

    // Guardar en BD 
    $db = new PDO("mysql:host=localhost;dbname=indexproyecto;charset=utf8", "root", "");
    $stmt = $db->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $passwordHash]);


    // Redirigir o mostrar mensaje
    header("Location: index.php?ctl=login");
}
