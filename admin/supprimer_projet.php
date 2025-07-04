<?php
session_start();
require_once '../config.php';


if (!isset($_GET['id_projet'])) {
    header('Location: ../index.php#portfolio');
    exit;
}

$id_projet = $_GET['id'];

// Récupérer le projet pour avoir le chemin de l'image
$query = "SELECT image_path FROM projet WHERE id_projet = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$id_projet]);
$projet = $stmt->fetch(PDO::FETCH_ASSOC);

if ($projet) {
    // Supprimer l'image
    if (file_exists($projet['image_path'])) {
        unlink($projet['image_path']);
    }
    
    // Supprimer le projet de la base de données
    $query = "DELETE FROM projets WHERE id_projet = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id_projet]);
}

header('Location: ../index.php#portfolio');
exit;
?>