<?php

require_once '../../app/libs/bGeneral.php'; 

function registro()
{
    $errores = [];

    // Recoger datos
    $username   = recoge("username");
    $name       = recoge("name");
    $email      = recoge("email");
    $password   = recoge("password");
    $password2  = recoge("password2");
    $pais_id    = recoge("pais_id");

    // ============================
    // VALIDACIONES
    // ============================

    // Username
    if (!preg_match('/^[A-Za-z0-9._-]{3,50}$/', $username)) {
        $errores["username"] = "El nombre de usuario no es válido.";
    }

    // Nombre
    cTexto($name, "name", $errores, 30, 2, true, true);

    // Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores["email"] = "El correo no es válido.";
    }

    // Contraseña
    if (strlen($password) < 6) {
        $errores["password"] = "La contraseña debe tener al menos 6 caracteres.";
    }

    if ($password !== $password2) {
        $errores["password2"] = "Las contraseñas no coinciden.";
    }

    // ============================
    // SI HAY ERRORES → VOLVER A LA VISTA
    // ============================
    if (!empty($errores)) {
        $params = [
            "errores" => $errores,
            "username" => $username,
            "name" => $name,
            "email" => $email
        ];
        require "app/templates/registro.php";
        return;
    }

    // ============================
    // VALIDAR QUE EMAIL Y USERNAME NO EXISTEN
    // ============================

    $db = new PDO("mysql:host=localhost;dbname=indexproyecto;charset=utf8", "root", "");

    // Email
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errores["email"] = "El correo ya está registrado.";
    }

    // Username
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        $errores["username"] = "El nombre de usuario ya está en uso.";
    }

    if (!empty($errores)) {
        $params = [
            "errores" => $errores,
            "username" => $username,
            "name" => $name,
            "email" => $email
        ];
        require "app/templates/registro.php";
        return;
    }

    // ============================
    // INSERTAR USUARIO
    // ============================

    $passwordHash = crypt_blowfish($password);

    $sql = "INSERT INTO usuarios (username, nombre, email, contrasena, pais)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $db->prepare($sql);
    $stmt->execute([$username, $name, $email, $passwordHash, $pais_id]);

    // ============================
    // REDIRIGIR A LOGIN
    // ============================

    header("Location: index.php?ctl=login");
}
