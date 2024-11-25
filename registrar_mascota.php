<?php
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
            exit;
        }
    }

    // Recoger el resto de los datos del formulario
    $usuario_id = $_POST["usuario_id"];
    $numero_arete = $_POST["numero_arete"];
    $nombre_dueño = $_POST["nombre_dueño"];
    $telefono_dueño = $_POST["telefono_dueño"];
    $direccion_dueño = $_POST["direccion_dueño"];
    $nombre_mascota = $_POST["nombre_mascota"];
    $especie = $_POST["especie"];
    $raza = $_POST["raza"];
    $fecha_ultima_vacunacion = $_POST["fecha_ultima_vacunacion"];
    $genero = $_POST["genero"];
    $esterilizada = $_POST["esterilizada"];
    $edad = $_POST["edad"];
    $unidad_tiempo = $_POST["unidad_tiempo"];
    $chip_tatuaje = $_POST["chip_tatuaje"];
    $señas_particulares = $_POST["señas_particulares"];
    $vacunas_suministradas = explode("\n", $_POST["vacunas_suministradas"]);

    // Validar el número de arete
    if (empty($numero_arete)) {
        echo "Por favor, ingrese un número de arete o 'N/A'.";
        exit;
    }

    // Asegúrate de que el usuario_id no sea cero y esté en la base de datos
    if ($usuario_id <= 0) {
        echo "ID de usuario inválido.";
        exit;
    }

    $sql_dueño = "INSERT INTO dueños (nombre_dueño, telefono_dueño, direccion_dueño) VALUES (?, ?, ?)";
    $stmt_dueño = $conn->prepare($sql_dueño);
    $stmt_dueño->bind_param("sss", $nombre_dueño, $telefono_dueño, $direccion_dueño);
    
    if ($stmt_dueño->execute()) {
        $dueño_id = $conn->insert_id;
    } else {
        echo "Error: " . $stmt_dueño->error;
        exit;
    }
    // Insertar en la tabla mascotas
    $sql_mascota = "INSERT INTO mascotas (numero_arete, nombre_mascota, especie, raza, dueño_id, usuario_id) 
                    VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_mascota = $conn->prepare($sql_mascota);
    $stmt_mascota->bind_param("ssssii", $numero_arete, $nombre_mascota, $especie, $raza, $dueño_id, $usuario_id);
    
    if ($stmt_mascota->execute()) {
        $mascota_id = $conn->insert_id;
    } else {
        echo "Error: " . $stmt_mascota->error;
        exit;
    }

    // Insertar en la tabla detalles_mascotas
    $sql_detalle = "INSERT INTO detalles_mascotas (mascota_id, fecha_ultima_vacunacion, genero, esterilizada, edad, unidad_tiempo, chip_tatuaje, señas_particulares) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_detalle = $conn->prepare($sql_detalle);
    $stmt_detalle->bind_param("ssssssss", $mascota_id, $fecha_ultima_vacunacion, $genero, $esterilizada, $edad, $unidad_tiempo, $chip_tatuaje, $señas_particulares);

    if ($stmt_detalle->execute()) {
        echo "Detalles de la mascota registrados exitosamente.";
    } else {
        echo "Error: " . $stmt_detalle->error;
        exit;
    }

    // Insertar en la tabla fotos
    $sql_foto = "INSERT INTO fotos (mascota_id, ruta) VALUES (?, ?)";
    $stmt_foto = $conn->prepare($sql_foto);
    $stmt_foto->bind_param("is", $mascota_id, $target_file);

    if ($stmt_foto->execute()) {
        echo "Foto de la mascota registrada exitosamente.";
    } else {
        echo "Error: " . $stmt_foto->error;
        exit;
    }

    // Insertar en la tabla vacunas
    $sql_vacuna = "INSERT INTO vacunas (mascota_id, vacuna) VALUES (?, ?)";
    $stmt_vacuna = $conn->prepare($sql_vacuna);
    
    foreach ($vacunas_suministradas as $vacuna) {
        $vacuna = trim($vacuna);
        if ($vacuna != "") {
            $stmt_vacuna->bind_param("is", $mascota_id, $vacuna);
            if ($stmt_vacuna->execute() !== TRUE) {
                echo "Error: " . $stmt_vacuna->error;
                exit;
            }
        }
    }

    // Cerrar conexiones
    $stmt_dueño->close();
    $stmt_mascota->close();
    $stmt_detalle->close();
    $stmt_foto->close();
    $stmt_vacuna->close();
    $conn->close();
}
?>
