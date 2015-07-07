<?php
////	INIT
define("NO_MODULE_CONTROL",true);
require "commun.inc.php";
require_once PATH_INC."header.inc.php";
// INFOS + DROIT ACCES + LOGS
$espace_tmp = (isset($_GET["id_espace"]))  ?  info_espace($_GET["id_espace"])  :  array("id_espace"=>"0");
if($_SESSION["espace"]["droit_acces"]<2)	exit;

	////	DERNIER ELEMENT DU TABLEAU
	function last($array) {
            if (!is_array($array)) { return $array; }
            if (!count($array)) { return null; }
            return $array[count($array)-1];
	} 

////	VALIDATION DU FORMULAIRE
////
if(isset($_POST["id_espace"]))
{
	////	CREATION NOUVEAU MILLESIME DU SUIVI
	if(isset($_POST["millesime_suivant"]) && isset($_POST["confirm_changement"]) && $_POST["confirm_changement"] == "Oui" && isset($_POST["a_creer"])){
	
		$millesimes = millesimes_suivi();
		$annee_a_creer = intval(last($millesimes))+1;
		if($_POST["a_creer"] == $annee_a_creer){ 
		 	alter_tables($annee_a_creer);			// Tables gt_saisie et gt_contact
			ajout_tables($annee_a_creer);			// Tables gt_sx_cloture_ et gt_sx_tva_
			maj_nouvelles_tables($annee_a_creer);	// Recopie des champs 'id_' pour garder la correspondance 
		}
	////	LOGS  
	add_logs("modif", "", "", $trad["ESPACES_parametrage_suivi_creation"]." : ".$_POST["a_creer"]);
	}
	
	////	SUPPRESSION PREMIER MILLESIME DU SUIVI
	if(isset($_POST["millesime_supprime"]) && isset($_POST["confirm_suppression"]) && $_POST["confirm_suppression"] == "Oui" && isset($_POST["a_supprimer"])){
	
		$millesimes = millesimes_suivi();
		$annee_a_supprimer = $millesimes[0];
		if($_POST["a_supprimer"] == $annee_a_supprimer){ 
		 	alter_tables_suppr($annee_a_supprimer);			// Tables gt_saisie et gt_contact
			suppr_tables($annee_a_supprimer);			// Tables gt_sx_cloture_ et gt_sx_tva_
		}
	////	LOGS  
	add_logs("modif", "", "", $trad["ESPACES_parametrage_suivi_suppression"]." : ".$annee_a_supprimer);
	}
	
	////	 MODIFICATION DATE DE DEPOT
	if(isset($_POST['date_depot']) && $_POST['date_depot'] != "" && $_POST['confirm_changement_date_hidden'] == 1 ){
	
		$nouv_date = htmlspecialchars(strip_tags($_POST['date_depot']), ENT_QUOTES);

		if(date_parse($nouv_date)){
		$new_date = explode('/',$nouv_date );
alert($new_date[2]);
		if(strlen($new_date[2]) == 2) $new_date[2] = 2000+intval($new_date[2]);
		$nouv_date = $new_date[2]."-".$new_date[1]."-".$new_date[0];
		if(!isset($_POST['update_date'])){
			db_query("INSERT INTO gt_delais SET an = '".$_SESSION['millesime']."', depot = '".$nouv_date."'");
		}else{
			db_query("UPDATE gt_delais SET depot = '".$nouv_date."' WHERE an = ".$_SESSION['millesime']."");
		}
		}
	////	LOGS  
	add_logs("modif", "", "", $trad["ESPACES_parametrage_suivi_modif_date_depot"]." : ".$_SESSION['millesime']." : ".$nouv_date);
	}

	////	RELOAD L'ESPACE (+ REDIR VERS "/module_espaces/" SI BESOIN)  OU  RECHARGE JUSTE LA PAGE
	$page_redir = ($_POST["id_espace"]==$_SESSION["espace"]["id_espace"])  ?  "index.php?id_espace_acces=".$_POST["id_espace"]  :  "";
	if($page_redir!="" && preg_match("/".MODULE_PATH."/i",$_POST["page_origine"]))	{
$page_redir .= "&redir_module_path=".MODULE_PATH;
}
	reload_close($page_redir);
}
?>


<style type="text/css">
table			{ width:100%; margin-bottom:5px; font-weight:bold; }
.module_line	{ display:table; width:100%; padding:4px; }
.module_cell	{ display:table-cell; }
.module_cell2	{ display:table-cell; width:30px; }
#sortable_modules				{ list-style-type: none; margin: 0; padding: 0; width: 100%; }
#sortable_modules li			{ margin: 2px; min-height: 25px; }
#sortable_modules li.highlight	{ border:1px dashed #aaa; height:50px; } /*"module fantome" durant le déplacement*/
</style>


<script type="text/javascript">
////	Redimensionne
resize_iframe_popup(600,500);

////    CONTROLE DE SAISIE DU FORMULAIRE
////
function controle_formulaire()
{
/* 	// Vérif des modules cochés
	var nb_modules = 0;
	tab_modules = document.getElementsByName("liste_modules[]");
	for(i=0; i<tab_modules.length; i++)		{ if(tab_modules[i].checked==true)  nb_modules++; }
	if(nb_modules==0)			{ alert("<?php echo $trad["ESPACES_selectionner_module"]; ?>"); return false; }
	if(get_value("nom")=="")	{ alert("<?php echo $trad["specifier_nom"]; ?>");  return false; } */
}


////	CREATION NOUVEAU MILLESIME
////
function changement_millesime(id_elem)
{
	// Sélectionne / désélectionne
	checkbox_text(id_elem,'lien_select2');
	// Affiche le password?
	afficher('input_option_millesime',is_checked('box_millesime_suivant'));
	if(is_checked('txt_option_select'))		element('password').focus();
}

////	SUPPRESSION NOUVEAU MILLESIME
////
function suppression_millesime(id_elem)
{
	// Sélectionne / désélectionne
	checkbox_text(id_elem,'lien_select2');
	// Affiche le password?
	afficher('input_option_millesime_suppr',is_checked('box_millesime_suivant_suppr'));
	if(is_checked('txt_option_select_suppr'))		element('password').focus();
}

////	SELECTION DE "DEBUT ACTIVITE"
function changement_date_depot()
{
	if(document.getElementById("span_date_depot").style.display == "none") 	
		{ 	document.getElementById("span_date_depot").style.display = "inline";
			document.getElementById("confirm_changement_date_hidden").value = "1";}	
	else
		{ 	document.getElementById("span_date_depot").style.display = "none";
			document.getElementById("confirm_changement_date_hidden").value = "0";}
}

</script>


<form action="<?php echo php_self(); ?>" method="post" OnSubmit="return controle_formulaire();" style="padding:10px;font-weight:bold;font-size:11px;">

	<?php
	////	PARAMETRAGE GENERAL
	////
	echo "<fieldset>";
		////	NOM & DESCRIPTION
		echo "<div>";
			echo "<span style='text-align:center'><h2 class='suivi'>".majuscule($trad["ESPACES_gestion_module_suivi"])."</h1></span> ";
		echo "</div>";
	echo "</fieldset>";


	if($_SESSION["user"]["admin_general"]==1)
	{
	////	CREATION NOUVEAU MILLESIME (ADMIN GENERAL)
	////
		echo "<fieldset style='margin-top:40px'>";
			echo "<legend>".$trad["ESPACES_gestion_module_suivi"]."</legend>";
			echo "<div class='div_liste_users'><table class='pas_selection' style='width:100%;'>";
				////	ENTETE
				echo "<tr>";
					echo "<td colspan='2' class='txt_acces_admin' name='tous_txt' style='width:100%;'><div syle='width:100px;text-align:center;'><i>".majuscule($trad["ESPACES_gestion_millesimes"])."</i></div></td>";
				echo "</tr>";
				
				////	TOUS LES MILLESIMES
				
				$millesimes = millesimes_suivi(); $annee_a_creer = $millesimes[1]+1; $derniere_annee = $millesimes[1];
				echo "<tr class='ligne_survol'>";
					echo "<td class='txt_acces_user' name='tous_txt' style='width:60%;padding-left:20px;' >".$trad["ESPACES_premier_millesime"]."</td>";
					echo "<td>".$millesimes[0]."</td>";
				echo "</tr>";
				echo "<tr class='ligne_survol'>";
					echo "<td class='txt_acces_user' name='tous_txt' style='width:60%;padding-left:20px;' >".$trad["ESPACES_dernier_millesime"]."</td>";
					echo "<td>".$millesimes[1]."</td>";
				echo "</tr>";
				
			//	CREATION MILLESIME
				echo "<tr class='ligne_survol'>";
					echo "<td colspan='2'>";
					echo "<div class='div_config_suivi '>".$trad["ESPACES_question_creer_millesime"]."<span style='color:#660033'>".$annee_a_creer."</span>";
					if($millesimes[1] == $_SESSION['millesime']){
					////	CONFIRMATION
		echo "<div style='margin-top:20px;' >";
			echo "<input type='checkbox' name='millesime_suivant' value='1' id='box_millesime_suivant' onClick=\"changement_millesime(this.id);\" /> ";
			echo "<span id='txt_option_select' onClick=\"changement_millesime(this.id);\" ".infobulle($trad["ESPACES_changement_millesime_infos"])." >".$trad["ESPACES_proceder_creation_millesime"]."</span>";
			echo "<span id='input_option_millesime' class='lien_select' style='margin-left:10px; display:none;'><img src=\"".PATH_TPL."divers/fleche_droite.png\" />&nbsp;<i>".$trad["ESPACES_confirmer_changement_millesime"]."</i> 	<select name='confirm_changement'><option value='Non'selected='selected'> Non </option><option value='Oui' > Oui </option></select> <input type='hidden' name='a_creer' value='". $annee_a_creer ."'  /></span>";
		echo "</div>";
					
					}
					else{
						echo "<div>Le millesime courant doit être ".$derniere_annee."</div>";
					}
					echo "</div>";
				echo "</td></tr>";
				
			//	SUPPRESION MILLESIME
				echo "<tr class='ligne_survol'>";
					echo "<td colspan='2'>";
					echo "<div class='div_config_suivi '>".$trad["ESPACES_question_supprimer_millesime"]."<span style='color:#660033'>".$millesimes[0]."</span>";
					if($millesimes[0] != $_SESSION['millesime']){
					////	CONFIRMATION
		echo "<div style='margin-top:20px;' >";
			echo "<input type='checkbox' name='millesime_supprime' value='1' id='box_millesime_suivant_suppr' onClick=\"suppression_millesime(this.id);\" /> ";
			echo "<span id='txt_option_select_suppr' onClick=\"suppression_millesime(this.id);\" ".infobulle($trad["ESPACES_suppression_millesime_infos"])." >".$trad["ESPACES_proceder_suppression_millesime"]."</span>";
			echo "<span id='input_option_millesime_suppr' class='lien_select' style='margin-left:10px; display:none;'><img src=\"".PATH_TPL."divers/fleche_droite.png\" />&nbsp;<i>".$trad["ESPACES_confirmer_changement_millesime"]."</i> 	<select name='confirm_suppression'><option value='Non'selected='selected'> Non </option><option value='Oui' > Oui </option></select> <input type='hidden' name='a_supprimer' value='". $millesimes[0] ."'  /></span>";
		echo "</div>";
					
					}
					else{
						echo "<div>Le millesime courant ne doit pas être ".$millesimes[0]."</div>";
					}
					echo "</div>";
				echo "</td></tr>";
				
			echo "</table></div>";
		echo "</fieldset>";
		
	////	DATE LIMITE DE DÉPOT (ADMIN GENERAL)
	////
		echo "<fieldset style='margin-top:40px'>";
			echo "<legend>".$trad["ESPACES_gestion_module_suivi"]."</legend>";
			echo "<div class='div_liste_users'><table class='pas_selection' style='width:100%;'>";
				////	ENTETE
				echo "<tr>";
					echo "<td colspan='2' class='txt_acces_admin' name='tous_txt' style='width:100%;'><div syle='width:100px;text-align:center;'><i>DATE LIMITE DE DÉPOT</i></div></td>";
				echo "</tr>";
				////
				$date_limite 	= date_limite();
				$input_update 	= ""; 	// POUR : UPDATE OU INSERT de LA DATE
				if($date_limite == '' || $date_limite == NULL) {
					$date_limite 	= "30-04-".$_SESSION['millesime']."";
					$input_update 	= "<input type='hidden' value='1' name='update_date'>";
				}
				echo "<tr class='ligne_survol'>";
					echo "<td class='txt_acces_user' name='tous_txt' style='width:60%;padding-left:20px;' >Date limite de dépot pour ".$_SESSION['millesime']." : </td>";
					echo "<td>" .$date_limite."<span id='txt_option_modif_date' class='lien_select'  onClick='changement_date_depot();' ".infobulle($trad["modifier_fin_activite"])." style='padding-left:10px;'> Modifier ?</span></td></tr>";
				echo "<tr><td style='width:60%;padding-left:20px;'></td>";
				echo "<td><span style='margin-left: 10px; display: none;' class='txt_acces_user' id='span_date_depot'>Date limite : ";
					echo "<input type='text' value='' id='date_depot' name='date_depot' style='width:55px'>";
					echo "<input type='hidden' value='0' id='confirm_changement_date_hidden' name='confirm_changement_date_hidden'>".$input_update;
				echo "</span>";
				echo "</td></tr>";
			echo "</table></div>";
		echo "</fieldset>";
	}
	
	?>


	<div style="margin-top:30px;text-align:right;">
		<input type="hidden" name="id_espace" value="<?php echo $espace_tmp["id_espace"]; ?>" />
		<input type="hidden" name="page_origine" value="<?php echo $_SERVER["HTTP_REFERER"]; ?>" />
		<input type="submit" value="<?php echo $trad["valider"]; ?>" class="button_big" />
	</div>

</form>


<?php require PATH_INC."footer.inc.php"; ?>