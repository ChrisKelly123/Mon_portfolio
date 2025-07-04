<?php
// Connexion à la base de données (modifier les paramètres selon votre configuration)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "portfolio";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn) {
    echo('connexion réussie');
}else{
    echo('connexion échouée');
}

if(isset($_POST["ajouter"])) {
    // Récupérer les valeurs du formulaire
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $message = $_POST['message'];

        // Préparer et exécuter la requête d'insertion
        $sql = "INSERT INTO contact (nom, email, message) VALUES ('$nom', '$email', '$message')";

        if ($conn->query($sql) === TRUE) {
            // Header permet de faire la redirection
            header("Location: index.php");
        } else {
            echo "Erreur: " . $sql . "<br>" . $conn->error;
        }
    } 
    
    // Fermer la connexion
    $conn->close();
?>
