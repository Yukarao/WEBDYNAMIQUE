<?php
$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);
$resultats = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $critere = $_POST['critere'];
    $valeur = trim($_POST['valeur']);

    if ($critere === 'agent' && is_numeric($valeur)) {
        $stmt = $pdo->prepare("SELECT * FROM propriete WHERE id_agent = ?");
        $stmt->execute([$valeur]);
        $resultats = $stmt->fetchAll();
    } elseif ($critere === 'propriete' && is_numeric($valeur)) {
        $stmt = $pdo->prepare("SELECT * FROM propriete WHERE id_propriete = ?");
        $stmt->execute([$valeur]);
        $resultats = $stmt->fetchAll();
    } elseif ($critere === 'ville') {
        $stmt = $pdo->prepare("SELECT * FROM propriete WHERE ville LIKE ?");
        $stmt->execute(["%" . $valeur . "%"]);
        $resultats = $stmt->fetchAll();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche</title>
</head>
<body>
     <h1>Recherche de propriétés</h1>

    <form method="POST">
        <label for="critere">Rechercher par :</label>
        <select name="critere" id="critere" required>
            <option value="agent">Agent (ID)</option>
            <option value="propriete">Numéro de propriété</option>
            <option value="ville">Ville</option>
        </select>

    <input type="text" name="valeur" placeholder="Entrez votre valeur de recherche" required>
    <button type="submit">Rechercher</button>
    </form>

    <?php if (!empty($resultats)): ?>
        <h2>Résultats :</h2>
        <?php foreach ($resultats as $bien): ?>
                <h3><?= htmlspecialchars($bien['titre']) ?></h3>
                <p><strong>Ville :</strong> <?= htmlspecialchars($bien['ville']) ?></p>
                <p><strong>Prix :</strong> <?= number_format($bien['prix'], 2, ',', ' ') ?> €</p>
                <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($bien['description'])) ?></p>
                <a href="fiche_bien.php?id_propriete=<?= $bien['id_propriete'] ?>">Voir la fiche</a>
            </div>
        <?php endforeach; ?>
    <?php elseif ($_SERVER["REQUEST_METHOD"] === "POST"): ?>
        <p>Aucun bien trouvé pour cette recherche.</p>
    <?php endif; ?>      

</body>
</html>