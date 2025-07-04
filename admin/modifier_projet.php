<?php
session_start();
require_once '../config.php';

// On récupère le paramètre 'id' de l'URL (même si la colonne s'appelle id_projet)
if (!isset($_GET['id'])) {
    header('Location: ../index.php#portfolio');
    exit;
}

$id_projet = $_GET['id']; // Le nom de variable peut être $id_projet pour plus de clarté

// Mais dans la requête on utilise id_projet comme nom de colonne
$query = "SELECT * FROM projet WHERE id_projet = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$id_projet]);
$projet = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$projet) {
    header('Location: ../index.php#portfolio');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $lien = $_POST['lien'];
    
    // Si une nouvelle image est uploadée
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Vérifications de l'image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $error = "Le fichier n'est pas une image.";
        } elseif ($_FILES["image"]["size"] > 5000000) {
            $error = "L'image est trop volumineuse (max 5MB).";
        } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $error = "Seuls les formats JPG, JPEG, PNG & GIF sont autorisés.";
        } elseif (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Suppression de l'ancienne image
            if (file_exists($projet['image_path'])) {
                unlink($projet['image_path']);
            }
            
            // Mise à jour avec la nouvelle image
            $query = "UPDATE projet SET nom = ?, description = ?, image_path = ?, lien = ? WHERE id_projet = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$nom, $description, $target_file, $lien, $id_projet]);
            
            header('Location: ../index.php#portfolio');
            exit;
        } else {
            $error = "Une erreur est survenue lors de l'upload de l'image.";
        }
    } else {
        // Mise à jour sans changer l'image
        $query = "UPDATE projet SET nom = ?, description = ?, lien = ? WHERE id_projet = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$nom, $description, $lien, $id_projet]);
        
        header('Location: ../index.php#portfolio');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le projet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Modifier le projet</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom du projet</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($projet['nom']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?= htmlspecialchars($projet['description']) ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="image" class="form-label">Nouvelle image (laisser vide pour ne pas changer)</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                <div class="mt-2">
                    <small>Image actuelle :</small><br>
                    <img src="<?= htmlspecialchars($projet['image_path']) ?>" style="max-height: 100px;">
                </div>
            </div>
            
            <div class="mb-3">
                <label for="lien" class="form-label">Lien</label>
                <input type="url" class="form-control" id="lien" name="lien" value="<?= htmlspecialchars($projet['lien']) ?>">
            </div>
            
            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
            <a href="../index.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>
</html>