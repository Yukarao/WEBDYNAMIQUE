<link rel="stylesheet" href="style.css">
<?php
session_start();
if (!isset($_SESSION['id_utilisateur'])) exit;

$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$id_utilisateur = $_SESSION['id_utilisateur'];
$id_contact = (int) $_GET['contact_id'] ?? 0;

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

foreach ($messages as $msg) {
    $align = $msg['id_expediteur'] == $id_utilisateur ? 'right' : 'left';
    echo "<div style='text-align: $align; margin: 5px;'>";
    echo "<strong>{$msg['prenom']} {$msg['nom']}</strong><br>";
    echo nl2br(htmlspecialchars($msg['contenu'])) . "<br>";
    echo "<small>{$msg['date_envoi']}</small>";
    echo "</div><hr>";
}
