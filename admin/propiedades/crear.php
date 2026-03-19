<!-- importacion de la base de datos -->
<?php
require '../../includes/config/database.php';
//conexion a la base de datos
    $db = conectarDB();
    //consulta de la base de datos
    $consulta = "SELECT * FROM vendedores";
    $resultado = mysqli_query($db, $consulta);

  //arreglo con mensaje de errores
$errores = [];
$titulo = '';
$precio = '';
$descripcion = '';
$habitaciones = '';
$wc = '';
$estacionamiento = '';
$vendedorId = '';


if($_SERVER['REQUEST_METHOD'] === 'POST') {
    //echo "<pre>";
    //echo "POST:\n";
    //var_dump($_POST);
    //echo "\nFILES:\n";
    //var_dump($_FILES);

    //if(isset($_FILES['imagen'])) {
    //    echo "\nIMAGEN DETALLE:\n";
    //    var_dump($_FILES['imagen']);
    //}
    //echo "</pre>";
    //exit;

    $titulo = mysqli_real_escape_string($db, $_POST['titulo'] ?? '');
    $precio = mysqli_real_escape_string($db, $_POST['precio'] ?? '');
    $descripcion = mysqli_real_escape_string($db, $_POST['descripcion'] ?? '');
    $habitaciones = mysqli_real_escape_string($db, $_POST['habitaciones'] ?? '');
    $wc = mysqli_real_escape_string($db, $_POST['wc'] ?? '');
    $estacionamiento = mysqli_real_escape_string($db, $_POST['estacionamiento'] ?? '');
    $vendedorId = mysqli_real_escape_string($db, $_POST['vendedor'] ?? '');
    $imagen = $_FILES['imagen'] ?? null;
    $nombreImagen = mysqli_real_escape_string($db, $imagen['name'] ?? '');
    $creado = date('Y/m/d');
//asignar files hacia una variable

    if(!$titulo) {
        $errores[] = "Debes añadir un titulo";
    }

    if(!$precio) {
        $errores[] = "Debes añadir un precio";
    }

    if(strlen($descripcion) < 50) {
        $errores[] = "La descripción es obligatoria y debe tener al menos 50 caracteres";
    }   

    if(!$habitaciones) {
        $errores[] = "Debes añadir el número de habitaciones";
    }

    if(!$wc) {
        $errores[] = "Debes añadir el número de baños";
    }

    if(!$estacionamiento) {
        $errores[] = "Debes añadir el número de plazas de estacionamiento";
    }

    if(!$vendedorId) {
        $errores[] = "Elije un vendedor";
    }
    if(!$imagen || ($imagen['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        $errores[] = "La imagen es obligatoria";
    }

    $medida = 1000 * 1000;

    if(($imagen['size'] ?? 0) > $medida) {
        $errores[] = "La imagen es muy pesada";
    }

    //revisar que el arreglo de errores esté vacío
        if(empty($errores)) {
            /** SUBIDA DE ARCHIVOS */
            //crear carpeta 
            $carpetaImagenes = '../../imagenes/';
            if(!is_dir($carpetaImagenes)) {
                mkdir($carpetaImagenes);
            }
            //generar un nombre unico
            $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";
            //subir la imagen
            move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen);

        $query = "INSERT INTO propiedades (titulo, precio, imagen, descripcion,
            habitaciones, wc, estacionamiento, creado, vendedores_id) 
                VALUES ('$titulo','$precio', '$nombreImagen', '$descripcion',
                '$habitaciones', '$wc', '$estacionamiento', '$creado', '$vendedorId')";
   
        $resultado = mysqli_query($db, $query);
        if($resultado) {
            //redireccionar al usuario
            header('Location: /admin?resultado=1');
        }
    }
}

require '../../includes/funciones.php';
    incluirTemplate('header');
?>

    <main class="contenedor seccion">
        <h1>Crear</h1>
        <a href="/admin" class="boton boton-verde">Volver</a>

        <?php foreach($errores as $error): ?>
            <div class="alerta error">
                <?php echo $error; ?>
            </div>
        <?php endforeach; ?>

        <form class="formulario" method="POST" action="/admin/propiedades/crear.php"
        enctype="multipart/form-data">
            <fieldset>
                <legend>Información General</legend>
                <label for="titulo">Titulo</label>
                <input type="text"
                id ="titulo"
                name="titulo"
                placeholder="Titulo Propiedad"
                value="<?php echo $titulo; ?>">

                <label for="precio">Precio</label>
                <input type="number"
                id ="precio"
                name="precio"
                placeholder="Precio Propiedad"
                value="<?php echo $precio; ?>">
                
                <label for="imagen">Imagen</label>
                <input type="file"
                id ="imagen"
                name="imagen"
                accept="image/jpeg, image/png">

                <label for="descripcion">Descripción</label>
                <textarea id="descripcion"
                name="descripcion"><?php echo $descripcion; ?></textarea>
            </fieldset>
            <fieldset>
                <legend>Información Técnica</legend>
                <label for="habitaciones">Habitaciones</label>
                <input type="number"
                id ="habitaciones"
                name="habitaciones"
                placeholder="Ej: 3"
                min="1" max="9"
                value="<?php echo $habitaciones; ?>">

                <label for="wc">Baños</label>
                <input type="number"
                id ="wc" name="wc"
                placeholder="Ej: 3"
                min="1" max="9"
                value="<?php echo $wc; ?>">

                <label for="estacionamiento">Estacionamiento</label>
                <input type="number"
                id ="estacionamiento"
                name="estacionamiento"
                placeholder="Ej: 3"
                min="1" max="9"
                value="<?php echo $estacionamiento; ?>">
            </fieldset>
            <fieldset>
                <select name="vendedor">
                    <option value="" disabled selected>--Seleccione un Vendedor--</option>
                    <?php while($vendedor = mysqli_fetch_assoc($resultado)): ?>
                        <option <?php echo $vendedorId === $vendedor['id'] ? 'selected' : ''; ?>
                            value="<?php echo $vendedor['id']; ?>">
                            <?php echo$vendedor['nombre'] . " " . $vendedor['apellido']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </fieldset>
            <input type="submit"
            value="Crear Propiedad"
            class="boton boton-verde">
        </form>
    </main>

<?php
    incluirTemplate('footer');
?>