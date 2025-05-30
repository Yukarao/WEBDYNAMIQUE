<?php
session_start();
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Client') {
    header("Location: auth.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$biens = $pdo->query("SELECT * FROM propriete")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des biens</title>
</head>
<body>
    <h2>Biens disponibles</h2>

    <?php foreach ($biens as $bien): ?>
        <h3><?= htmlspecialchars($bien['titre']) ?></h3>
        <p><strong>Ville :</strong> <?= htmlspecialchars($bien['ville']) ?></p>
        <p><strong>Prix :</strong> <?= number_format($bien['prix'], 2, ',', ' ') ?> €</p>

        <?php
        $imagePath = 'images/' . $bien['id_propriete'] . '.jpg';
        if (file_exists($imagePath)): ?>
            <img src="<?= $imagePath ?>" alt="Photo du bien" width="300">
        <?php else: ?>
            <p><em>Pas de photo disponible.</em></p>
        <?php endif; ?>

        <p>
            <a href="fiche_bien.php?id_propriete=<?= $bien['id_propriete'] ?>">Voir les détails</a>
        </p>
    </div>
<?php endforeach; ?>

    <p><a href="compte_client.php">Retour à mon espace</a></p>
</body>
</html>