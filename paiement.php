<?php
session_start();
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Client') {
    header("Location: auth.php");
    exit;
}
$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$id_client = $_SESSION['id_utilisateur'];
$id_propriete = $_GET['id_propriete'];
$montant = $_GET['prix'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_carte = $_POST['nom_carte'];
    $adresse = $_POST['adresse'];
    $numero = $_POST['numero'];
    $expiration = $_POST['expiration'];
    $code = $_POST['code'];
    $solde = $_POST['solde'];

    $statut = $solde >= $montant ? 'accepté' : 'refusé';

    $stmt = $pdo->prepare("INSERT INTO paiement (id_client, id_propriete, nom_carte, adresse_facturation, numero_carte, expiration, code_securite, montant, solde_disponible, statut)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id_client, $id_propriete, $nom_carte, $adresse, $numero, $expiration, $code, $montant, $solde, $statut]);

    if ($statut === 'accepté') {
    // Enregistrement
    $stmt = $pdo->prepare("INSERT INTO achat (id_client, id_propriete) VALUES (?, ?)");
    $stmt->execute([$id_client, $id_propriete]);

    // Marquer comme vendue
    $stmt = $pdo->prepare("UPDATE propriete SET statut = 'vendue' WHERE id_propriete = ?");
    $stmt->execute([$id_propriete]);

    $message = "Paiement accepté ! La propriété est maintenant à vous.";
} else {
        $message = "Paiement refusé : solde insuffisant.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement</title>
</head>
<body>
<h2>Finaliser l'achat</h2>
<?php if ($message): ?>
    <p style="color: <?= strpos($message, 'accepté') !== false ? 'green' : 'red' ?>"><?= $message ?></p>
	<p><a href="paiement.php">Retourner au paiement</a></p>
    <p><a href="compte_client.php">Retour à mon compte</a></p>
<?php else: ?>
    <form method="POST">
        <label>Nom sur la carte : <input type="text" name="nom_carte" required></label><br><br>
        <label>Adresse de facturation : <input type="text" name="adresse" required></label><br><br>
        <label>Numéro de carte : <input type="text" name="numero" pattern="[0-9]{16}" maxlength="16" required></label><br><br>
        <label>Date d'expiration : <input type="month" name="expiration" required></label><br><br>
        <label>Code de sécurité : <input type="text" name="code"  pattern="[0-9]{3,4}" maxlength="4" required></label><br><br>
        <label>Solde disponible : <input type="number" name="solde" min="0" step="10000" required></label><br><br>
        <button type="submit">Valider le paiement</button>
     </form>
<?php endif; ?>
</body>
</html>