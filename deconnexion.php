<?php
session_start();
unset($_SESSION['id_utilisateur']);
unset($_SESSION['role']);
unset($_SESSION['prenom']);
unset($_SESSION['nom']);
unset($_SESSION['email']);
unset($_SESSION['biens_consultes']);
session_destroy();
header("Location: auth.php");
exit;