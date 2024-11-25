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

// Eliminar registro
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM padron_municipal WHERE id='$delete_id'";
    if ($conn->query($delete_sql) === TRUE) {
        echo "<script>alert('Registro eliminado exitosamente'); window.location.href='datos.php';</script>";
    } else {
        echo "Error al eliminar el registro: " . $conn->error;
    }
}

// Recuperar los datos de la tabla
$search = "";
if (isset($_POST['search'])) {
    $search = $_POST['search'];
    $sql = "SELECT id, nombre_mascota, raza, edad, foto, fecha_ultima_vacunacion FROM padron_municipal WHERE LOWER(nombre_mascota) LIKE LOWER('%$search%')";
} else {
    $sql = "SELECT id, nombre_mascota, raza, edad, foto, fecha_ultima_vacunacion FROM padron_municipal";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="prueba.css">
    <title>Document</title>
</head>

<style>
        .custom-btn {
            background-color: black; 
            border-color: black;  
        }
        .custom-btn:hover {
            background-color: #9C918C; 
            border-color: #9C918C;
        }
        h4 {
            color: black;
            font-size: 40px;
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
            margin-top: 20px;
        }
        .margin-top {
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

<script>
        function confirmDelete(id) {
            if (confirm("¿Estás seguro de que deseas eliminar este registro?")) {
                window.location.href = 'datos.php?delete_id=' + id;
            }
        }
    </script>

<body>
    <header class="header">
        <a href="#" class="logo"><img src="vertical1.png" alt="" height="75"></a>
        <nav class="navbar">
            <a href="registro.php">Registro</a>
            <a href="datos.php">Datos</a>
            <a href="boletin.php">Bóletin</a>
            <a href="boletinG.php">Generar Bóletin</a>
            <a href="?logout=true">
                <img src="img/cerrar-sesion.png" width="45" height="45" alt="Cerrar Sesión" class="d-inline-block align-top">
            </a>
        </nav>
    </header>
    <div class="container margin-top">
    <h2>Datos de Mascotas Registradas</h2>
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
            <th>Nombre</th>
            <th>Raza</th>
            <th>Edad</th>
            <th>Foto</th>
            <th>Fecha Última Vacunación</th>
            <th>Acciones</th>
            <th>Formato</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["nombre_mascota"] . "</td>";
                echo "<td>" . $row["raza"] . "</td>";
                echo "<td>" . $row["edad"] . "</td>";
                echo "<td><img src='" . $row["foto"] . "' alt='Foto' width='100' height='90'></td>";
                echo "<td>" . $row["fecha_ultima_vacunacion"] . "</td>";
                echo "<td><button class='btn-delete' onclick='confirmDelete(" . $row["id"] . ")'><img src='img/borrar.png' alt='Eliminar' width='20' height='20'></button></td>";
                echo "<td><a href='generar_formato.php?id=" . $row["id"] . "' class='btn btn-primary custom-btn'>Generar Formato</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No hay mascotas registradas</td></tr>";
        }
        ?>
    </tbody>
    </table>
</div>
</body>
</html>
