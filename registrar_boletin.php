<?php
// Habilitar el informe de errores de PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conectar a la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "mascotas"; 
    
    $conn = new mysqli($servername, $username, $password, $database);
    
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Directorio de subida
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Nombre del archivo
    $target_file = $target_dir . basename($_FILES["foto"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Verificar si el archivo es una imagen
    $check = getimagesize($_FILES["foto"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "El archivo no es una imagen.";
        $uploadOk = 0;
    }

    // Verificar si el archivo ya existe
    if (file_exists($target_file)) {
        echo "El archivo ya existe.";
        $uploadOk = 0;
    }

    // Verificar el tamaño del archivo
    if ($_FILES["foto"]["size"] > 5000000) {
        echo "El archivo es demasiado grande.";
        $uploadOk = 0;
    }

    // Permitir ciertos formatos de archivo
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Solo se permiten archivos JPG, JPEG, PNG y GIF.";
        $uploadOk = 0;
    }

    // Verificar si $uploadOk es 0 por algún error
    if ($uploadOk == 0) {
        echo "El archivo no fue subido.";
    } else {
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            echo "El archivo " . htmlspecialchars(basename($_FILES["foto"]["name"])) . " ha sido subido.";
        } else {
            echo "Error subiendo el archivo.";
        }
    }

    // Recoger el resto de los datos del formulario
    $nombre_dueno = $_POST["nombre_dueno"];
    $direccion_dueño = $_POST["direccion_dueño"]; // Nuevo campo para dirección del dueño
    $nombre_mascota = $_POST["nombre_mascota"];
    $especie = $_POST["especie"];
    $raza = $_POST["raza"];
    $fecha_perdida = $_POST["fecha_perdida"];
    $lugar_perdida = $_POST["lugar_perdida"];
    $contacto = $_POST["contacto"];
    $genero = $_POST["genero"];
    $edad = $_POST["edad"];
    $unidad_t = $_POST["unidad_t"];
    $señas_particulares = $_POST["señas_particulares"];
    
    // Insertar en la tabla `dueños_boletin`
    $sql_dueño = "INSERT INTO dueños_boletin (nombre, contacto, dir_dueño) VALUES ('$nombre_dueno', '$contacto', '$direccion_dueño')";
    if ($conn->query($sql_dueño) === TRUE) {
        $dueño_id = $conn->insert_id;
    } else {
        echo "Error: " . $sql_dueño . "<br>" . $conn->error;
        error_log("Error: " . $sql_dueño . " - " . $conn->error);
        exit;
    }

    // Insertar en la tabla `mascotas_boletin`
    $sql_mascota = "INSERT INTO mascotas_boletin (dueño_id, nombre, especie, raza, genero, edad, unidad_t, señas_particulares) 
                    VALUES ('$dueño_id', '$nombre_mascota', '$especie', '$raza', '$genero', '$edad', '$unidad_t', '$señas_particulares')";
    if ($conn->query($sql_mascota) === TRUE) {
        $mascota_id = $conn->insert_id;
    } else {
        echo "Error: " . $sql_mascota . "<br>" . $conn->error;
        error_log("Error: " . $sql_mascota . " - " . $conn->error);
        exit;
    }

    // Insertar en la tabla `boletines`
    $sql_boletin = "INSERT INTO boletines (dueño_id, mascota_id, foto, fecha_perdida, lugar_perdida) 
                    VALUES ('$dueño_id', '$mascota_id', '$target_file', '$fecha_perdida', '$lugar_perdida')";
    if ($conn->query($sql_boletin) === TRUE) {
        echo "Boletín de búsqueda de mascota registrado exitosamente.";
    } else {
        echo "Error: " . $sql_boletin . "<br>" . $conn->error;
        error_log("Error: " . $sql_boletin . " - " . $conn->error);
        exit;
    }

    $conn->close();
}
?>
