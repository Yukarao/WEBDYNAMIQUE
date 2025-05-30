<?php
session_start();

$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

 ]);

$stmt = $pdo->prepare("SELECT * FROM propriete WHERE type_bien = 'enchere'");
$stmt->execute();
$biens = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ventes par enchère - Omnes Immobilier</title>
</head>
<body>
    <h1>Immobiliers en vente par enchère</h1>

    <?php if (count($biens) > 0): ?>
        <?php foreach ($biens as $bien): ?>
            <div style="border: 1px solid #ccc; padding: 10px; margin: 10px;">
                <h3><?= htmlspecialchars($bien['titre']) ?></h3>
                <p><strong>Ville :</strong> <?= htmlspecialchars($bien['ville']) ?></p>
                <p><strong>Prix de départ :</strong> <?= number_format($bien['prix'], 2, ',', ' ') ?> €</p>
                <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($bien['description'])) ?></p>

                <?php
                $imagePath = 'images/' . $bien['id_propriete'] . '.jpg';
                if (file_exists($imagePath)): ?>
                    <img src="<?= $imagePath ?>" alt="Photo du bien" width="250">
                <?php else: ?>
                    <p><em>Pas de photo disponible</em></p>
                <?php endif; ?>

                <p><a href="fiche_bien.php?id_propriete=<?= $bien['id_propriete'] ?>">Voir la fiche</a></p>
                <p><a href="participer_enchere.php?id_propriete=<?= $bien['id_propriete'] ?>">Participer à l'enchère</a></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucun bien actuellement en vente par enchère.</p>
    <?php endif; ?>

    <p><a href="tout_parcourir.php"> Retour à Tout Parcourir</a></p>
	<p><a href="accueil.php">Retour à l'accueil</a></p>
</body>
</html>