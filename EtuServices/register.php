<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $pdo = connectToDatabase();

    $stmt = $pdo->prepare('INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)');
    $stmt->execute([$nom, $prenom, $email, $password]);

    echo "<p>Utilisateur enregistré avec succès !</p>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Inscription</h1>
    </header>
    <form action="register.php" method="post">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required>
        <br>
        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" required>
        <br>
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">S'inscrire</button>
    </form>
    <nav>
        <a href="accueil.php">Retour à l'accueil</a>
    </nav>
</body>
</html>
