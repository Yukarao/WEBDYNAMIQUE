<?php
session_start();

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Client') {
    header("Location: auth.php");
    exit;
}
$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,]);

$id_client = $_SESSION['id_utilisateur'];
$id_utilisateur_agent = (int) $_GET['id_agent'];

// Recup id_agent
$stmt = $pdo->prepare("SELECT id_agent FROM agent WHERE id_utilisateur = ?");
$stmt->execute([$id_utilisateur_agent]);
$agent_info = $stmt->fetch();

if (!$agent_info) {
    die("Erreur : agent non trouvé.");
}
$id_agent = $agent_info['id_agent'];

$stmt = $pdo->prepare("SELECT nom, prenom, email FROM utilisateur WHERE id_utilisateur = ?");
$stmt->execute([$id_utilisateur_agent]);
$agent = $stmt->fetch();

// recup creneaux
$stmt = $pdo->prepare("SELECT * FROM creneau WHERE id_agent = ? ORDER BY date, heure_debut");
$stmt->execute([$id_agent]);
$creneaux = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement resa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_creneau'])) {
    $id_creneau = $_POST['id_creneau'];
    $stmt = $pdo->prepare("SELECT * FROM creneau WHERE id_creneau = ? AND disponible = 1");
    $stmt->execute([$id_creneau]);
    $creneau = $stmt->fetch();

    if ($creneau) {
        $stmt = $pdo->prepare("INSERT INTO rendezvous (id_client, id_agent, id_creneau, date, heure) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$id_client, $id_agent, $id_creneau, $creneau['date'], $creneau['heure_debut']]);

        $stmt = $pdo->prepare("UPDATE creneau SET disponible = 0 WHERE id_creneau = ?");
        $stmt->execute([$id_creneau]);

        
        $stmt = $pdo->prepare("SELECT email, prenom FROM utilisateur WHERE id_utilisateur = ?");
        $stmt->execute([$id_client]);
        $client = $stmt->fetch();

        // Envoi mail confirmation //pb installation composer ne  marche pas
        $to = $client['email'];
        $subject = "Confirmation de rendez-vous";
        $message = "Bonjour " . $client['prenom'] . ",\n\nVotre rendez-vous avec l'agent " . $agent['prenom'] . " " . $agent['nom'] . " est confirmé pour le " . $creneau['date'] . " à " . $creneau['heure_debut'] . ".\n\nMerci de votre confiance.";
        $headers = "From: contact@omnes-immobilier.com";
        mail($to, $subject, $message, $headers);

        $confirmation = "Votre rendez-vous a été réservé et un mail de confirmation vous a été envoyé.";
    } else {
        $erreur = "Ce créneau n'est plus disponible.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Prise de RDV</title>
</head>
<body>
    <h2 align="center">Prendre rendez-vous avec <?= htmlspecialchars($agent['prenom']) ?> <?= htmlspecialchars($agent['nom']) ?></h2>

    <?php if (isset($confirmation)) echo "<p style='color:green;text-align:center;'>$confirmation</p>"; ?>
    <?php if (isset($erreur)) echo "<p style='color:red;text-align:center;'>$erreur</p>"; ?>

    <table>
        <tr><th>Date</th><th>Heure</th><th>Disponibilité</th><th>Action</th></tr>
        <?php foreach ($creneaux as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['date']) ?></td>
                <td><?= htmlspecialchars($c['heure_debut']) ?> - <?= htmlspecialchars($c['heure_fin']) ?></td>
                <td class="<?= $c['disponible'] ? 'disponible' : 'occupe' ?>">
                    <?= $c['disponible'] ? 'Disponible' : 'Occupé' ?>
                </td>
                <td>
                    <?php if ($c['disponible']): ?>
                        <form method="post" class="inline">
                            <input type="hidden" name="id_creneau" value="<?= $c['id_creneau'] ?>">
                            <button type="submit">Réserver</button>
                        </form>
                    <?php else: ?>
                        Non disponible
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p style="text-align:center;"><a href="liste_propriete.php">Retour aux biens</a></p>
</body>
</html>
