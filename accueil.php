<?php
$pdo = new PDO("mysql:host=localhost;dbname=omnes_immobilier;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,]);

// Carrousel : 
$carrousel = $pdo->query("SELECT * FROM propriete ORDER BY id_propriete DESC LIMIT 3")->fetchAll();

// Bien de la semaine : 
$bulletin = $pdo->query("SELECT * FROM propriete ORDER BY id_propriete DESC LIMIT 1")->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil - Omnes Immobilier</title>
	
</head>

<body>

<!--  HEADER -->
<header>
    <h1>OMNES IMMOBILIER</h1>
    <nav>
        <a href="accueil.php">Accueil</a> |
        <a href="tout_parcourir.php">Tout parcourir</a> |
        <a href="recherche.php">Recherche</a> |
    <?php if (isset($_SESSION['role'])): ?>
		<?php if ($_SESSION['role'] === 'Client'): ?>
		
			<a href="compte_client.php">Mon Compte</a>
		<?php elseif ($_SESSION['role'] === 'Agent'): ?>
		
			<a href="compte_agent.php">Mon Compte</a>
		<?php elseif ($_SESSION['role'] === 'Admin'): ?>
		
			<a href="admin.php">Mon Compte</a>
	<?php endif; ?>
	
<?php else: ?>
    <a href="auth.php">Mon Compte</a>
<?php endif; ?>
    </nav>
</header>

<!-- BIENVENUE -->
<section>
    <h2> Bienvenue chez Omnes Immobilier</h2>
    <p>
        Bienvenue sur le site officiel d’Omnes Immobilier, votre partenaire de confiance pour tous vos projets immobiliers.
    </p>
    <p>
        Que vous soyez à la recherche d’un bien à acheter, d’un agent expérimenté pour vous accompagner, ou simplement en quête d’informations fiables sur le marché, vous êtes au bon endroit.
    </p>
    <p>
        Notre plateforme intuitive vous permet d’explorer notre catalogue de propriétés, de prendre rendez-vous avec nos agents et de rester informé des actualités les plus pertinentes du secteur.
    </p>
</section>

<!-- CARROUSEL -->
<section>
    <h2>Carrousel des propriétés</h2>
    <p>
        Découvrez nos dernières propriétés mises en avant grâce à notre carrousel dynamique.
       Chaque bien est sélectionné avec soin par nos agents pour répondre à vos critères les plus exigeants : emplacement, qualité, potentiel d’investissement.
    </p>
    <p>
        Vous pouvez également rencontrer les spécialistes d’Omnes Immobilier : des professionnels passionnés à votre écoute, prêts à vous guider avec transparence et expertise.
    </p>

   <div id="carrousel" style="position: relative; width: 300px; height: 350px; overflow: hidden; margin: 20px auto;">
        <?php foreach ($carrousel as $index => $bien): ?>
            <div class="slide" style="display: <?= $index === 0 ? 'block' : 'none' ?>;">
                <p><strong><?= htmlspecialchars($bien['titre']) ?></strong></p>
                <p><?= htmlspecialchars($bien['ville']) ?> - <?= number_format($bien['prix'], 2, ',', ' ') ?> €</p>
                <?php $imagePath = 'images/' . $bien['id_propriete'] . '.jpg';
                if (file_exists($imagePath)): ?>
                    <img src="<?= $imagePath ?>" alt="Image du bien" width="250">
                <?php else: ?>
                    <p><em>Pas d’image</em></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <button id="prev" style="position:absolute;top:50%;left:0;">←</button>
        <button id="next" style="position:absolute;top:50%;right:0;">→</button>
    </div>
</section>

<!-- BULLETIN IMMOBILIER -->
<section>
    <h2>Bulletin Immobilier de la semaine</h2>
    <?php if ($bulletin): ?>
        <p>Cette semaine, dans notre bulletin :</p>
        <ul>
            <li>Visites libres organisées samedi dès 13h</li>
            <li>Un article sur la revalorisation des taux d’emprunt</li>
            <li>Le point sur les nouvelles tendances du marché locatif</li>
        </ul>
        <p><strong><?= htmlspecialchars($bulletin['titre']) ?> - <?= htmlspecialchars($bulletin['ville']) ?></strong></p>
    <?php else: ?>
        <p>Aucune donnée disponible cette semaine.</p>
    <?php endif; ?>
</section>

<!-- TÉMOIGNAGES CLIENTS -->
<section>
    <h2>Ils nous ont fait confiance :</h2>
    <p>Sophie M. – Paris : "Grâce à Omnes Immobilier, j’ai trouvé le logement idéal en un temps record."</p>
    <p>Michel L. – Lyon : "Professionnels à l’écoute, excellent accompagnement tout au long de la vente."</p>
    <p> Claire & Julien – Nantes : "Une équipe humaine et disponible. Merci pour ce bel investissement."</p>
</section>

<!--CONTACT -->

<section>

   <h2>Vous souhaitez nous joindre ou nous rencontrer ?</h2>
    <p>Email : <a href="mailto:contact@omnes-immobilier.fr">contact@omnes-immobilier.fr</a></p>
    <p>Téléphone : 01 23 45 67 89</p>
    <p>Adresse : 10 rue de Rivoli, Paris 75000</p>
    <p><div><iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2625.1406772540117!2d2.357804176526229!3d48.85552777133181!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e671fdf120b961%3A0xae63cc92c7bd822e!2s10%20Rue%20de%20Rivoli%2C%2075004%20Paris!5e0!3m2!1sfr!2sfr!4v1748639914055!5m2!1sfr!2sfr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></div></iframe></p>
</section>


<!-- FOOTER -->
<footer>
    <p>© 2025 Omnes Immobilier. All rights reserved.</p>
    <p><a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
</footer>

	<!-- Gestion carrousel -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const slides = document.querySelectorAll('#carrousel .slide');
    const prevBtn = document.getElementById('prev');
    const nextBtn = document.getElementById('next');
    let current = 0;

function showSlide(index) {
    slides.forEach((slide, i) => {
    slide.style.display = (i === index) ? 'block' : 'none';});
}

prevBtn.addEventListener('click', () => {
    current = (current - 1 + slides.length) % slides.length;
    showSlide(current);});

nextBtn.addEventListener('click', () => {
current = (current + 1) % slides.length;
 showSlide(current);});
showSlide(current);

setInterval(() => {
            current = (current + 1) % slides.length;
            showSlide(current); }, 5000); });
</script>
</body>
</html>