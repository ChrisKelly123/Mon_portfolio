<?php
session_start();
require_once '../config.php';

if (!file_exists('../config.php')) {
    die("Erreur : config.php introuvable. Chemin actuel : " . realpath('../config.php'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $lien = $_POST['lien'];
    
    // Gestion de l'upload d'image
    $upload_dir = '../uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = uniqid().'.'.$extension;
    $target_file = $upload_dir . $filename;
    
    // Vérifications de l'image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        $error = "Le fichier n'est pas une image.";
    } elseif ($_FILES["image"]["size"] > 5000000) {
        $error = "L'image est trop volumineuse (max 5MB).";
    } elseif (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Insertion en base de données
        $query = "INSERT INTO projet (nom, description, image_path, lien) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$nom, $description, $target_file, $lien]);
        
        header('Location: ../index.php#portfolio');
        exit;
    } else {
        $error = "Une erreur est survenue lors de l'upload de l'image.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un projet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Ajouter un nouveau projet</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom du projet</label>
                <input type="text" class="form-control" id="nom" name="nom" required>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            
            <div class="mb-3">
                <label for="image" class="form-label">Image du projet</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
            </div>
            
            <div class="mb-3">
                <label for="lien" class="form-label">Lien (optionnel)</label>
                <input type="url" class="form-control" id="lien" name="lien">
            </div>
            
            <button type="submit" class="btn btn-primary">Ajouter le projet</button>
            <a href="../index.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>
</html>