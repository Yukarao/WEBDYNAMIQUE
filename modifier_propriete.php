<?php
session_start();

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Admin') {
    header("Location: auth.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,]);
	
	if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de propriété invalide.");
	}
	
$id_propriete = (int) $_GET['id'];
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

    $stmt = $pdo->prepare("UPDATE propriete SET titre = ?, description = ?, adresse = ?, ville = ?, prix = ?, type_bien = ?, superficie = ?, id_agent = ? WHERE id_propriete = ?");
	$stmt->execute([$titre, $description, $adresse, $ville, $prix, $type, $superficie, $id_agent, $id_propriete]);

    header("Location: admin.php");
    exit;
	}

$stmt = $pdo->prepare("SELECT * FROM propriete WHERE id_propriete = ?");
$stmt->execute([$id_propriete]);
$propriete = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$propriete) {
    die("Propriété introuvable.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une propriete</title>
</head>
<body>

<h2>Modifier une propriété</h2>

<form method="post">
    <label>Titre : <input type="text" name="titre" value="<?= htmlspecialchars($propriete['titre']) ?>" required></label><br><br>
    <label>Description : <textarea name="description" required><?= htmlspecialchars($propriete['description']) ?></textarea></label><br><br>
    <label>Adresse : <input type="text" name="adresse" value="<?= htmlspecialchars($propriete['adresse']) ?>" required></label><br><br>
    <label>Ville : <input type="text" name="ville" value="<?= htmlspecialchars($propriete['ville']) ?>" required></label><br><br>
    <label>Prix (€) : <input type="number" step="0.01" name="prix" value="<?= htmlspecialchars($propriete['prix']) ?>" required></label><br><br>
    <label>Type de bien : <input type="text" name="type_bien" value="<?= htmlspecialchars($propriete['type_bien']) ?>" required></label><br><br>
    <label>Superficie (m²) : <input type="number" name="superficie" value="<?= htmlspecialchars($propriete['superficie']) ?>" required></label><br><br>
	<label>Agent responsable :
        <select name="id_agent" required>
            <option value="">-- Choisir un agent --</option>
            <?php foreach ($agents as $agent): ?>
                <option value="<?= $agent['id_utilisateur'] ?>" <?= ($agent['id_utilisateur'] == $propriete['id_agent']) ? 'selected' : '' ?>>
				<?= htmlspecialchars($agent['prenom'] . ' ' . $agent['nom']) ?>
				</option>
            <?php endforeach; ?>
        </select>
    </label>
    <input type="submit" value="Enregistrer les modifications">
</form>
<br>
<a href="admin.php">Retour au compte</a>

</body>
</html>