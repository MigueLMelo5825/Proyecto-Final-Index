<?php

// Función que encripta el password utilizando blowfish con salt fijo
function encriptar($password, $cost=10) {
    return password_hash($password, PASSWORD_DEFAULT, ['cost' => $cost]);
}

function comprobarhash($pass, $passBD) {
    // Primero comprobamos si se ha empleado una contraseña correcta:
    return password_verify($pass, $passBD) ;
}
?>
