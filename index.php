<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="prueba.css">
    <link rel="icon" type="image/png" href="img/cuidado.png">
    <title>Padron Municipal San Francisco</title>
</head>

<style>
    body {
    font-family: Arial, sans-serif;
    background-color: #f2f2f2;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.content-wrapper {
    flex: 1;
}

.main-content {
    display: flex;
    justify-content: center;
    align-items: center;
    flex: 1;
    padding-top: 100px; 
}

.main-content img {
    max-width: 100%;
    height: auto;
}

.footer {
    background-color: white;
    padding: 20px 0;
    display: flex;
    justify-content: center;
    align-items: center;
}

.footer .footer-images {
    display: flex;
    justify-content: center;
    gap: 20px;
}

.footer .footer-images a {
    font-family: font-family: Arial, sans-serif;
    text-decoration: none;
    color: black;
}

.footer .footer-images img {
    max-width: 100px;
    height: auto;
}

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

.container {
    background-color: transparent;
    padding: 40px;
    border-radius: 8px;
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
    border-radius: 4px;
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
    background-color: #795544;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 18px;
    transition: background-color 0.3s ease;
}

.submit-btn input:hover {
    background-color: #795548;
}

</style>

<body>
    <header class="header">
        <a href="#" class="logo"><img src="vertical1.png" alt="" height="75"></a>
        <nav class="navbar">
            <a href="login.php"> Inicia Sesión
                <img src="acceso.png" alt="" width="45" height="45" class="d-inline-block align-top">
            </a>
            <a href="registro1.php">
                Registro
                <img src="perfil.png" alt="" width="45" height="45" class="d-inline-block align-top">
            </a>
        </nav>
    </header>

    <div class="main-content">
        <img src="banner_padron.png" alt="Main Image">
    </div>
    <footer class="footer">
        <div class="footer-images">
            <a href="login.php"> Registro de mascotas
                <img src="perrod.png" alt="" width="45" height="45" class="d-inline-block align-top">
            </a>
            <br>
            <a href="login.php"> ¿Perdiste tu mascota?
                <img src="hallazgo.png" alt="" width="45" height="45" class="d-inline-block align-top">
            </a>
            <br>
            <a href="login.php"> ¿Encontraste una mascota?
                <img src="buscar.png" alt="" width="45" height="45" class="d-inline-block align-top">
            </a>
        </div>
    </footer>
</body>
</html>
