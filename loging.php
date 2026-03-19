<?php

session_start();

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: base.php');
	exit;
}

require 'includes/config/database.php';
$db = conectarDB();

$errores = [];
$emailInput = trim($_POST['email'] ?? '');
$email = filter_var(strtolower($emailInput), FILTER_VALIDATE_EMAIL);
$password = trim($_POST['password'] ?? '');

if(!$email) {
	$errores[] = 'El email es obligatorio o no es valido';
}

if(!$password) {
	$errores[] = 'El password es obligatorio';
}

if(empty($errores)) {
	$stmt = mysqli_prepare($db, 'SELECT email, password FROM usuarios WHERE email = ? LIMIT 1');

	if($stmt) {
		mysqli_stmt_bind_param($stmt, 's', $email);
		mysqli_stmt_execute($stmt);
		$resultado = mysqli_stmt_get_result($stmt);
		$usuario = mysqli_fetch_assoc($resultado);
		mysqli_stmt_close($stmt);

		if($usuario && password_verify($password, $usuario['password'])) {
			$_SESSION['usuario'] = $usuario['email'];
			$_SESSION['login'] = true;
			header('Location: admin/index.php');
			exit;
		}

		$errores[] = 'Credenciales invalidas';
	} else {
		$errores[] = 'No se pudo validar el login. Verifica la tabla usuarios.';
	}
}

$_SESSION['errores_login'] = $errores;
$_SESSION['email_login'] = $emailInput;

header('Location: base.php');
exit;
