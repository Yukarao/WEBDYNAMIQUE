<link rel="stylesheet" href="style.css">
<?php
session_start();

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Admin') {
    header("Location: auth.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_utilisateur'])) {
    $id_utilisateur = intval($_POST['id_utilisateur']);
    
	try {
        $pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        $stmt = $pdo->prepare("SELECT id_agent FROM agent WHERE id_utilisateur = ?");
        $stmt->execute([$id_utilisateur]);
        $agent = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($agent) {
            $id_agent = $agent['id_agent'];

            $stmt = $pdo->prepare("DELETE FROM creneau WHERE id_agent = ?");
            $stmt->execute([$id_agent]);
			
            $stmt = $pdo->prepare("DELETE FROM agent WHERE id_agent = ?");
            $stmt->execute([$id_agent]);
        }
        $stmt = $pdo->prepare("DELETE FROM utilisateur WHERE id_utilisateur = ?");
        $stmt->execute([$id_utilisateur]);

        header("Location: admin.php?message=suppression_reussie");
        exit;

    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }

}else {
    echo "Requête invalide ou ID utilisateur manquant.";
}
?>