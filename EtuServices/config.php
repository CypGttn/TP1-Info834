<?php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'guittonc');
define('DB_USER', 'root');
define('DB_PASS', ''); // Laissez vide si vous n'avez pas défini de mot de passe pour root

// Fonction pour se connecter à la base de données
function connectToDatabase() {
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        echo 'Erreur de connexion à la base de données : ' . $e->getMessage();
        exit();
    }
}
?>
