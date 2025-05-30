<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion / Inscription</title>
    <style>
        .form-container {
            display: none;
        }
        .form-container.active {
            display: block;
        }
        button {
            margin: 5px;
        }
    </style>
</head>
<body>

<h2>Bienvenue sur Omnes Immobilier</h2>

<!-- BOUTONS DE SWITCH -->
<button onclick="showForm('connexion')">Connexion</button>
<button onclick="showForm('inscription')">Inscription</button>

<!-- FORMULAIRE CONNEXION -->
<div id="connexion" class="form-container active">
    <h3>Connexion</h3>
    <form action="traitement_connexion.php" method="post">
        <label>Email : <input type="email" name="email" required></label><br><br>
        <label>Mot de passe : <input type="password" name="mot_de_passe" required></label><br><br>
        <input type="submit" value="Se connecter">
    </form>
</div>

<!-- FORMULAIRE INSCRIPTION -->
<div id="inscription" class="form-container">
    <h3>Inscription</h3>
    <form action="traitement_inscription.php" method="post">
        <label>Nom : <input type="text" name="nom" required></label><br><br>
        <label>Prénom : <input type="text" name="prenom" required></label><br><br>
        <label>Email : <input type="email" name="email" required></label><br><br>
        <label>Mot de passe : <input type="password" name="mot_de_passe" required></label><br><br>
        <label>Rôle :
            <select name="role" id="roleSelect" onchange="toggleSpecialite()" required>
                <option value="">--Choisissez--</option>
                <option value="Client">Client</option>
                <option value="Agent">Agent</option>
                <option value="Admin">Admin</option>
            </select>
        </label><br><br>
		
		<div id="specialiteContainer" style="display: none;">
            <label>Spécialité : <input type="text" name="specialite" id="specialiteInput"></label><br><br>
        </div>
        <input type="submit" value="S'inscrire">
    </form>
</div>

<script>
    function showForm(formId) {
        document.querySelectorAll('.form-container').forEach(form => {
            form.classList.remove('active');
        });
        document.getElementById(formId).classList.add('active');
    }

    function toggleSpecialite() {
        const role = document.getElementById("roleSelect").value;
        const specialiteContainer = document.getElementById("specialiteContainer");
        specialiteContainer.style.display = (role === "Agent") ? "block" : "none";
    }
</script>

</body>
</html>