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
$rdvs = [];

if ($role === 'Agent') {
    // Recup de l'id_agent depuis la table agent
    $stmtAgent = $pdo->prepare("SELECT id_agent FROM agent WHERE id_utilisateur = ?");
    $stmtAgent->execute([$id_utilisateur]);
    $agent = $stmtAgent->fetch();

    if (!$agent) {
        die("Agent introuvable.");
    }
    $id_agent = $agent['id_agent'];

    
    $stmt = $pdo->prepare("
        SELECT r.*, c.jour, c.heure_debut, c.heure_fin, u.nom AS client_nom, u.prenom AS client_prenom
        FROM rendezvous r
        JOIN creneau c ON r.id_creneau = c.id_creneau
        JOIN utilisateur u ON r.id_client = u.id_utilisateur
        WHERE r.id_agent = ?
        ORDER BY c.jour, c.heure_debut
    ");
    $stmt->execute([$id_agent]);
    $rdvs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} elseif ($role === 'Admin') {
    $stmt = $pdo->query("
    SELECT r.*, c.jour, c.heure_debut, c.heure_fin,u.nom AS client_nom, u.prenom AS client_prenom,ua.nom AS agent_nom, ua.prenom AS agent_prenom
    FROM rendezvous r
    JOIN creneau c ON r.id_creneau = c.id_creneau
    JOIN utilisateur u ON r.id_client = u.id_utilisateur
    JOIN agent a ON r.id_agent = a.id_agent
    JOIN utilisateur ua ON a.id_utilisateur = ua.id_utilisateur
    ORDER BY c.jour, c.heure_debut");
    $rdvs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    die("Rôle non reconnu.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des rendez-vous</title>
</head>
<body>
    <h2>Mes rendez-vous</h2>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'annulation_ok'): ?>
        <p style="color: green;"><strong>Le rendez-vous a été annulé avec succès.</strong></p>
    <?php endif; ?>

<?php if (count($rdvs) > 0): ?>
    <table border="1" cellpadding="8">
        <tr>
            <th>Date</th>
            <th>Heure début</th>
            <th>Heure fin</th>
            <th>Client</th>
            <?php if ($role === 'Admin'): ?>
                <th>Agent</th>
            <?php endif; ?>
            <th>Actions</th>
        </tr>

         <?php foreach ($rdvs as $rdv): ?>
            <tr>
                <td><?= htmlspecialchars($rdv['date']) ?></td>
                <td><?= htmlspecialchars($rdv['heure_debut']) ?></td>
                <td><?= htmlspecialchars($rdv['heure_fin']) ?></td>

                <?php if ($role === 'Agent'): ?>
                    <td><?= htmlspecialchars($rdv['client_prenom']) ?> <?= htmlspecialchars($rdv['client_nom']) ?></td>
                <?php elseif ($role === 'Admin'): ?>
                    <td><?= htmlspecialchars($rdv['client_prenom']) ?> <?= htmlspecialchars($rdv['client_nom']) ?></td>
                    <td><?= htmlspecialchars($rdv['agent_prenom']) ?> <?= htmlspecialchars($rdv['agent_nom']) ?></td>
                <?php endif; ?>

                <td>
                    <form method="post" action="annuler_rdv.php" onsubmit="return confirm('Annuler ce rendez-vous ?');">
                        <input type="hidden" name="id_rdv" value="<?= $rdv['id_rdv'] ?>">
                        <button type="submit">Annuler</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Aucun rendez-vous trouvé.</p>
<?php endif; ?>

<br>
<?php $redirect = ($role === 'Admin') ? 'admin.php' : 'compte_agent.php'; ?>
<a href="<?= $redirect ?>">Retour à mon compte</a>
</body>
</html>
