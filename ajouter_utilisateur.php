<?php
session_start();

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Admin') {
    header("Location: auth.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
	$mot_de_passe = $_POST['mot_de_passe'];
    $role = htmlspecialchars(trim($_POST['role']));
	$specialite = isset($_POST['specialite']) ? htmlspecialchars(trim($_POST['specialite'])) : '';

	
	if ($email && !empty($mot_de_passe)) {
        $stmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
		if ($stmt->fetch()) {
			$erreur = "Email déjà utilisé. ";
        }
		else {
		$mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?,  ?) ");
        $stmt->execute([$nom, $prenom, $email, $mot_de_passe_hash, $role]);
		
		if (strtolower(trim($role)) === 'agent') {
                $id_utilisateur = $pdo->lastInsertId();
                $stmt = $pdo->prepare("INSERT INTO agent (id_utilisateur, specialite) VALUES (?, ?)");
                $stmt->execute([$id_utilisateur, $specialite]);
        }

        header("Location: admin.php?message=ajout_reussi");
        exit;
		}
	} else {
    $erreur =" Champs invalides ou incomplets.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title> Ajouter Utilisateur</title>
</head>
<body>
    <h2> Ajouter l'utilisateur</h2>

    <?php if (!empty($erreur)) echo "<p style='color:red;'>$erreur</p>"; ?>

    <form method="post" action="ajouter_utilisateur.php">
	
        <label>Nom : <input type="text" name="nom" required></label><br><br>
        <label>Prénom : <input type="text" name="prenom" required></label><br><br>
        <label>Email : <input type="text" name="email" required></label><br><br>
		<label>Mot de passe : <input type="password" name="mot_de_passe" required></label><br><br>
        <label>Rôle :
            <select name="role" id="roleSelect" onchange="toggleSpecialiteField()">
                <option value="Client">Client</option>
                <option value="Agent">Agent</option>
                <option value="Admin">Admin</option>
            </select>
        </label><br><br>
		
		<div id="specialiteField" style="display:none;">
        <label>Spécialité : <input type="text" name="specialite" id="specialiteInput"></label><br><br>
		</div>

        <input type="submit" value="Ajouter">
    </form>
	
	<script>
	function toggleSpecialiteField() {
    const role = document.getElementById('roleSelect').value;
    document.getElementById('specialiteField').style.display = (role === 'Agent') ? 'block' : 'none';
	}
	</script>

    <p><a href="admin.php">Retour au compte</a></p>
</body>
</html>