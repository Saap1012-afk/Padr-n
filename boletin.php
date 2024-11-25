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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="prueba.css">
    <link rel="stylesheet" href="input.css">
    <link rel="icon" type="image/png" href="img/cuidado.png">
    <title>Padron Municipal San Francisco</title>
</head>

<style>
        .custom-btn {
            background-color: #708090;
            border-color: #708090;
        }
        .custom-btn:hover {
            background-color: #795548;
            border-color: #795548;
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
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.8);
            margin: 40px auto;
            max-width: 900px;
        }
        .margin-top {
            margin-top: 140px;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-size: 32px;
        }
        .form-table {
            width: 100%;
            font-family: 'Verdana', sans-serif;
        }
        .form-table td {
            padding: 15px;
        }
        .form-table label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-table input[type="text"],
        .form-table input[type="date"],
        .form-table input[type="number"],
        .form-table select,
        .form-table textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 20px;
        }
        .form-table textarea {
            resize: vertical;
        }
        .form-table input[type="file"] {
            padding: 5px;
        }
        .submit-btn {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        .submit-btn input {
            background-color: #030303;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }
        .submit-btn input:hover {
            background-color: #030303;
        }
    </style>
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
    <form id="boletinForm" method="post" enctype="multipart/form-data" action="registrar_boletin.php">
        <table class="form-table">
            <tr>
                <td colspan="3">
                    <label for="foto">Foto:</label>
                    <input type="file" name="foto" id="foto" required>
                </td>
            </tr>
            <tr>
                <td colspan="1">
                    <label for="nombre_dueno">Nombre del Dueño:</label>
                    <input type="text" name="nombre_dueno" id="nombre_dueno" required>
                </td>
        
    <td colspan="2">
        <label for="direccion_dueño">Dirección del Dueño:</label>
        <input type="text" name="direccion_dueño" id="direccion_dueño" required>
    </td>
</tr>

            <tr>
                <td>
                    <label for="nombre_mascota">Nombre de la Mascota:</label>
                    <input type="text" name="nombre_mascota" id="nombre_mascota" required>
                </td>
                <td>
                    <label for="especie">Especie:</label>
                    <select name="especie" id="especie" required>
                        <option value="Canino">Canino</option>
                        <option value="Felino">Felino</option>
                        <option value="Ave">Ave</option>
                        <option value="Reptil">Reptil</option>
                        <option value="Roedor">Roedor</option>
                        <option value="Pez">Pez</option>
                        <option value="Otro">Otro</option>
                    </select>
                </td>
                <td>
                    <label for="raza">Raza:</label>
                    <input type="text" name="raza" id="raza" required>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="fecha_perdida">Fecha de Pérdida:</label>
                    <input type="date" name="fecha_perdida" id="fecha_perdida" required>
                </td>
                <td colspan="2">
                    <label for="lugar_perdida">Lugar de Pérdida:</label>
                    <input type="text" name="lugar_perdida" id="lugar_perdida" required>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="contacto">Contacto:</label>
                    <input type="text" name="contacto" id="contacto" required>
                </td>
                <td>
                    <label for="genero">Género:</label>
                    <select name="genero" id="genero" required>
                        <option value="Macho">Macho</option>
                        <option value="Hembra">Hembra</option>
                    </select>
                </td>
                <td>
                    <label for="edad">Edad:</label>
                    <input type="number" name="edad" id="edad" required>
                    <label for="unidad_t">Unidad:</label>
                        <select name="unidad_t" id="unidad_t" required>
                            <option value="años">Años</option>
                            <option value="meses">Meses</option>
                            <option value="dias">Días</option>
                        </select>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <label for="señas_particulares">Señas Particulares:</label>
                    <textarea name="señas_particulares" id="señas_particulares" rows="4" required></textarea>
                </td>
            </tr>
        </table>
        <div class="submit-btn">
            <input type="submit" value="Registrar">
        </div>
    </form>
</div>

<script>
    document.getElementById('boletinForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevenir el envío del formulario por defecto

        var form = this;
        var allFieldsFilled = true;
        var fields = form.querySelectorAll('input[required], select[required], textarea[required]');

        fields.forEach(function(field) {
            if (!field.value.trim()) {
                allFieldsFilled = false;
            }
        });

        if (!allFieldsFilled) {
            alert('Por favor complete todos los campos requeridos');
            return false; // Detener el envío del formulario si los campos requeridos no están completos
        }

        // Crear objeto FormData con los datos del formulario
        var formData = new FormData(form);
        var xhr = new XMLHttpRequest(); // Crear objeto XMLHttpRequest para realizar la solicitud AJAX

        xhr.onreadystatechange = function() {
            if (this.readyState == 4) {
                if (this.status == 200) {
                    // Cuando la solicitud se complete y el estado de la respuesta sea exitoso (status 200)
                    alert("Registro exitoso"); // Mostrar mensaje de registro exitoso
                    form.reset(); // Limpiar el formulario
                } else {
                    alert("Error al registrar mascota"); // Mostrar mensaje de error si la solicitud falla
                }
            }
        };

        xhr.open("POST", "registrar_boletin.php", true); // Especificar el método, la URL y si la solicitud es asíncrona
        xhr.send(formData); // Enviar los datos del formulario como parte de la solicitud
    });
</script>
</body>
</html>
