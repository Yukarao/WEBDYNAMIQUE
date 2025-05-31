<?php
session_start();
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Admin') {
    header("Location: auth.php");
    exit;
}
$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "");

$creneaux = $pdo->query("
    SELECT c.*, u.nom, u.prenom
    FROM creneau c
    LEFT JOIN agent a ON c.id_agent = a.id_agent
    LEFT JOIN utilisateur u ON a.id_utilisateur = u.id_utilisateur
    ORDER BY c.jour, c.heure_debut
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des créneaux</title>
</head>
<body>
    <h2>Liste des créneaux</h2>
    <p><a href="modifier_creneau.php">Ajouter ou modifier un créneau</a></p>
    <p><a href="admin.php">Retour au tableau de bord</a></p>

    <?php if (count($creneaux) > 0): ?>
        <table border="1" cellpadding="8">
            <tr>
                <th>Agent</th>
                <th>Jour</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Disponible</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($creneaux as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></td>
                    <td><?= htmlspecialchars($c['jour']) ?></td>
                    <td><?= htmlspecialchars($c['heure_debut']) ?></td>
                    <td><?= htmlspecialchars($c['heure_fin']) ?></td>
                    <td><?= $c['disponible'] ? 'Oui' : 'Non' ?></td>
                    <td>
                        <form action="supprimer_creneau.php" method="POST" onsubmit="return confirm('Supprimer ce créneau ?');" style="display:inline;">
                            <input type="hidden" name="id_creneau" value="<?= $c['id_creneau'] ?>">
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Aucun créneau enregistré.</p>
    <?php endif; ?>
</body>
</html>
