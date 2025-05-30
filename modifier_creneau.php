<?php
session_start();

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Admin') {
    header("Location: auth.php");
    exit;
}
$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,]);

$agents = $pdo->query("SELECT u.id_utilisateur, u.nom, u.prenom FROM utilisateur u JOIN agent a ON u.id_utilisateur = a.id_utilisateur")->fetchAll();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_agent = $_POST['id_agent'] ?? null;
    $jours_selectionnes = $_POST['jours'] ?? [];
    $heure_debut = $_POST['heure_debut'] ?? null;
    $heure_fin = $_POST['heure_fin'] ?? null;

    if ($id_agent && !empty($jours_selectionnes) && $heure_debut && $heure_fin) {
        foreach ($jours_selectionnes as $jour) {
            for ($i = 0; $i < 4; $i++) {
                $date = date('Y-m-d', strtotime("$jour +$i week"));

                $stmt = $pdo->prepare("SELECT * FROM creneau WHERE id_agent = ? AND date = ? AND ((? < heure_fin AND ? > heure_debut))");
                $stmt->execute([$id_agent, $date, $heure_fin, $heure_debut]);
                if ($stmt->rowCount() === 0) {
                    $stmt_insert = $pdo->prepare("INSERT INTO creneau (id_agent, date, heure_debut, heure_fin, disponible) VALUES (?, ?, ?, ?, 1)");
                    $stmt_insert->execute([$id_agent, $date, $heure_debut, $heure_fin]);
                }
            }
        }
        $message = "Créneaux ajoutés/modifiés avec succès.";
    } else {
        $message = "Veuillez sélectionner un agent, au moins un jour, et renseigner les horaires.";
    }
}

$jours_semaines = [
    'monday' => 'Lundi',
    'tuesday' => 'Mardi',
    'wednesday' => 'Mercredi',
    'thursday' => 'Jeudi',
    'friday' => 'Vendredi',
    'saturday' => 'Samedi',
    'sunday' => 'Dimanche'
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modification de créneaux</title>
</head>
<body>
<h2>Modifier ou ajouter des créneaux pour un agent</h2>

<?php if (!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>

<form method="post">
    <label>Choisissez un agent :</label>
    <select name="id_agent" required>
        <option value="">-- Sélectionner --</option>
        <?php foreach ($agents as $a): ?>
            <option value="<?= $a['id_utilisateur'] ?>">
                <?= htmlspecialchars($a['prenom'] . ' ' . $a['nom']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <fieldset>
        <legend>Sélectionnez les jours de la semaine</legend>
        <?php foreach ($jours_semaines as $val => $label): ?>
            <label><input type="checkbox" name="jours[]" value="<?= $val ?>"> <?= $label ?></label><br>
        <?php endforeach; ?>
    </fieldset>
    <br>
    <label>Heure de début : <input type="time" name="heure_debut" required></label><br><br>
    <label>Heure de fin : <input type="time" name="heure_fin" required></label><br><br>

    <input type="submit" value="Mettre à jour les créneaux">
</form>

<p><a href="admin.php">Retour au tableau de bord admin</a></p>
</body>
</html>