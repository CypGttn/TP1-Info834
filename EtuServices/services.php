<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['user'];

// Fonction pour appeler le script Python et enregistrer l'utilisation des services
function logServiceUsage($email, $service) {
    // Vérifier si les chemins sont corrects pour votre environnement
    $pythonPath = "\"C:\\Users\\Guitton Cyprien\\AppData\\Local\\Programs\\Python\\Python311\\python.exe\"";
    $scriptPath = "C:\\xampp\\htdocs\\EtuServices\\server_redis.py";  // Assurez-vous que c'est bien le fichier Python (.py)
    
    // Exécution du script Python avec les paramètres appropriés
    $cmd = "$pythonPath $scriptPath $email $service 2>&1";  // Envoie les paramètres et récupère les erreurs éventuelles
    shell_exec($cmd);  // Exécute la commande
}

// Vérifier si un service a été utilisé
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['service'])) {
        logServiceUsage($email, $_POST['service']);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Bienvenue, <?= htmlspecialchars($email) ?></h1>
    </header>
    
    <h2>Choisissez un service :</h2>
    <form action="services.php" method="post">
        <button type="submit" name="service" value="Vente">Service Vente</button>
        <button type="submit" name="service" value="Achat">Service Achat</button>
    </form>

    <nav>
        <a href="logout.php">Se déconnecter</a>
    </nav>
</body>
</html>
