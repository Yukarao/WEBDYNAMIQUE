<link rel="stylesheet" href="style.css">
<?php
session_start();
// Connexion a la base de données
$host = 'localhost';
$db = 'omnes_immobilier';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Verification des données du formulaire
if (
    isset($_POST['email'], $_POST['mot_de_passe']) &&
    !empty($_POST['email']) && !empty($_POST['mot_de_passe'])
) {
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $mot_de_passe = $_POST['mot_de_passe'];
 
	 if (!$email) {
        die("Email invalide.");
    }
    

    // Recherche l'utilisateur dans la base
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
    $stmt->execute([$email]);
		$user = $stmt->fetch();

    if ($user && password_verify($mot_de_passe, $user['mot_de_passe']))
		{
        // on stocke les infos
        $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];
        $_SESSION['role'] = $user['role'];
		$_SESSION['email'] = $user['email'];

        // Redirection vers la page de compte selon le rôle
        if ($user['role'] === 'Admin') {
        header("Location: admin.php");
		} elseif ($user['role'] === 'Agent') {
        header("Location: compte_agent.php");
		} else {
        header("Location: compte_client.php");
		}
    exit;
    }else {
        echo "Identifiants incorrects.";
    }
} else {
    echo "Veuillez remplir tous les champs.";
}
?>