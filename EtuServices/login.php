<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = escapeshellarg($_POST['email']);
    $password = escapeshellarg($_POST['password']);
    
    $pythonPath = "\"C:\\Users\\Guitton Cyprien\\AppData\\Local\\Programs\\Python\\Python311\\python.exe\"";
    $scriptPath = "C:\\xampp\\htdocs\\EtuServices\\server_redis.py";

    $cmd = "$pythonPath $scriptPath $email $password 2>&1";
    $output = shell_exec($cmd);
    
    if (strpos($output, "Connexion réussie") !== false) {
        $_SESSION['user'] = $_POST['email'];
        header("Location: services.php");
        exit();
    } else {
        $error = nl2br(htmlspecialchars($output)); // Affiche le message d'erreur de Redis
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Connexion</h1>
    </header>
    <form action="login.php" method="post">
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Se connecter</button>
    </form>
    <?php if (isset($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>
    <nav>
        <a href="accueil.php">Retour à l'accueil</a>
    </nav>
</body>
</html>
