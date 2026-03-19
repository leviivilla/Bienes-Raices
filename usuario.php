<?php
//crear la conexion
require 'includes/config/database.php';
$db = conectarDB();

//crear un email y password
$email = "correo@corre.com";
$password = "1234";

$passwordHash = password_hash($password, PASSWORD_BCRYPT);

//crear tabla de usuarios si no existe
$queryTabla = "CREATE TABLE IF NOT EXISTS usuarios (
	id INT(11) NOT NULL AUTO_INCREMENT,
	email VARCHAR(100) NOT NULL UNIQUE,
	password VARCHAR(255) NOT NULL,
	PRIMARY KEY (id)
)";

mysqli_query($db, $queryTabla);

$stmt = mysqli_prepare($db, "SELECT id FROM usuarios WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$existeUsuario = mysqli_num_rows($resultado) > 0;
mysqli_stmt_close($stmt);

if($existeUsuario) {
	$stmt = mysqli_prepare($db, "UPDATE usuarios SET password = ? WHERE email = ?");
	mysqli_stmt_bind_param($stmt, 'ss', $passwordHash, $email);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	echo "Usuario actualizado correctamente" . PHP_EOL;
} else {
	//query para crear el usuario
	$stmt = mysqli_prepare($db, "INSERT INTO usuarios (email, password) VALUES (?, ?)");
	mysqli_stmt_bind_param($stmt, 'ss', $email, $passwordHash);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);

	echo "Usuario creado correctamente" . PHP_EOL;
}

//opcional: normalizar usuario antiguo del curso para que tambien funcione con password hasheado
$emailAntiguo = "correo@correo.com";
$stmt = mysqli_prepare($db, "SELECT id FROM usuarios WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $emailAntiguo);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$existeAntiguo = mysqli_num_rows($resultado) > 0;
mysqli_stmt_close($stmt);

if($existeAntiguo) {
	$stmt = mysqli_prepare($db, "UPDATE usuarios SET password = ? WHERE email = ?");
	mysqli_stmt_bind_param($stmt, 'ss', $passwordHash, $emailAntiguo);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	echo "Usuario antiguo normalizado";
}
