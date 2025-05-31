<?php
session_start();
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Client') {
    header("Location: auth.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,]);

$id_client = $_SESSION['id_utilisateur'];

$sql = "SELECT p.*, e.montant_offre
FROM propriete p
JOIN encheres e ON e.id_propriete = p.id_propriete
WHERE p.id_categorie = 5
	AND p.statut != 'vendue'
  AND e.id_utilisateur = ?
  AND e.montant_offre = (
      SELECT MAX(e2.montant_offre)
      FROM encheres e2
      WHERE e2.id_propriete = p.id_propriete
  )";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_client]);
$biens = $stmt->fetchAll();

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier - Biens remportés</title>
</head>
<body>
    <h1>Mes biens remportés aux enchères</h1>

    <?php if (count($biens) > 0): ?>
        <?php foreach ($biens as $bien): ?>
		 <div class="carte">
        <h3><?= htmlspecialchars($bien['titre']) ?></h3>
        <p><strong>Ville :</strong> <?= htmlspecialchars($bien['ville']) ?></p>
        <p><strong>Votre offre gagnante :</strong> <?= number_format($bien['montant_offre'], 2, ',', ' ') ?> €</p>
        <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($bien['description'])) ?></p>

	<?php
        $imagePath = 'images/' . $bien['id_propriete'] . '.jpg';
                if (file_exists($imagePath)): ?>
                    <img src="<?= $imagePath ?>" alt="Image du bien" width="250">
                
				<?php else: ?>
                <p><em>Aucune image disponible</em></p>
                <?php endif; ?>
				
				<div class="actions">
                    <a href="paiement.php?id_propriete=<?= $bien['id_propriete'] ?>&prix=<?= $bien['montant_offre'] ?>" class="paiement">
                         Finaliser le paiement
                    </a>
                    <a href="supprimer_offres.php?id_propriete=<?= $bien['id_propriete'] ?>" class="supprimer" onclick="return confirm('Êtes-vous sûr de vouloir annuler vos offres pour ce bien ?');">
                        Supprimer mes offres
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Vous n’avez remporté aucun bien pour le moment ou vous avez déjà payé vos achats.</p>
    <?php endif; ?>

    <p><a href="compte_client.php">Retour à mon compte</a></p>
</body>
</html>
