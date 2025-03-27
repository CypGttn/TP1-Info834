<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$services = $_SESSION['services'] ?? []; // Utilisation de l'opérateur NULL coalescent pour éviter l'erreur

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos services</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Nos services</h1>
    </header>
    <div class="content">
        <p>Bienvenue, <?= htmlspecialchars($user) ?> !</p>
        <p>Voici une liste de vos services autorisés :</p>

        <?php if (!empty($services)): ?>
            <ul>
                <?php foreach ($services as $service): ?>
                    <li><?= htmlspecialchars($service) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucun service disponible pour le moment.</p>
        <?php endif; ?>
    </div>
    <nav>
        <a href="accueil.php">Retour à l'accueil</a>
        <a href="logout.php">Se déconnecter</a>
    </nav>
</body>
</html>
