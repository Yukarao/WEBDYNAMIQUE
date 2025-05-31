<?php
session_start();


if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: connexion.php");
    exit();
}
$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,]);
if (!isset($_SESSION['id_utilisateur']) || empty($_SESSION['id_utilisateur'])) {
    die("Erreur : vous devez être connecté pour participer à une enchère.");
}

$id_utilisateur = $_SESSION['id_utilisateur'];
if (!isset($_GET['id_propriete'])) {
    echo "ID de propriété manquant.";
    exit();
}

$id_propriete = $_GET['id_propriete'];
$message = "";

$stmtBien = $pdo->prepare("SELECT * FROM propriete WHERE id_propriete = ?");
$stmtBien->execute([$id_propriete]);
$bien = $stmtBien->fetch();

if (!$bien) {
    die("Bien introuvable.");
}

$prix_initial = $bien['prix'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['montant_offre'])) {
    $montant = floatval($_POST['montant_offre']);
	
$stmtMax = $pdo->prepare("SELECT MAX(montant_offre) AS max_offre FROM encheres WHERE id_propriete = ?");
    $stmtMax->execute([$id_propriete]);
    $max = $stmtMax->fetch()['max_offre'] ?? 0;

if ($montant <= $max) {
        $message = "Votre offre doit être supérieure à l'offre actuelle de " . number_format($max, 2, ',', ' ') . " €.";
    } else {
  
        $stmt = $pdo->prepare("INSERT INTO encheres (id_propriete, id_utilisateur, montant_offre) VALUES (?, ?, ?)");
        $stmt->execute([$id_propriete, $id_utilisateur, $montant]);

        if ($montant >= 2 * $prix_initial) {
            $message = "Votre offre est supérieure ou égale au double du prix initial. Le bien est automatiquement à vous !";
			$gagne_automatiquement = true;
		} else {
            $message = "Votre offre a bien été enregistrée.";
			$gagne_automatiquement = false;
        }
    }
}	
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Participer à l'enchère - <?= htmlspecialchars($bien['titre']) ?></title>
</head>
<body>
    <h1>Enchère pour : <?= htmlspecialchars($bien['titre']) ?></h1>
    <p><strong>Ville :</strong> <?= htmlspecialchars($bien['ville']) ?></p>
    <p><strong>Prix de départ :</strong> <?= number_format($bien['prix'], 2, ',', ' ') ?> €</p>
    <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($bien['description'])) ?></p>

    <form method="POST">
        <label for="montant_offre">Votre offre (€) :</label><br>
        <input type="number" name="montant_offre" min="<?= $bien['prix'] ?>" step="1000" required><br><br>
        <button type="submit">Proposer une enchère</button>
    </form>

    <?php if ($message): ?>
        <p style="color: <?= strpos($message, 'bien') ? 'green' : 'red' ?>"><?= $message ?></p>
    <?php endif; ?>
	
	<?php if (!empty($gagne_automatiquement)): ?>
    <p><a href="panier.php" style="background: green; color: white; padding: 10px; text-decoration: none;">🛒 Voir mon panier</a></p>
	<?php endif; ?>
    <p><a href="enchere.php">Retour aux enchères</a></p>
</body>
</html>