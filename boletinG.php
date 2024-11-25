<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

if (isset($_GET["logout"]) && $_GET["logout"] == true) {
    session_destroy();
    header("location: index.php");
    exit;
}

// Conectar a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "mascotas";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Eliminar registro de boletín y registros relacionados
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Eliminar registros relacionados en opciones_checkbox
    $delete_checkbox_sql = "DELETE FROM opciones_checkbox WHERE boletin_id = ?";
    $stmt_checkbox = $conn->prepare($delete_checkbox_sql);
    $stmt_checkbox->bind_param("i", $delete_id);
    $stmt_checkbox->execute();
    $stmt_checkbox->close();

    // Eliminar boletín y registros relacionados
    $delete_boletin_sql = "DELETE b, mb, db 
                           FROM boletines b
                           LEFT JOIN mascotas_boletin mb ON b.mascota_id = mb.id
                           LEFT JOIN dueños_boletin db ON b.dueño_id = db.id
                           WHERE b.id = ?";
    $stmt_boletin = $conn->prepare($delete_boletin_sql);
    $stmt_boletin->bind_param("i", $delete_id);

    if ($stmt_boletin->execute()) {
        echo "<script>alert('Registro eliminado exitosamente'); window.location.href='boletinG.php';</script>";
    } else {
        echo "Error al eliminar el registro: " . $conn->error;
    }

    $stmt_boletin->close();
}

// Manejar búsqueda
if (isset($_POST['search'])) {
    $search = $_POST['search'];
    header("Location: boletinG.php?search=" . urlencode($search));
    exit;
}

// Recuperar los datos de la tabla
$search = isset($_GET['search']) ? $_GET['search'] : "";
$sql = "SELECT b.id, b.foto, b.fecha_perdida, mb.nombre AS nombre_mascota, mb.señas_particulares, db.nombre AS nombre_dueño,
               COALESCE(oc.seleccionado, 0) AS seleccionado
        FROM boletines b
        LEFT JOIN mascotas_boletin mb ON b.mascota_id = mb.id
        LEFT JOIN dueños_boletin db ON b.dueño_id = db.id
        LEFT JOIN opciones_checkbox oc ON b.id = oc.boletin_id";

if (!empty($search)) {
    $sql .= " WHERE LOWER(mb.nombre) LIKE LOWER('%$search%')";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="prueba.css">
    <link rel="icon" type="image/png" href="img/cuidado.png">
    <title>Padron Municipal San Francisco</title>
    <style>
        .custom-btn {
            background-color: black; 
            border-color: black;  
        }
        .custom-btn:hover {
            background-color: #9C918C; 
            border-color: #9C918C;
        }
        h2 {
            color: black;
            font-size: 40px;
            text-align: center;
            margin-top: 20px;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }
        .container {
            background-color: transparent;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.8);
            margin-top: 140px; 
        }
        .search-bar {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn-delete {
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .btn-delete:hover {
            background-color: #8A7E79;
        }
    </style>
</head>
<body>
<header class="header">
    <div class="header-left">
        <a href="#" class="logo"><img src="vertical1.png" alt="" height="75"></a>
        <a href="">ㅤㅤ</a>
        <img src="cani.png" width="175" height="75" >
    </div>
    <nav class="navbar">
        <a href="registro.php">Registro</a>
        <a href="datos.php">Datos</a>
        <a href="boletin.php">Bóletin</a>
        <a href="boleting.php">Generar Bóletin</a>
        <a href="?logout=true">
            <img src="img/cerrar-sesion.png" width="45" height="45" alt="Cerrar Sesión" class="d-inline-block align-top">
        </a>
    </nav>
</header>
    <div class="container margin-top">
        <h2>Boletín de Búsqueda de Mascotas</h2>
        <form method="post" class="search-bar">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Buscar por nombre de mascota" value="<?php echo htmlspecialchars($search); ?>">
                <div class="input-group-append">
                    <button class="btn btn-primary custom-btn" type="submit">Buscar</button>
                </div>
            </div>
        </form>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Fecha Pérdida</th>
                    <th>Nombre Mascota</th>
                    <th>Señas Particulares</th>
                    <th>Nombre Dueño</th>
                    <th>Encontrada</th>
                    <th>Eliminar</th>
                    <th>Generar Formato</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><img src='" . $row["foto"] . "' height='100' width='100' ></td>";
                        echo "<td>" . $row["fecha_perdida"] . "</td>";
                        echo "<td>" . $row["nombre_mascota"] . "</td>";
                        echo "<td>" . $row["señas_particulares"] . "</td>";
                        echo "<td>" . $row["nombre_dueño"] . "</td>";
                        echo "<td><input type='checkbox' " . ($row["seleccionado"] ? "checked" : "") . " onclick='updateCheckbox(" . $row["id"] . ", this.checked)'></td>";
                        echo "<td><button class='btn-delete' onclick='confirmDelete(" . $row["id"] . ")'><img src='img/borrar.png' alt='Eliminar' width='20' height='20'></button></td>";
                        echo "<td><a href='generar_formato_boletin.php?id=" . $row["id"] . "' target='_blank' class='btn btn-primary custom-btn'>Generar Formato</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No hay registros encontrados</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>
        function confirmDelete(id) {
            if (confirm("¿Estás seguro de que deseas eliminar este registro?")) {
                window.location.href = 'boletinG.php?delete_id=' + id;
            }
        }

        function updateCheckbox(id, checked) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "update_checkbox.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send("id=" + id + "&checked=" + (checked ? 1 : 0));
        }
    </script>
</body>
</html>
