<?php
////	INIT
define("IS_MAIN_PAGE",true);
require "commun.inc.php"; //alert("index module suivi : ".$_SESSION["millesime"]);
require PATH_INC."header_menu.inc.php";

init_id_dossier();
$droit_acces_dossier = droit_acces_controler($objet["suivi_dossier"], $_GET["id_dossier"], 1);
 
elements_width_height_type_affichage("medium","100px","liste");

////	CHOIX de la PAGE DU SUIVI A AFFICHER
if(isset($_POST["page_suivi"])) { $page_suivi = $_POST["page_suivi"];}
elseif(isset($_GET["page_suivi"])) { $page_suivi = $_GET["page_suivi"];}
elseif(isset($_SESSION["page_suivi"])) { $page_suivi = $_SESSION["page_suivi"];}
else { $page_suivi = "Saisie";}

	$_SESSION["page_suivi"] = $page_suivi;
	define("PAGE_SUIVI", $page_suivi);
////	BARRE DE NAVIGATION DU SUIVI
//
include "../module_suivi/barre.menu.php";

include page_a_afficher($page_suivi);	// Afficher apr�s la barre de menu


require PATH_INC."footer.inc.php"; 
?>