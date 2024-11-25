<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "mascotas";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $checked = $_POST["checked"];

    // Verificar si el registro ya existe
    $sql = "SELECT * FROM opciones_checkbox WHERE boletin_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Si existe, actualizar
        $sql = "UPDATE opciones_checkbox SET seleccionado = ? WHERE boletin_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $checked, $id);
    } else {
        // Si no existe, insertar
        $sql = "INSERT INTO opciones_checkbox (boletin_id, opcion, seleccionado) VALUES (?, 'default', ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id, $checked);
    }

    if ($stmt->execute()) {
        echo "Checkbox updated successfully.";
    } else {
        echo "Error updating checkbox: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
