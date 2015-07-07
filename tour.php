<?php

//// ENREGISTREMENT DES POST
////


	
////	MENU CHEMIN + OBJETS_DOSSIERS
////
// echo menu_chemin($objet["contact_dossier"], $_GET["id_dossier"]);
$cfg_dossiers = array("objet"=>$objet["contact_dossier"], "id_objet"=>$_GET["id_dossier"]);
require_once PATH_INC."dossiers.inc.php";



////	LISTE DES CONTACTS
////
$liste_contacts = db_tableau("SELECT * FROM gt_contact T1, gt_sx_saisie T2
	WHERE T1.id_contact = T2.id_contact 
	AND T1.id_dossier='".intval($_GET["id_dossier"])."'
	AND T1.debut_activite <= '".$_SESSION['millesime']."' 
	AND (T1.fin_activite = '' OR T1.fin_activite = '0' OR T1.fin_activite >= '".$_SESSION['millesime']."')
	".sql_affichage($objet["contact"],$_GET["id_dossier"],"T1.")); 
//echo'<pre>'.print_r($liste_contacts).'</pre>';
	
if(count($liste_contacts)>0){

	
	//// STYLE DIV + TITRE 
	$style_div = "width:99%;margin:0 0 0 5%;border-bottom:#555 solid 1px;border-radius:2px;height:40px;";
	$style_div_contenu = "width:80%;margin:0 0 0 10%;border-bottom:#555 solid 1px;border-radius:2px;height:30px";
	$style_titre = "color: #0B243B; font-size: 1.8em; text-shadow: 2px 2px 5px #000000; margin-top:5px;";
	
	////	MILLESIMES CREES
	////
	$millesimes = millesimes_suivi();		
	$mois	=  array("Sjanv","Sfev","Smar","Savr","Smai","Sjuin","Sjuil","Saou","Ssept","Soct","Snov","Sdec");

	//// INITIALISATION CARDINALITE DOSSIER (colone de gauche)
	////
	$cardinalite = 1;
	
	

?>

	<h1 style="text-align:center;margin-top:20px; margin-bottom:50px;" class="suivi">A qui le tour</h1>


<!--	///  SAISIE         -->
	<div id="div_elem_0" class="div_elem_suivi_no_hover" style="<?php echo $style_div; ?> ">
		<div class="div_elem_contenu">
		<table class="div_elem_table">
		<tr >
			<td style="padding-left: 10%;" ><h2 style="<?php echo $style_titre; ?>" >Saisie</h2></td>
		</tr>
		</table>
		</div>
	</div>
	
	<?php

	//// 
	foreach($liste_contacts as $contact_tmp){
		foreach($contact_tmp as $contact => $val){
			if(substr($contact, 0, 1) == 'S'){
				if($val == 'D') {
					$dossier = $contact_tmp['civilite']." ".$contact_tmp['nom']." ".$contact_tmp['prenom'];
					$interval = 0;
				if($contact_tmp['depot_docs']){
					list($an, $m, $j) = explode("-", $contact_tmp['depot_docs']);
					$date_dep = $j."-".$m."-".$an;
					$interval = date_diff(new DateTime($contact_tmp['depot_docs']), new DateTime());
					$interval = $interval->format('%R%a jours');
				}
				
		echo "<div id='div_elem_0' class='div_elem_suivi' style='". $style_div_contenu .";'>";
		echo "<div class='div_elem_contenu'><table class='div_elem_table'>";
		echo "<tr >";
		echo "<td class='div_elem_td' style='padding-left: 20%;' >".$dossier." documents déposés le ".$date_dep."</td>";
		echo "<td class='div_elem_td' style='width:120px;'>".$interval."</td>";
		echo "</tr></table></div></div>";
						break;
				}
			}
		}
	}
?>
	</td>
	</tr></table>

<!--	///  REVISION         -->
	<div id="div_elem_0" class="div_elem_suivi_no_hover" style="<?php echo $style_div; ?> margin-top:30px;">
		<div class="div_elem_contenu">
		<table class="div_elem_table">
		<tr >
			<td style="padding-left: 10%;" ><h2 style="<?php echo $style_titre; ?>" >Révision</h2></td>
		</tr>
		</table>
		</div>
	</div>
	
	<?php

	//// 
	foreach($liste_contacts as $contact_tmp){
		foreach($contact_tmp as $contact => $val){
				if(substr($contact, 0, 1) == 'S'){
					if($val == 'S') {
						$dossier = $contact_tmp['civilite']." ".$contact_tmp['nom']." ".$contact_tmp['prenom'];
					$interval = 0;
				if($contact_tmp['saisie_docs']){
						list($an, $m, $j) = explode("-", $contact_tmp['saisie_docs']);
					$date_dep = $j."-".$m."-".$an;
					$interval = date_diff(new DateTime($contact_tmp['saisie_docs']), new DateTime());
					$interval = $interval->format('%R%a jours');
				}	
				
		echo "<div id='div_elem_0' class='div_elem_suivi' style='". $style_div_contenu .";'>";
		echo "<div class='div_elem_contenu'><table class='div_elem_table'>";
		echo "<tr >";
		echo "<td class='div_elem_td' style='padding-left: 20%;' >".$dossier." : saisie faite depuis le ".$date_dep."</td>";
		echo "<td class='div_elem_td' style='width:120px;'>".$interval."</td>";
		echo "</tr></table></div></div>";
						break;
				}
			}
		}
	}
?>
	</td>
	</tr></table>
		

<!--	///  REMISE DOCS         -->
	<div id="div_elem_0" class="div_elem_suivi_no_hover" style="<?php echo $style_div; ?> margin-top:30px;">
		<div class="div_elem_contenu">
		<table class="div_elem_table">
		<tr >
			<td style="padding-left: 10%;" ><h2 style="<?php echo $style_titre; ?>" >Derniers Docs Rendus</h2></td>
		</tr>
		</table>
		</div>
	</div>
	
	<?php

	//// 
	foreach($liste_contacts as $contact_tmp){
		foreach($contact_tmp as $contact => $val){
				if(substr($contact, 0, 1) == 'S'){
					if($val == 'R') {
						$dossier = $contact_tmp['civilite']." ".$contact_tmp['nom']." ".$contact_tmp['prenom'];
					$interval = 0;
				if($contact_tmp['rendu_docs']){
						list($an, $m, $j) = explode("-", $contact_tmp['rendu_docs']);
					$date_dep = $j."-".$m."-".$an;
					$interval = date_diff(new DateTime($contact_tmp['rendu_docs']), new DateTime());
					$interval = $interval->format('%R%a jours');
				}	
				
		echo "<div id='div_elem_0' class='div_elem_suivi' style='". $style_div_contenu .";'>";
		echo "<div class='div_elem_contenu'><table class='div_elem_table'>";
		echo "<tr >";
		echo "<td class='div_elem_td' style='padding-left: 20%;' >".$dossier." : documents rendus le ".$date_dep."</td>";
		echo "<td class='div_elem_td' style='width:120px;'>".$interval."</td>";
		echo "</tr></table></div></div>";
						break;
				}
			}
		}
	}
?>
	</td>
	</tr></table>



<?php
}

	////	AUCUN CONTACT
	else  echo "<div class='div_elem_aucun' style ='margin-top:50px;'>".$trad["CONTACT_aucun_contact"]."</div>";
?>