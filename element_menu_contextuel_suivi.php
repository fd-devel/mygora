<?php
////	INIT
////
if(!isset($cfg_menu_elem["objet_infos"]))	$cfg_menu_elem["objet_infos"] = objet_infos($cfg_menu_elem["objet"], $cfg_menu_elem["id_objet"]);
else										$cfg_menu_elem["id_objet"] = $cfg_menu_elem["objet_infos"][$cfg_menu_elem["objet"]["cle_id_objet"]];
$id_menu_contextuel = "menu_context_".rand(1,99)."_".$cfg_menu_elem["objet"]["type_objet"]."_".$cfg_menu_elem["id_objet"];// "rand()" pour afficher 2 menus contextuels du même elem (ex: menu d'agendas)
$objet_independant = objet_independant($cfg_menu_elem["objet"],$cfg_menu_elem["objet_infos"]);
$dossier_racine = is_dossier_racine($cfg_menu_elem["objet"],$cfg_menu_elem["id_objet"]);
$txt_lecture = $txt_ecriture_limit = $txt_ecriture = "";
$icone_element_individuel = true;

////	POSITIONNEMENT DU CONTENEUR
////
if(@$cfg_menu_elem["taille_icone"]=="big")				$div_elem_style = "float:left;margin:-4px;margin-right:15px;";//menu de l'agenda, etc.
elseif(@$cfg_menu_elem["taille_icone"]=="small_inline")	$div_elem_style = "float:left;display:inline;margin-left:5px;";
else													$div_elem_style = "float:left;margin:0px;";



////	AFFICHE LE MENU
////
echo "<div class='noprint' style='".$div_elem_style."'>";

	////	AFFICHAGE DU MENU CONTEXTUEL DE L'ELEMENT
	////
	echo "<div class='menu_context' id='".$id_menu_contextuel."'>"; 

//		echo'<pre>'.print_r($cfg_menu_elem).'</pre>';
		
		////	NOM
			echo "<div class='menu_context_ligne '><div class='menu_context_img'><img src=\"".PATH_TPL."module_utilisateurs/acces_utilisateur.png\" /></div><div class='menu_context_txt'>".$cfg_menu_elem["objet_infos"]["civilite"]." ".$cfg_menu_elem["objet_infos"]["nom"]." ".$cfg_menu_elem["objet_infos"]["prenom"]."</div></div><hr class='menu_context_hr' />";
	
		////	INFO EDI TVA
		if($_SESSION['page_suivi'] == 'TVA'){
			echo "<div class='menu_context_ligne lien' onClick=\"popup('mail_info_envoi_edi.php?declaration=tva&id_contact=".$cfg_menu_elem["objet_infos"]["id_contact"]."','Envoi EDI TVA', 600, 300);\"><div class='menu_context_img'><img src=\"".PATH_TPL."divers/envoi_mail.png\" /></div><div class='menu_context_txt'>Informer EDI TVA</div></div><hr class='menu_context_hr' />";
			
			if($cfg_menu_elem["objet_infos"]["tva_".$_SESSION['millesime']] == 'ANNUELLE'){
				
			echo "<div class='menu_context_ligne lien' onClick=\"popup('mail_ac_tva.php?acpt=".$cfg_menu_elem["objet_infos"]["tva_a_montant"]."&id_contact=".$cfg_menu_elem["objet_infos"]["id_contact"]."','Envoi EDI TVA', 600, 500);\"><div class='menu_context_img'><img src=\"".PATH_TPL."divers/envoi_mail.png\" /></div><div class='menu_context_txt'>Informer Acompte TVA</div></div><hr class='menu_context_hr' />";
			}
		}
			
		////	INFO ACPTE IS
		if($_SESSION['page_suivi'] == 'Saisie'){
			if($cfg_menu_elem["objet_infos"]["alerte_is"] == "oui")		echo "<div class='menu_context_ligne lien' onClick=\"popup('mail_info_envoi_edi.php?declaration=is&id_contact=".$cfg_menu_elem["objet_infos"]["id_contact"]."','Envoi EDI TVA', 600, 300);\"><div class='menu_context_img'><img src=\"".PATH_TPL."divers/envoi_mail.png\" /></div><div class='menu_context_txt'>Informer Acompte IS </div></div><hr class='menu_context_hr' />";
		}
		
	
		////	MODIFIER / CONSULTER
		
		if(isset($cfg_menu_elem["modif"]))		echo "<div class='menu_context_ligne lien' onclick=\"popup('../module_contact/contact.php?id_contact=" . $cfg_menu_elem["objet_infos"]["id_contact"] . "','aff_contact" . $contact_tmp["id_contact"] . "');\"><div class='menu_context_img'><img src=\"".PATH_TPL."divers/crayon.png\" /></div><div class='menu_context_txt'>".($objet_independant==true?$trad["modifier_et_acces"]:$trad["modifier"])."</div></div>";


		// séparation <hr> ?
		if(isset($cfg_menu_elem["modif"]) )		echo "<hr class='menu_context_hr' />";

		////	AUTEUR/DATE CREATION  &  AUTEUR/DATE MODIF  &  HISTORIQUE DES LOGS
		if($dossier_racine==false)
		{
			// Historique des logs de mofif & accès : auteur / admin espace
			if(is_auteur($cfg_menu_elem["objet_infos"]["id_utilisateur"]) || $_SESSION["espace"]["droit_acces"]==2)
				echo "<div class='menu_context_ligne lien' onClick=\"popup('".ROOT_PATH."module_logs/logs_element.php?module_path=".MODULE_PATH."&type_objet=".$cfg_menu_elem["objet"]["type_objet"]."&id_objet=".$cfg_menu_elem["id_objet"]."');\"><div class='menu_context_img'><img src=\"".PATH_TPL."divers/logs.png\" /></div><div class='menu_context_txt'>".$trad["historique_element"]."</div></div>";
		}


	echo "</div>";


	////	ICONE "PLUS" & AFFICHAGE DU MENU CONTEXTUEL  (position absolute pour que l'icone "plus" soit au dessus du contenu du bloc -> exemple des images du gestionnaire de fichier qui occupent tout le bloc)
	////
	if(empty($cfg_menu_elem["taille_icone"]))		$icone_height_tmp = "height:24px;";
	elseif($cfg_menu_elem["taille_icone"]=="small")	$icone_height_tmp = "height:17px;";
	elseif($cfg_menu_elem["taille_icone"]=="big")	$icone_height_tmp = "height:30px;";
	else											$icone_height_tmp = "";
	$icone_pos_absolute	= (@$cfg_menu_elem["icone_plus_position_absolute"]==true)  ?  "position:absolute;"  :  "";
	$icone_src_tmp		= ($_SESSION["user"]["id_utilisateur"] > 0 && @strtotime($cfg_menu_elem["objet_infos"]["date_crea"]) > @$_SESSION["user"]["precedente_connexion"])  ?  "options_new"  :  "options";
	if(@$cfg_menu_elem["taille_icone"]=="small_inline")		$icone_src_tmp .= "_inline";
	echo "<img src=\"".PATH_TPL."divers/".$icone_src_tmp.".png\" class='noprint' style='".$icone_height_tmp.$icone_pos_absolute."' id='icone_".$id_menu_contextuel."' />";
	echo "<script type='text/javascript'>  menu_contextuel('".$id_menu_contextuel."','".@$cfg_menu_elem["id_div_element"]."');  </script>";


echo "</div>";
?>