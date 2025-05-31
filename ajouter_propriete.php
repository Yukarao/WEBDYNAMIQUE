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


$categories = $pdo->query("SELECT id_categorie, nom FROM categorie")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $adresse = $_POST['adresse'];
    $ville = $_POST['ville'];
    $prix = $_POST['prix'];
    $type = $_POST['type_bien'];
    $superficie = $_POST['superficie'];
    $id_agent = $_POST['id_agent'];

    $stmt = $pdo->prepare("
        SELECT a.id_agent
        FROM agent a
        JOIN categorie c ON a.specialite = c.nom
        WHERE c.id_categorie = ?
        LIMIT 1 ");
    $stmt->execute([$id_categorie]);
    $agent = $stmt->fetch();

    if (!$agent) {
        die("Aucun agent trouvé pour cette catégorie.");
    }

    $stmt = $pdo->prepare("
        INSERT INTO propriete (id_admin, titre, description, adresse, ville, prix, type_bien, superficie, id_categorie, id_agent)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$id_admin, $titre, $description, $adresse, $ville, $prix, $type_bien, $superficie, $id_categorie, $id_agent]);

    header("Location: admin.php?message=propriete_ajoutee");
    exit;
}
?>
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

    <label>Catégorie :
        <select name="id_categorie" required>
            <option value="">-- Choisir une catégorie --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id_categorie'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
            <?php endforeach; ?>
        </select>
    </label><br><br>

    <input type="submit" value="Ajouter">
</form>

<br><a href="admin.php">Retour</a>	
</body>
</html>
