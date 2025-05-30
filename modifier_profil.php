<?php
session_start();
if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: connexion.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$id_utilisateur = $_SESSION['id_utilisateur'];
$role = $_SESSION['role'];


// Traitement formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $mot_de_passe = $_POST['mot_de_passe'];

    if (!empty($mot_de_passe)){
        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE utilisateur SET nom = ?, prenom = ?, mot_de_passe = ? WHERE id_utilisateur = ?");
        $stmt->execute([$nom, $prenom, $mot_de_passe_hash, $id_utilisateur]);
    } else{
        $stmt = $pdo->prepare("UPDATE utilisateur SET nom = ?, prenom = ? WHERE id_utilisateur = ?");
        $stmt->execute([$nom, $prenom, $id_utilisateur]);
    }

    $_SESSION['nom'] = $nom;
    $_SESSION['prenom'] = $prenom;

    // Mise à jour table 
    if ($role === 'Agent') {
        $telephone = htmlspecialchars(trim($_POST['telephone']));
        $agence = htmlspecialchars(trim($_POST['agence']));
        $specialite = htmlspecialchars(trim($_POST['specialite']));

        $stmt = $pdo->prepare("UPDATE agent SET telephone = ?, agence = ?, specialite = ? WHERE id_utilisateur = ?");
        $stmt->execute([$telephone, $agence, $specialite, $id_utilisateur]);
   }

    // Redirection après modif
    echo "<script>
        alert('Modifications enregistrées avec succès.');
        window.location.href = 'compte_" . strtolower($role) . ".php';
    </script>";
    exit;
}

// champs pre remplis
if($role === 'Agent') {
    $stmt = $pdo->prepare("SELECT telephone, agence, specialite FROM agent WHERE id_utilisateur = ?");
    $stmt->execute([$id_utilisateur]);
    $agent = $stmt->fetch(PDO::FETCH_ASSOC);
}?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier mon profil</title>
</head>
<body>

<h2>Modifier mes informations</h2>

<form method="post">
    <label>Nom :
        <input type="text" name="nom" value="<?= htmlspecialchars($_SESSION['nom']); ?>" required>
    </label><br><br>
    <label>Prénom :
        <input type="text" name="prenom" value="<?= htmlspecialchars($_SESSION['prenom']); ?>" required>
    </label><br><br>

    <label>Nouveau mot de passe (laisser vide si inchangé) :
        <input type="password" name="mot_de_passe">
    </label><br><br>

    <?php if ($role === 'Agent'): ?>
        <label>Téléphone :
        <input type="text" name="telephone" value="<?= htmlspecialchars($agent['telephone'] ?? '') ?>"></label><br><br>

        <label>Agence :
        <input type="text" name="agence" value="<?= htmlspecialchars($agent['agence'] ?? '') ?>"></label><br><br>

        <label>Spécialité : 
		<input type="text" name="specialite" value="<?= htmlspecialchars($agent['specialite'] ?? '') ?>"></label><br><br>
    <?php endif; ?>
    <input type="submit" value="Enregistrer">
</form>

</body>
</html>