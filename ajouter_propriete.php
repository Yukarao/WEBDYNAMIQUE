<?php
session_start();

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Admin') {
    header("Location: auth.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$id_admin = $_SESSION['id_utilisateur'];


$agents = $pdo->query("SELECT id_utilisateur, nom, prenom FROM utilisateur WHERE role = 'Agent'")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $adresse = $_POST['adresse'];
    $ville = $_POST['ville'];
    $prix = $_POST['prix'];
    $type = $_POST['type_bien'];
    $superficie = $_POST['superficie'];
    $id_agent = $_POST['id_agent'];

    $stmt = $pdo->prepare("INSERT INTO propriete (id_admin, id_agent, titre, description, adresse, ville, prix, type_bien, superficie) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id_admin, $id_agent, $titre, $description, $adresse, $ville, $prix, $type, $superficie]);

    header("Location: auth.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une propriété</title>
</head>
<body>
<h2>Ajouter une propriété</h2>

<form method="post">
    <label>Titre : <input type="text" name="titre" required></label><br><br>
    <label>Description : <textarea name="description" required></textarea></label><br><br>
    <label>Adresse : <input type="text" name="adresse" required></label><br><br>
    <label>Ville : <input type="text" name="ville" required></label><br><br>
    <label>Prix (€) : <input type="number" step="0.01" name="prix" required></label><br><br>
    <label>Type de bien : <input type="text" name="type_bien" required></label><br><br>
    <label>Superficie (m²) : <input type="number" name="superficie" required></label><br><br>

    <label>Agent responsable :
        <select name="id_agent" required>
            <option value="">-- Choisir un agent --</option>
            <?php foreach ($agents as $agent): ?>
                <option value="<?= $agent['id_utilisateur'] ?>">
                    <?= htmlspecialchars($agent['prenom'] . ' ' . $agent['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label><br><br>

    <input type="submit" value="Ajouter">
</form>

<br><a href="admin.php">Retour</a>	
</body>
</html>
