<link rel="stylesheet" href="style.css">
<?php
// Connexion a la base de données
$host = 'localhost';
$db = 'omnes_immobilier';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Verification des données du formulaire
if (
    isset($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['mot_de_passe'], $_POST['role']) &&
    !empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['email']) && !empty($_POST['mot_de_passe']) && !empty($_POST['role'])
) {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $mot_de_passe = $_POST['mot_de_passe'];
    $role = htmlspecialchars(trim($_POST['role']));
	$specialite = isset($_POST['specialite']) ? htmlspecialchars(trim($_POST['specialite'])) : null;


    if (!$email) {
        die("Email invalide.");
    }

    // On verifie si l'email est deja associe a un compte
    $stmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        die("Cet email est déjà utilisé.");
    }

    // Hashage mdp
    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Insertion dans la base
    $stmt = $pdo->prepare("INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $prenom, $email, $mot_de_passe_hash, $role]);
	$id_utilisateur = $pdo->lastInsertId();

	// si cest un agent on lajoute a la table liee
	if ($role === 'Agent') {
        $stmt = $pdo->prepare("INSERT INTO agent (id_utilisateur, telephone, agence, specialite) VALUES (?, NULL, NULL, ?)");
		$stmt->execute([$id_utilisateur, $specialite]);
    }

    echo "Inscription réussie. <a href='auth.php'>Se connecter</a>";
} else {
    echo "Veuillez remplir tous les champs.";
}
?>