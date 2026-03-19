<?php
session_start();

$errores = $_SESSION['errores_login'] ?? [];
$email = $_SESSION['email_login'] ?? '';
unset($_SESSION['errores_login'], $_SESSION['email_login']);

require 'includes/funciones.php';
incluirTemplate('header');
?>

    <main class="contenedor seccion contenido-centrado">
        <h1>Iniciar Sesión</h1>
        <?php foreach($errores as $error): ?>
            <div class="alerta error">
                <?php echo $error; ?>
                
            </div>
        <?php endforeach; ?>
        <form class="formulario" method="POST" action="loging.php">
            <fieldset>
                <legend>Email y Password</legend>

                <label for="email">E-mail</label>
                <input type="email" name="email" placeholder="Tu Email" id="email" value="<?php echo $email; ?>" required>


                <label for="password">Password</label>
                <input type="password" name="password" placeholder="Tu Password" id="password" required>
            </fieldset>
            <input type="submit" value="Iniciar Sesión" class="boton boton-verde">

        </form>
    </main>

<?php
    incluirTemplate('footer');
?>