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

// Eliminar registro de mascota y registros relacionados
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Obtener el dueño de la mascota
    $get_dueño_sql = "SELECT dueño_id FROM mascotas WHERE id=?";
    $stmt = $conn->prepare($get_dueño_sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $dueño_id = null;
    if ($row = $result->fetch_assoc()) {
        $dueño_id = $row['dueño_id'];
    }
    $stmt->close();

    // Eliminar fotos relacionadas
    $delete_fotos_sql = "DELETE FROM fotos WHERE mascota_id=?";
    $stmt = $conn->prepare($delete_fotos_sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    // Eliminar vacunas relacionadas
    $delete_vacunas_sql = "DELETE FROM vacunas WHERE mascota_id=?";
    $stmt = $conn->prepare($delete_vacunas_sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    // Eliminar detalles de mascotas
    $delete_detalles_sql = "DELETE FROM detalles_mascotas WHERE mascota_id=?";
    $stmt = $conn->prepare($delete_detalles_sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    // Finalmente, eliminar la mascota
    $delete_mascota_sql = "DELETE FROM mascotas WHERE id=?";
    $stmt = $conn->prepare($delete_mascota_sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        // Verificar si el dueño tiene otras mascotas
        $check_dueño_sql = "SELECT COUNT(*) AS mascota_count FROM mascotas WHERE dueño_id=?";
        $stmt = $conn->prepare($check_dueño_sql);
        $stmt->bind_param("i", $dueño_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $mascota_count = $row['mascota_count'];
        $stmt->close();

        // Eliminar el dueño si no tiene más mascotas
        if ($mascota_count == 0) {
            $delete_dueño_sql = "DELETE FROM dueños WHERE id=?";
            $stmt = $conn->prepare($delete_dueño_sql);
            $stmt->bind_param("i", $dueño_id);
            $stmt->execute();
            $stmt->close();
        }

        echo "<script>alert('Registro eliminado exitosamente'); window.location.href='datos.php';</script>";
    } else {
        echo "Error al eliminar el registro: " . $conn->error;
    }

    $stmt->close();
}

// Recuperar los datos de la tabla
$search = "";
$sort = "DESC"; // Orden por defecto (Del más nuevo al más antiguo)

if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

if (isset($_GET['sort'])) {
    $sort = $_GET['sort'];
}

// Validar el parámetro de orden
if ($sort !== "ASC" && $sort !== "DESC") {
    $sort = "DESC";
}

// Preparar la consulta SQL con LIKE para buscar nombres parciales y ordenar
$sql = "SELECT m.id, m.nombre_mascota, m.raza, d.nombre_dueño, d.telefono_dueño, d.direccion_dueño, dm.fecha_ultima_vacunacion 
        FROM mascotas m
        LEFT JOIN dueños d ON m.dueño_id = d.id
        LEFT JOIN detalles_mascotas dm ON m.id = dm.mascota_id
        WHERE LOWER(m.nombre_mascota) LIKE LOWER('%$search%')
        ORDER BY dm.fecha_ultima_vacunacion $sort";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
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
        <h2>Datos de Mascotas Registradas</h2>
        <form method="get" class="search-bar">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Buscar por nombre de mascota" value="<?php echo htmlspecialchars($search); ?>">
                <div class="input-group-append">
                    <button class="btn btn-primary custom-btn" type="submit">Buscar</button>
                </div>
            </div>
            <div class="form-group">
                <label for="sort">Ordenar por fecha de última vacunación:</label>
                <select id="sort" name="sort" class="form-control" onchange="this.form.submit()">
                    <option value="DESC" <?php if ($sort == "DESC") echo "selected"; ?>>Del más nuevo al más antiguo</option>
                    <option value="ASC" <?php if ($sort == "ASC") echo "selected"; ?>>Del más antiguo al más nuevo</option>
                </select>
            </div>
        </form>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Raza</th>
                    <th>Dueño</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Fecha de la vacunación</th>
                    <th>Acciones</th>
                    <th>Formato</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["nombre_mascota"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["raza"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["nombre_dueño"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["telefono_dueño"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["direccion_dueño"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["fecha_ultima_vacunacion"]) . "</td>";
                        echo "<td><button class='btn-delete' onclick='confirmDelete(" . $row["id"] . ")'><img src='img/borrar.png' alt='Eliminar' width='20' height='20'></button></td>";
                        echo "<td><a href='generar_formato.php?id=" . $row["id"] . "' target='_blank' class='btn btn-primary custom-btn'>Generar Formato</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No hay mascotas registradas</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm("¿Estás seguro de que deseas eliminar este registro?")) {
                window.location.href = 'datos.php?delete_id=' + id;
            }
        }
    </script>
</body>
</html>
