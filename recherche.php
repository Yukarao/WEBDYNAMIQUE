<?php
// Si un mot clé est soumis
$pages = [
    'accueil.php',
    'liste_biens.php',
    'fiche_bien.php',
    'compte_client.php',];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche</title>
</head>
<body>
    <h1>Recherche</h1>
    <form id="formRecherche">
        <input type="text" id="motCle" placeholder="Entrez un mot-clé..." required>
        <button type="submit">Rechercher</button>
    </form>
	
	<div id="resultats"></div>
	
<script>
const pages = <?= json_encode($pages) ?>;
const form = document.getElementById('formRecherche');
const resultatsDiv = document.getElementById('resultats');

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    resultatsDiv.innerHTML = '';
    const mot = document.getElementById('motCle').value.toLowerCase();

for (const page of pages) {
    const res = await fetch(page);
     const html = await res.text();
    const div = document.createElement('div');
     div.innerHTML = html;

    const texteVisible = div.innerText.toLowerCase();
		if (texteVisible.includes(mot)) {
            const index = texteVisible.indexOf(mot);
            const extrait = texteVisible.substring(index - 50, index + 50);
            const resultat = document.createElement('div');
                    resultat.classList.add('resultat');
                    resultat.innerHTML = `
                        <a href="${page}" target="_blank">${page}</a><br>
                        <em>...${extrait.replace(mot, '<mark>' + mot + '</mark>')}...</em>
                    `;
                    resultatsDiv.appendChild(resultat);
                }
            }
});
</script>
</body>
</html>