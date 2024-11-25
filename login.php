<?php
session_start();
require_once "conexion.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $contraseña = $_POST["contraseña"];

    $sql = "SELECT id, usuario, contraseña FROM usuarios WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($contraseña, $row["contraseña"])) {
            $_SESSION["loggedin"] = true;
            $_SESSION["usuario"] = $row["usuario"]; // Guarda el nombre de usuario en la sesión
            header("Location: loader.php");
            exit();
        } else {
            $message = "Correo o contraseña incorrectos.";
        }
    } else {
        $message = "Correo o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="prueba2.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" type="image/png" href="img/cuidado.png">
    <title>Inicio de sesión</title>
    <style>
        .icon-eye {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }
        .input-box {
            position: relative;
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
            bottom: 20px;
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
    </style>
</head>
<body>
    <div class="wrapper">
        <form action="login.php" method="post">
            <h1>Inico de sesión</h1>
            <?php if (!empty($message)): ?>
                <div class="alert alert-danger" role="alert"><?php echo $message; ?></div>
            <?php endif; ?>
            <div class="input-box">
                <input type="email" placeholder="Usuario" name="email" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" placeholder="Contraseña" name="contraseña" id="contraseña" required>
                <i class='bx bxs-lock-alt icon-eye' id="togglePassword"></i>
            </div>
            <button type="submit" class="btn">Iniciar sesión</button>
            <div class="register-link">
                <p>¿No tienes cuenta? <a href="registro1.php">Regístrate</a></p>
            </div>
        </form>
    </div>
    <button class="button" onclick="window.location.href='index.php';">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"></path>
        </svg>
        <div class="text">Regresar</div>
    </button>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#contraseña');
        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('bxs-show');
        });
    </script>
</body>
</html>
