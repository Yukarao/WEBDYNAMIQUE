<?php
session_start();

if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: auth.php");
    exit;
}
$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$id_utilisateur = $_SESSION['id_utilisateur'];
$role = $_SESSION['role'];

if (!isset($_GET['contact_id'])) {
    die("Aucun contact spécifié.");
}

$id_contact = (int) $_GET['contact_id'];


$stmt = $pdo->prepare("SELECT nom, prenom FROM utilisateur WHERE id_utilisateur = ?");
$stmt->execute([$id_contact]);
$contact = $stmt->fetch();

if (!$contact) {
    die("Utilisateur non trouvé.");}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contenu'])) {
    $contenu = trim($_POST['contenu']);
    if ($contenu !== '') {
        $stmt = $pdo->prepare("INSERT INTO message (id_expediteur, id_destinataire, contenu, date_envoi) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$id_utilisateur, $id_contact, $contenu]);
    }
}

// Historique 
$stmt = $pdo->prepare("
    SELECT m.*, u.nom, u.prenom
    FROM message m
    JOIN utilisateur u ON m.id_expediteur = u.id_utilisateur
    WHERE (m.id_expediteur = :me AND m.id_destinataire = :them)
       OR (m.id_expediteur = :them AND m.id_destinataire = :me)
    ORDER BY m.date_envoi ASC
");
$stmt->execute(['me' => $id_utilisateur, 'them' => $id_contact]);
$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Discussion avec <?= htmlspecialchars($contact['prenom'] . ' ' . $contact['nom']) ?></title>
</head>
<body>
    <h2>Discussion avec <?= htmlspecialchars($contact['prenom'] . ' ' . $contact['nom']) ?></h2>
	<div id="chatbox"></div>
	
    <form method="post"id="messageForm">
        <textarea name="contenu" rows="3" cols="50" required></textarea><br>
        <button type="submit">Envoyer</button>
    </form>

    <p><a href="messagerie.php">Retour à la messagerie</a></p>
<script>
    const chatbox = document.getElementById('chatbox');

    function loadMessages() {
        fetch('recup_messages.php?contact_id=<?= $id_contact ?>')
            .then(response => response.text())
            .then(data => {
                chatbox.innerHTML = data;
                chatbox.scrollTop = chatbox.scrollHeight; });
        }
        setInterval(loadMessages, 3000); // rafraîchissement 
        loadMessages(); 

        
        document.getElementById('messageForm').addEventListener('submit', function () {
            setTimeout(loadMessages, 500);
        });
    </script>
</body>
</html>
