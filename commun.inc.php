<?php
////	INIT
@define("MODULE_NOM","suivi");
@define("MODULE_PATH","module_suivi");
require_once "../includes/global.inc.php";
$objet["suivi_dossier"]	= array("type_objet"=>"suivi_dossier", "cle_id_objet"=>"id_dossier", "type_contenu"=>"suivi", "cle_id_contenu"=>"id_suivi", "table_objet"=>"gt_suivi_dossier");
$objet["suivi"]			= array("type_objet"=>"suivi", "cle_id_objet"=>"id_suivi", "type_conteneur"=>"suivi_dossier", "cle_id_conteneur"=>"id_dossier", "table_objet"=>"gt_suivi");

$objet["contact_dossier"]	= array("type_objet"=>"contact_dossier", "cle_id_objet"=>"id_dossier", "type_contenu"=>"contact", "cle_id_contenu"=>"id_contact", "table_objet"=>"gt_contact_dossier");
$objet["contact"]			= array("type_objet"=>"contact", "cle_id_objet"=>"id_contact", "type_conteneur"=>"contact_dossier", "cle_id_conteneur"=>"id_dossier", "table_objet"=>"gt_contact");
$objet["contact_dossier"]["champs_recherche"]	= array("nom","description");
$objet["contact"]["champs_recherche"]			= array("nom","prenom","adresse","codepostal","ville","pays","competences","hobbies","fonction","societe_organisme","commentaire");
$objet["contact"]["tri"] = modif_tri_defaut_personnes(array("nom@@asc","nom@@desc","prenom@@asc","prenom@@desc","civilite@@asc","civilite@@desc","date_crea@@desc","date_crea@@asc","date_modif@@desc","date_modif@@asc","codepostal@@asc","codepostal@@desc","ville@@asc","ville@@desc","pays@@asc","pays@@desc","id_utilisateur@@asc","id_utilisateur@@desc"));


patch_dossier_racine($objet["suivi_dossier"]);
	
//// FONCTION MENU CONTEXTUEL
function menu_contextuel_suivi($contact, $contact_tmp, $periodicite){
global $trad;
	////	MODIF / SUPPR / INFOS / CREATION USER (admin g�n�ral)
		$cfg_menu_elem = array("objet"=>$contact, "objet_infos"=>$contact_tmp);
		$contact_tmp["droit_acces"] = ($_GET["id_dossier"]>1)  ?  $droit_acces_dossier  :  droit_acces($contact,$contact_tmp);
		if($contact_tmp["droit_acces"]>=2)	{			
 		
		if($_SESSION["user"]["admin_general"]==1)	$cfg_menu_elem["options_divers"][] = array("icone_src"=>PATH_TPL."divers/ajouter.png", "text"=>$trad["CONTACT_creer_user"], "action_js"=>"confirmer('".addslashes($trad["CONTACT_creer_user_infos"])."','index.php?action=creer_user&id_contact=".$contact_tmp["id_contact"]."');"); 
 }
	////	DIV SELECTIONNABLE + OPTIONS
		$cfg_menu_elem["id_div_element"] = div_element_suivi($contact, $contact_tmp["id_contact"],$periodicite);
		$cfg_menu_elem["taille_icone"]="small_inline";
		$cfg_menu_elem["modif"] = true;
		require "element_menu_contextuel_suivi.php";

}

////	PREMIER ET DERNIER MILLESIMES DU SUIVI
////
function millesimes_suivi(){
	$result = mysql_query("SHOW COLUMNS FROM gt_sx_saisie" );
	$prem_s =2099; $dernier = 0;
	while ($champ = mysql_fetch_assoc($result)) {
		$annee = substr($champ['Field'], -4); 
		if(is_numeric($annee)){
			$prem_s 	= ($prem_s > $annee) ? $annee+1 : $prem_s;
			$dernier	= ($dernier < $annee) ? $annee : $dernier;
		}
	}
	return array($prem_s, $dernier);
}

////	ALTERRE LES TABLES POUR CREATION NOUVEAU MILLESIME
////
function alter_tables($annee){
	//	Table saisie
	$label_mois	=  array("Sjanv","Sfev","Smar","Savr","Smai","Sjuin","Sjuil","Saou","Ssept","Soct","Snov","Sdec");
	foreach($label_mois as $val){
		db_maj_champ_ajoute("no_control", "gt_sx_saisie", $val.$annee, "ALTER TABLE `gt_sx_saisie` ADD `".$val.$annee."` char(1) NOT NULL");
	}
	
	// Table contact
	$label_champ = array("imposition_", "regime_", "is_", "tva_", "tvaexercice_", "moiscloture_");
	foreach($label_champ as $champ){
		db_maj_champ_ajoute("no_control", "gt_contact", $champ.$annee, "ALTER TABLE `gt_contact` ADD `".$champ.$annee."` TINYTEXT NULL");
	}
}

////	ALTERRE LES TABLES POUR SUPPRESSION PREMIER MILLESIME
////
function alter_tables_suppr($annee){
	
	// Table contact
	$label_champ = array("imposition_", "regime_", "is_", "tva_", "tvaexercice_", "moiscloture_");
	foreach($label_champ as $champ){
		db_query("ALTER TABLE `gt_contact` DROP `".$champ.$annee."`;", true);
	}
	
	$annee = $annee-1;
	//	Table saisie
	$label_mois	=  array("Sjanv","Sfev","Smar","Savr","Smai","Sjuin","Sjuil","Saou","Ssept","Soct","Snov","Sdec");
	foreach($label_mois as $val){
		db_query("ALTER TABLE `gt_sx_saisie` DROP `".$val.$annee."`;", false);
	}
	alert('e');
}

////	SUPPRIME LES TABLES PREMIER MILLESIME 
////
function suppr_tables($annee){
	$tables = array("gt_sx_tva_", "gt_sx_cloture_");
	foreach($tables as $table){
		db_query("DROP TABLE `".$table.$annee."`;", false);
	}
}

////	AJOUTE LES TABLES CLOTURE ET TVA POUR CREATION NOUVEAU MILLESIME
////
function ajout_tables($annee){
	// Table cloture
	$requete = "CREATE TABLE IF NOT EXISTS `gt_sx_cloture_".$annee."` (`id_cloture_".$annee."` SMALLINT AUTO_INCREMENT, `id_contact` MEDIUMINT, `cloture` tinytext, `visa` tinytext, `tdfc` tinytext, `dossier` tinytext, `agoa` tinytext, `edition` tinytext, `remise` tinytext, PRIMARY KEY (`id_cloture_".$annee."`));";
	db_maj_table_ajoute("no_control", "gt_sx_cloture_".$annee."", $requete);

	
	// Table tva
	$requete = "CREATE TABLE IF NOT EXISTS `gt_sx_tva_".$annee."` (
  `id_tva_".$annee."` SMALLINT AUTO_INCREMENT,
  `id_contact` MEDIUMINT,
  `tva_a` text,
  `tva_a_date` text,
  `tva_a_montant` text,
  `tva_a_ac1` text,
  `tva_a_ac2` text,
  `tva_a_ac3` text,
  `tva_a_ac4` text,
  `tva_1t` text,
  `tva_1t_date` text,
  `tva_2t` text,
  `tva_2t_date` text,
  `tva_3t` text,
  `tva_3t_date` text,
  `tva_4t` text,
  `tva_4t_date` text,
  `tva_1m` text,
  `tva_1m_date` text,
  `tva_2m` text,
  `tva_2m_date` text,
  `tva_3m` text,
  `tva_3m_date` text,
  `tva_4m` text,
  `tva_4m_date` text,
  `tva_5m` text,
  `tva_5m_date` text,
  `tva_6m` text,
  `tva_6m_date` text,
  `tva_7m` text,
  `tva_7m_date` text,
  `tva_8m` text,
  `tva_8m_date` text,
  `tva_9m` text,
  `tva_9m_date` text,
  `tva_10m` text,
  `tva_10m_date` text,
  `tva_11m` text,
  `tva_11m_date` text,
  `tva_12m` text,
  `tva_12m_date` text,
  PRIMARY KEY (`id_tva_".$annee."`));";
	db_maj_table_ajoute("no_control", "gt_sx_tva_".$annee."", $requete);	

}


////	MISE A JOUR DES CHAMPS id EN CREATION DE MILLESIME
////
function maj_nouvelles_tables($annee){
	$annee_a_recopier = $annee-1;
	// cloture
	$tableau = db_tableau("SELECT id_cloture_".$annee_a_recopier.", id_contact FROM gt_sx_cloture_".$annee_a_recopier."");
	
	foreach($tableau as $id_cloture_tmp){ 
		db_query("INSERT INTO gt_sx_cloture_".$annee." SET id_cloture_".$annee." = ".$id_cloture_tmp['id_cloture_'.$annee_a_recopier].", id_contact =".$id_cloture_tmp["id_contact"]."");
	}
	// tva
	$tvas = db_tableau("SELECT id_tva_".$annee_a_recopier.", id_contact FROM gt_sx_tva_".$annee_a_recopier."");
	foreach($tvas as $id_tva_tmp){
		db_query("INSERT INTO gt_sx_tva_".$annee." SET id_tva_".$annee." = ".$id_tva_tmp['id_tva_'.$annee_a_recopier].", id_contact =".$id_tva_tmp["id_contact"]."");
	}
	// gt_contact
	foreach(db_colonne("SELECT id_contact FROM gt_contact") as $id_contact_tmp){
		foreach(db_tableau("SELECT imposition_".$annee_a_recopier.", regime_".$annee_a_recopier.", tva_".$annee_a_recopier.", tvaexercice_".$annee_a_recopier.", moiscloture_".$annee_a_recopier." FROM gt_contact WHERE id_contact = ".$id_contact_tmp."") as $champs_contact_tmp){
			db_query("UPDATE gt_contact SET 
			imposition_".$annee." 	= '".$champs_contact_tmp["imposition_".$annee_a_recopier.""]."',
			regime_".$annee." 		= '".$champs_contact_tmp["regime_".$annee_a_recopier.""]."',
			tva_".$annee."			= '".$champs_contact_tmp["tva_".$annee_a_recopier.""]."',
			tvaexercice_".$annee."	= '".$champs_contact_tmp["tvaexercice_".$annee_a_recopier.""]."',
			moiscloture_".$annee."	= '".$champs_contact_tmp["moiscloture_".$annee_a_recopier.""]."'
			WHERE id_contact = ".$id_contact_tmp."");
			
		}
	}
}

//// COOKIE AFFICHAGE
////
if(isset($_GET['cook-affsaisie'])) { 
	$val = $_GET['cook-affsaisie'];
	$timestamp_expire = time() + 60*24*3600;    // durée des cookies  60 jours
	setcookie("cook-affsaisie", $val, $timestamp_expire);
	
	header ( 'Location: index.php' );
}

////	CHANGEMENT MILLESIME COURANT - COULEUR DU MILLESIME
////
if(isset($_POST['anmoins']))
{
	// Verif si année demandée existe dans la BDD
	$annee = $_SESSION['millesime'];
	$annee--;
	$table = 'gt_sx_tva_'.$annee; 
	
	if(!db_maj_table_ajoute("no_control", $table))
	{
		alert("Le millésime ".$annee." n'existe pas! Demandez à votre administrateur de le créer.");
		$annee++;
	}
	else{
// gestion changement couleur année précédente
 	$coul = $_SESSION['couleur'];
	switch ($coul){
		case 'Rouge':
			$_SESSION['couleur'] = 'Vert';
			break;
		case 'Vert':
			$_SESSION['couleur'] = 'Bleu';
			break;
		case 'Bleu':
			$_SESSION['couleur'] = 'Rouge';
			break;} 
	}

	$_SESSION['millesime'] = $annee;
}
elseif(isset($_POST['anplus']))
{
	$annee = $_SESSION['millesime'];
	$annee++;
	$table = 'gt_sx_tva_'.$annee; 

	if(!db_maj_table_ajoute("no_control", $table))
	{
		alert("Le millésime ".$annee." n'existe pas! Demandez à votre administrateur de le créer.");
		$annee--;
	}
	else{
// gestion changement couleur année suivante
 	$coul = $_SESSION['couleur'];
	switch ($coul){
		case 'Rouge':
			$_SESSION['couleur'] = 'Bleu';
			break;
		case 'Vert':
			$_SESSION['couleur'] = 'Rouge';
			break;
		case 'Bleu':
			$_SESSION['couleur'] = 'Vert';
			break;} 
	}
	$_SESSION['millesime'] = $annee;
}

////	AFFICHAGE DE LA PAGE CHOISIE / PAR DEFAUT
////
function page_a_afficher($page)
{
	$page_a_afficher = "sx_saisie.php";
	switch ($page)
	{
		case "Saisie":
			$page_a_afficher = "sx_saisie.php";
			break;
		case "?":
			$page_a_afficher = "tour.php";
			break;
		case "TVA":
			$page_a_afficher = "sx_tva.php";
			break;
		case "Cloture":
			$page_a_afficher = "sx_cloture.php";
			break;
		case "Apercu":
			$page_a_afficher = "sx_apercu.php";
			break;
		case "Gestion":
			$page_a_afficher = "sx_gestion.php";
			break;
		case "Statistiques":
			$page_a_afficher = "statistiques.php";
			break;
		case "Agence":
			$page_a_afficher = "sx_stat_agence.php";
			break;
		case "Cegid":
			$page_a_afficher = "sx_cegid.php";
			break;
		default:
			$page_a_afficher = "sx_saisie.php";
			break;
	}
	return $page_a_afficher;
}

////	COULEUR DE L'INPUT EN FONCTION DE SA VALEUR
////
function coulCase($val)
{
switch ($val){
	case 'D':
		$c = 'input_suivi_jaune';	// COULEUR CASE DOCUMENTS
		break;
	case 'S':
		$c = 'input_suivi_bleu';	// COULEUR CASE SAISI
		break;
	case 'X':
		$c = 'input_suivi_vert';	// COULEUR CASES REVISE
		break;
	case 'R':
		$c = 'input_suivi_brun';	// COULEUR CASE RENDU
		break;
	case 'E':
		$c = 'input_suivi_vert'; // COULEUR CASES TVA EDI
		break;
	case 'P':
		$c = 'input_suivi_violet'; // COULEUR CASES TVA PAPIER
		break;
	default:
		$c = 'input_suivi';
		break;
	}
return $c;
}

////	COULEUR DE L'INPUT DATE EN FONCTION DE SA VALEUR
////
function coulCaseDateTva($val)
{
	//// si gestion delais / hors delais
/* 	if (isset($val) AND (!empty($val))){
		switch ($val){
			case 'E': 	$c = 'input_suivi_vert'; 	break;
			case 'P': 	$c = 'input_suivi_violet';	break;
			default:	$c = 'input_suivi';			break;
	}	}
	else{$c = 'input_suivi';} 	
	return $c;
*/
	/// sinon vide / pas vide
	if($val <> "") {$c = 'input_suivi_vert';}
	else{ $c = 'input_suivi';}
	return $c;
}

////	COULEUR DE L'INPUT DATE EN FONCTION DE SA VALEUR
////
function coulCaseDate($val, $case)
{
	if (isset($val) AND $val !=""){
	switch ($case){
		case 'cloture':
			$c = 'input_suivi_vert';
			break;
		case 'visa':
			$c = 'input_suivi_jaune';
			break;
		case 'tdfc':
			$c = 'input_suivi_jaune';
			break;
		case 'dossier':
			$c = 'input_suivi_vert';
			break;
		case 'fasja':
			$c = 'input_suivi_violet';
			break;
		case 'agoa':
			$c = 'input_suivi_bleu';
			break;
		case 'edition':
			$c = 'input_suivi_bleu';
			break;
		case 'remise':
			$c = 'input_suivi_orange';
			break;
		default :
			$c = 'input_suivi';
			break;
		}
		return $c;
	}
	else
		$c = 'input_suivi';
		return $c;
	}

////	INDIQUE SI LA CASE DOIT ETRE AFFICHER OU PAS
////
function afficheCase($MoisCloture, $moisCase, $anneeCase)
{
	if(isset($_COOKIE['cook-affsaisie']) && $_COOKIE['cook-affsaisie'] == 'mille'){
		$debut = mktime(12, 0, 0, $MoisCloture, 1, $_SESSION['millesime']-1);
		$case= mktime(12, 0, 0, $moisCase, 1, $anneeCase);
		$fin = mktime(12, 0, 0, $MoisCloture, 1, $_SESSION['millesime']);
	
		if($debut <= $case)
		{
			if($case < $fin)	{	return true;}
			else				{	return false;}
		}
		else	{					return ;}
	}
	else{							return true;}
  }

////	retourne la date limite de depot. valeur par defaut 30/04/..
////
function date_limite(){
	$limite = db_valeur("SELECT DATE_FORMAT(depot,'%d/%m/%Y') FROM gt_delais WHERE an = '".$_SESSION['millesime']."' AND  EXISTS (SELECT depot FROM gt_delais WHERE an = '".$_SESSION['millesime']."')");
	$delai = ($limite == '' || $limite == NULL) ? '30/04/'.($_SESSION['millesime']+1) : $limite;
	return $delai;
}
////	INDIQUE SI LE DELAIS DE DEPOT EST RESPECTER
////
function delais($depot, $delai) { //alert("depot : ".$depot." - delai : ".$delai);
	list($j1, $m1, $a1) = explode("/", $depot);		$date_depot = mktime(0,0,0,$m1, $j1, (2000 + $a1)); 
	$delai = date_parse($delai); $date_delai = mktime(0,0,0,$delai['month'], $delai['day'], $delai['year']); 
//	list($a2, $m2, $j2) = explode("-", $delai);		$date_delai = mktime(0,0,0,$m2, $j2, $a2); 
	if($date_depot <= $date_delai){ return true;} else { return false;}
}




?>