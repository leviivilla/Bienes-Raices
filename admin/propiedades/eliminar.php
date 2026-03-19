<?php

require '../../includes/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if ($id) {
        $db = conectarDB();

        $query = "SELECT imagen FROM propiedades WHERE id = ${id}";
        $resultado = mysqli_query($db, $query);
        $propiedad = mysqli_fetch_assoc($resultado);

        if ($propiedad && !empty($propiedad['imagen'])) {
            $archivoImagen = '../../imagenes/' . $propiedad['imagen'];
            if (file_exists($archivoImagen)) {
                unlink($archivoImagen);
            }
        }

        $query = "DELETE FROM propiedades WHERE id = ${id}";
        $resultado = mysqli_query($db, $query);

        if ($resultado) {
            header('Location: /admin?resultado=3');
            exit;
        }
    }
}

header('Location: /admin');
exit;
