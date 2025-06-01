<link rel="stylesheet" href="style.css">
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
    $id = intval($_POST['id']);
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $role = htmlspecialchars(trim($_POST['role']));

    if ($email) {
        $stmt = $pdo->prepare("UPDATE utilisateur SET nom = ?, prenom = ?, email = ?, role = ? WHERE id_utilisateur = ?");
        $stmt->execute([$nom, $prenom, $email, $role, $id]);

        header("Location: admin.php?message=modification_reussie");
        exit;
    } else {
        $erreur = "Email invalide.";
    }
} elseif (isset($_GET['id'])) {
    // Chargement infos
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = ?");
    $stmt->execute([$id]);
    $utilisateur = $stmt->fetch();

    if (!$utilisateur) {
        echo "Utilisateur introuvable.";
        exit;
    }
} else {
    echo "ID utilisateur manquant.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Utilisateur</title>
</head>
<body>
    <h2>Modifier l'utilisateur</h2>

    <?php if (isset($erreur)) echo "<p style='color:red;'>$erreur</p>"; ?>

    <form method="post" action="modifier_utilisateur.php">
        <input type="hidden" name="id" value="<?php echo $utilisateur['id_utilisateur']; ?>">

        <label>Nom : <input type="text" name="nom" value="<?php echo htmlspecialchars($utilisateur['nom']); ?>"></label><br><br>
        <label>Prénom : <input type="text" name="prenom" value="<?php echo htmlspecialchars($utilisateur['prenom']); ?>"></label><br><br>
        <label>Email : <input type="text" name="email" value="<?php echo htmlspecialchars($utilisateur['email']); ?>"></label><br><br>
        <label>Rôle :
            <select name="role">
                <option value="Client" <?php if ($utilisateur['role'] === 'Client') echo 'selected'; ?>>Client</option>
                <option value="Agent" <?php if ($utilisateur['role'] === 'Agent') echo 'selected'; ?>>Agent</option>
                <option value="Admin" <?php if ($utilisateur['role'] === 'Admin') echo 'selected'; ?>>Admin</option>
            </select>
        </label><br><br>

        <input type="submit" value="Modifier">
    </form>

    <p><a href="admin.php">Retour à la liste</a></p>
</body>
</html>