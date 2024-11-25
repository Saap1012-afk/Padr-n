<?php
require_once "conexion.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $apellidos = $_POST["apellidos"];
    $usuario = $_POST["usuario"];
    $email = $_POST["email"];
    $contraseña = $_POST["contraseña"];

    // Verificar si el correo electrónico ya existe en la base de datos
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = "El correo electrónico ya está registrado.";
    } else {
        // Validaciones de contraseña y demás
        if (strlen($contraseña) < 8 || !preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $contraseña) || !preg_match('/\W/', $contraseña)) {
            $message = "La contraseña debe tener al menos 8 caracteres y contener al menos un número y un caracter especial.";
        } else {
            $contraseña_encriptada = password_hash($contraseña, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellidos, usuario, email, contraseña) VALUES (?, ?, ?, ?, ?)");
            
            if ($stmt) {
                $stmt->bind_param("sssss", $nombre, $apellidos, $usuario, $email, $contraseña_encriptada);
                
                if ($stmt->execute()) {
                    header("Location: registro1.php?success=1");
                    exit();
                } else {
                    $message = "Error al registrar el usuario.";
                }
                $stmt->close();
            } else {
                $message = "Error al preparar la consulta: " . $conn->error;
            }
        }
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="prueba2.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" type="image/png" href="img/cuidado.png">
    <title>Registro de Usuarios</title>
    <style>
        .input-box {
            position: relative;
        }
        .input-box .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }
        .button {
            background-color: #fff;
            color: black;
            width: 8.5em;
            height: 1.9em;
            border: black 0.2em solid;
            border-radius: 30px;
            text-align: right;
            transition: all 0.6s ease;
            position: fixed;
            bottom: 17px;
            right: 20px;
            font-size: 1.2em;
        }
        .button:hover {
            background-color: #fff;
            cursor: pointer;
        }
        .button svg {
            width: 1.6em;
            margin: -0.2em 0.8em 1em;
            position: absolute;
            display: flex;
            transition: all 0.6s ease;
        }
        .button:hover svg {
            transform: translateX(5px);
        }
        .text {
            margin: 0 1.5em;
        }
        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            color: #fff;
        }
        .alert-success {
            background-color: #28a745;
        }
        .alert-danger {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <form action="registro1.php" method="post">
            <h1>Registro de Usuario</h1>
            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="alert alert-success" role="alert">Usuario registrado correctamente. Puedes <a href="login.php" style="color: #fff; text-decoration: underline;">iniciar sesión aquí</a>.</div>
            <?php elseif (!empty($message)): ?>
                <div class="alert alert-danger" role="alert"><?php echo $message; ?></div>
            <?php endif; ?>
            <div class="input-box">
                <input type="text" placeholder="Nombre" name="nombre" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="text" placeholder="Apellidos" name="apellidos" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="text" placeholder="Cédula del veterinario" name="usuario" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="email" placeholder="Correo Electrónico" name="email" required>
                <i class='bx bxs-envelope'></i>
            </div>
            <div class="input-box">
                <input type="password" placeholder="Contraseña" name="contraseña" id="contraseña" pattern="(?=.*\d)(?=.*[a-zA-Z])(?=.*\W).{8,}" 
                       title="La contraseña debe tener al menos 8 caracteres y contener al menos un número, una letra y un caracter especial" required>
                <i class='bx bxs-lock-alt toggle-password' id="toggle-password"></i>
            </div>
            <button type="submit" class="btn">Registrar</button>
            <div class="register-link">
                <p>¿Ya tienes cuenta? <a href="login.php">Inicia Sesión</a></p>
            </div>
        </form>
    </div>
    <button class="button" onclick="window.location.href='index.php';">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"></path>
    </svg>
    <div class="text">
        Regresar
    </div>
</button>
    <script>
        const togglePassword = document.querySelector('#toggle-password');
        const password = document.querySelector('#contraseña');
        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('bxs-show');
            this.classList.toggle('bxs-hide');
        });
    </script>
</body>
</html>
