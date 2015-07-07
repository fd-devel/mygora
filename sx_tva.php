<?php

//// ENREGISTREMENT DES POST
////
if(isset($_POST['ajout']))	{
	$table_tva = "gt_sx_tva_".$_SESSION['millesime'];	
	foreach($_POST as $key => $val)		{
		if($key != 'ajout' && $key != 'page_suivi' ) { //alert($key);
			list($ligne, $casetva) = explode("-", $key);
			if( !preg_match ("/date/", $casetva)) {
				db_query("UPDATE ".$table_tva." SET ".$casetva." = ".db_format($val)." WHERE id_contact = '".$ligne."'") ;
			}
			elseif(preg_match ("/date/", $casetva) || $val == '')
			{
				$val = db_format_date($val);
				db_query("UPDATE ".$table_tva." SET ".$casetva." = '".$val."' WHERE id_contact = '".$ligne."'") ;
			}
		}
	}
}

//// ORDRE D'AFFICHAGE DES DOSSIERS
////
if(isset($_GET['tri']))	{$tri = $_GET['tri'];}
else					{$tri = 'clo1';}
switch ($tri){
	case 'num1':	$ordre = "numero"; 		break;
	case 'num2':	$ordre = "numero DESC";	break;
	case 'nom1':	$ordre = "nom";			break;
	case 'nom2':	$ordre = "nom DESC";	break;
	case 'clo1':	$ordre = "moiscloture_".$_SESSION['millesime'].", nom";	break;
	case 'clo2':	$ordre = "moiscloture_".$_SESSION['millesime']." DESC, nom DESC";	break;
	default:		$ordre = "moiscloture_".$_SESSION['millesime'].", nom";	break;
}


////	LISTE DES CONTACTS : Interrogation globale, s'il y a des dossiers en TVA
////
$liste_contacts = db_tableau("SELECT * FROM gt_contact T1, gt_sx_tva_".$_SESSION['millesime']." T2
								WHERE T1.id_contact = T2.id_contact 
								AND T1.tva_cabinet = 'Oui'
								AND T1.tva_".$_SESSION['millesime']." <> 'NON ASSUJETTI'
								AND T1.id_dossier='".intval($_GET["id_dossier"])."'
								AND T1.debut_activite <= '".$_SESSION['millesime']."' 
								AND (T1.fin_activite = '0' OR T1.fin_activite >= '".$_SESSION['millesime']."')
								".sql_affichage($objet["contact"],$_GET["id_dossier"],"T1.")." 
								ORDER BY ".$ordre);


if(count($liste_contacts)>0){

//// STYLE DIV TITRE + DIV NUMEROTATION COLONNES MOIS MENSUELLE
	$style_div_mensuelle = "width:85%;max-width:85%;margin:0 0 0 7%;border-bottom:#555 solid 1px;border-radius:2px;";
//// STYLE DIV TITRE + DIV NUMEROTATION COLONNES MOIS TRIMESTRIELLE
	$style_div_trimestrielle = "width:80%;max-width:80%;margin:0 0 0 10%;border-bottom:#555 solid 1px;border-radius:2px;";
//// STYLE DIV TITRE + DIV NUMEROTATION COLONNES MOIS ANNUELLE
	$style_div_annuelle = "width:70%;max-width:70%;margin:0 0 0 15%;border-bottom:#555 solid 1px;border-radius:2px;";
//// STYLE DIV SEPARATEUR
	$style_sep = "width:90%;";

	
?>
<script type="text/javascript">
////	AFFICHAGE INPUT ACOMPTE
////
function affiche_acompte(id_elem)
{
	var id = id_elem.split('-');
	var elemid = id[0] + "-tva_a_montant";
//	console.log( elemid );
//	console.info(elemid);
	if(document.getElementById(elemid).style.display == "none") 	
		{ 	document.getElementById(elemid).style.display = "inline";}
	else
		{ 	document.getElementById(elemid).style.display = "none";
			document.getElementById(elemid).value = "";}
}
</script>

	<!-- INFO GAUCHE -->
	<div class="info1 div_elem_infos">
		<p>Date : <img src='../templates/divers/info_small.png' <?php echo infobulle($trad["comment_saisir_une_date"]); ?>/></p>
		<p><input type="text" class="input_suivi_vert input_date_2" maxlength="8" value="xx/xx/xx" /></p>
	</div>
 

	<!-- INFO DROITE -->
	<div class="info2 div_elem_infos" title="Cliquer pour afficher le suivi tva chez Artic" style="cursor:pointer">
		<a onclick="javascript:window.open('http://artdotnet/SuiviEdition11/index.aspx?tech=AG11<?php echo $_SESSION['user']['identifiant']; ?>','testeee','directories = no, location = no, menubar = no, resizable = yes, scrollbars = yes, status = no, toolbar = no, width = 800, height = 600')" >
			<table class="table_info" cellspacing="0" cellpadding="0">
				<tr><td colspan="2">Voir le suivi :</td></tr>
				<tr><td colspan="2">ARTIC</td></tr>
			</table>
		</a>
	</div>	


	<!-- DEBUT FORM -->
	<form id="consult" action="#" method="post" style="padding-left:8px; padding-top:10px;" >

	<h1 style="text-align:center;" class="suivi">T.V.A.</h1>


	<!-- BOUTON D'ENREGISTREMENT -->
	<div style="text-align:center; margin-bottom:10px">
		<input type="submit" value="enregistrer" class='submit'/>
		<input type="hidden" name="ajout"/>
		<input type="hidden" name="page_suivi" value="TVA"/>
	</div> 

<?php
	//// LISTE DES CONTACTS EN TVA MENSUELLE
	////
	$liste_TVA_mensuelle = db_tableau("SELECT * FROM gt_contact T1, gt_sx_tva_".$_SESSION['millesime']." T2
								WHERE T1.id_contact = T2.id_contact 
								AND T1.tva_cabinet = 'Oui'
								AND T1.tva_".$_SESSION['millesime']." = 'MENSUELLE'
								AND T1.debut_activite <= '".$_SESSION['millesime']."' 
								AND (T1.fin_activite = '0' OR T1.fin_activite >= '".$_SESSION['millesime']."')
								AND T1.id_dossier='".intval($_GET["id_dossier"])."'
								".sql_affichage($objet["contact"],$_GET["id_dossier"],"T1.")." 
								ORDER BY ".$ordre);

	// SI TVA MENSUELLE ON AFFICHE
	if(count($liste_TVA_mensuelle)>0){
	//// INITIALISATION CARDINALITE DOSSIER (colone de gauche)
	////
	$cardinalite = 1;
?>

	<div id="div_elem_0" class="div_elem_suivi_no_hover" style="<?php echo $style_div_mensuelle; ?> height:60px;">
		<div class="div_elem_contenu">
		<table class="div_elem_table">
			<!-- PREMIERE LIGNE DU TABLEAU -->
		<tr style="text-align:center;"  >
			<td colspan="16" ><h2 style="margin: 8px 0px -10px;">TVA Mensuelles :</h2></td></tr>

		<tr style="text-align:center;">
<?php
		$mois	=  array("janv","fev","mar","avr","mai","juin","juil","aou","sept","oct","nov","dec");
		echo "<td  class='div_elem_td td_suivi_tva_col1' ></td>";
		echo "<td  class='div_elem_td td_suivi_tva_col2' >Num</td>";
		echo "<td  class='div_elem_td td_suivi_tva_col3' >Nom</td>";
		echo "<td  class='div_elem_td '></td>";
		foreach($mois as $moi){	echo "<td class='div_elem_td td_suivi_tva_col4_m_titre' >".$moi."</td>";}
		echo "</tr></table></div></div>";


		// LISTE
		foreach($liste_TVA_mensuelle as $contact_tmp)
		{
			//echo'<pre>'.print_r($contact_tmp).'</pre>';
            ////	LIEN POPUP DEMAT ARTIC 
			if($contact_tmp["anciennumero"] <> ""){
            $lien_popup_demat = "class='div_elem_td td_suivi_tva_col2 txt_acces_user' onclick=\"popup('demat.php?dossier=" . $contact_tmp["anciennumero"] . "','aff_contact" . $contact_tmp["id_contact"] . "');\"";
			}else{$lien_popup_demat = "class='div_elem_td td_suivi_tva_col2'";}	
			
			// Numero
			$contact_numero = $contact_tmp["numero"];		
			
            ////	LIEN POPUP DOSSIER
            $lien_popup = "";
			
			// civilite, Nom
			$contact_pave_ident_info = $contact_tmp["civilite"]." ".$contact_tmp["nom"];
			
			////	DETAILS D'UN CONTACT
			$details_contact = "";
			
			$cases = "";
		//	$label_mois	=  array("tva_1m","tva_2m","tva_3m","tva_4m","tva_5m","tva_6m","tva_7m","tva_8m","tva_9m","tva_10m","tva_11m","tva_12m");
			$label_mois_date	=  array("tva_1m_date","tva_2m_date","tva_3m_date","tva_4m_date","tva_5m_date","tva_6m_date","tva_7m_date","tva_8m_date","tva_9m_date","tva_10m_date","tva_11m_date","tva_12m_date");
			
			foreach($label_mois_date as $key ){
				$name	= $contact_tmp["id_contact"];
				$val	= $contact_tmp[$key];
				$classCase = coulCaseDateTva($val);
		 	
				$cases .= "<td class='td_suivi_tva_col4_m'>";
				$cases .= "<input class='".$classCase." input_date_1' name='".$name."-".$key."' maxlength='8' value='".$val."'/></td>";
			}
		
			
			$details_contact .= $cases;


			// MENU CONTEXTUEL
			menu_contextuel_suivi($objet["contact"], $contact_tmp, 2);

			////	CONTENU
			echo "<div class='div_elem_contenu' >";
				echo "<table class='div_elem_table'><tr style='text-align:center;'>";
					echo "<td class='div_elem_td td_suivi_tva_col1 cardinalite' >".$cardinalite."</td>";
					echo "<td ".$lien_popup_demat." >".$contact_numero."</td>";
					echo "<td class='div_elem_td td_suivi_tva_col3' ".$lien_popup.">".$contact_pave_ident_info."</td>";
		echo "<td  class='div_elem_td '></td>";
					echo $details_contact;
				echo "</tr></table>";
			echo "</div>";
		echo "</div>";
		$cardinalite++;
		}
	//// SEPARATEUR HORIZONTAL
	echo "<div id='div_elem_0' class='div_elem_suivi_vide' style='". $style_div_mensuelle ."height:30px;'><br/><hr><br/></div>";
	}
//// FIN TVA MENSUELLE
	
	
	//// LISTE DES CONTACTS EN TVA TRIMESTRIELLE
	////
	$liste_TVA_trimestrielle = db_tableau("SELECT * FROM gt_contact T1, gt_sx_tva_".$_SESSION['millesime']." T2
								WHERE T1.id_contact = T2.id_contact 
								AND T1.tva_cabinet = 'Oui'
								AND T1.tva_".$_SESSION['millesime']." = 'TRIMESTRIELLE'
								AND T1.debut_activite <= '".$_SESSION['millesime']."' 
								AND (T1.fin_activite = '0' OR T1.fin_activite >= '".$_SESSION['millesime']."')
								AND T1.id_dossier='".intval($_GET["id_dossier"])."'
								".sql_affichage($objet["contact"],$_GET["id_dossier"],"T1.")." 
								ORDER BY ".$ordre);

	// SI TVA TRIMESTRIELLE ON AFFICHE
	if(count($liste_TVA_trimestrielle)>0){
	//// INITIALISATION CARDINALITE DOSSIER (colone de gauche)
	////
	$cardinalite = 1;
?>

	<div id="div_elem_0" class="div_elem_suivi_no_hover" style="<?php echo $style_div_trimestrielle; ?> height:60px;">
		<div class="div_elem_contenu">
		<table class="div_elem_table">
			<!-- PREMIERE LIGNE DU TABLEAU -->
		<tr style="text-align:center;"  >
			<td colspan="7" ><h2 style="margin: 8px 0px -10px;">TVA Trimestrielle :</h2></td>

		<tr style="text-align:center;">
<?php
		$trim	=  array("1er Trim","2em Trim","3em Trim","4em Trim");
		echo "<td  class='div_elem_td td_suivi_tva_col1' ></td>";
		echo "<td  class='div_elem_td td_suivi_tva_col2' >Num</td>";
		echo "<td  class='div_elem_td td_suivi_tva_col3' >Nom</td>";
		foreach($trim as $moi){	echo "<td  class='div_elem_td td_suivi_tva_col4_t' >".$moi."</td>";}
		echo "</tr>";

	/* 	echo "<tr style='font-size:0.8em;text-align:center;'>";
		for($i=1; $i<=4; $i++){ echo "<td class='td_suivi_tva_col4_1' >env</td><td class='td_suivi_tva_col4_2' >Date</td>";}
	 */	
		echo "</tr></table></div></div>";


		// LISTE
		foreach($liste_TVA_trimestrielle as $contact_tmp)
		{
			//echo'<pre>'.print_r($contact_tmp).'</pre>';
            ////	LIEN POPUP DEMAT ARTIC 
			if($contact_tmp["anciennumero"] <> ""){
            $lien_popup_demat = "class='div_elem_td td_suivi_tva_col2 txt_acces_user' onclick=\"popup('demat.php?dossier=" . $contact_tmp["anciennumero"] . "','aff_contact" . $contact_tmp["id_contact"] . "');\"";
			}else{$lien_popup_demat = "class='div_elem_td td_suivi_tva_col2'";}	
			
			// Numero
			$contact_numero = $contact_tmp["numero"];		
			
            ////	LIEN POPUP DOSSIER
            $lien_popup = "";
			
			// civilite, Nom
			$contact_pave_ident_info = $contact_tmp["civilite"]." ".$contact_tmp["nom"];
			
			////	DETAILS D'UN CONTACT
			$details_contact = "";
			
			$cases = "";
			$label_trim	=  array("tva_1t","tva_2t","tva_3t","tva_4t");
			$label_trim_date	=  array("tva_1t_date","tva_2t_date","tva_3t_date","tva_4t_date");
			
			foreach($label_trim_date as $key ){
		
		/* 		$cases .= "<td class='td_suivi_tva_col4_1' >"; */
					$name	= $contact_tmp["id_contact"];
					$val	= $contact_tmp[$key];

				$cases .= "<td class='div_elem_td td_suivi_tva_col4_t'>";
					$classCase = coulCaseDateTva($val);
					$cases .= "<input class='".$classCase." input_date_2' name='".$name."-".$key."' maxlength='8' value='".$val."'/></td>";
				}

			$details_contact .= $cases;

			// MENU CONTEXTUEL
			menu_contextuel_suivi($objet["contact"], $contact_tmp, 3);
			
			
			/* ////	MODIF / SUPPR / INFOS / CREATION USER (admin g�n�ral)
			$cfg_menu_elem = array("objet"=>$objet["contact"], "objet_infos"=>$contact_tmp);
			$contact_tmp["droit_acces"] = ($_GET["id_dossier"]>1)  ?  $droit_acces_dossier  :  droit_acces($objet["contact"],$contact_tmp);
			if($contact_tmp["droit_acces"]>=2)	{			}
 			
			if($_SESSION["user"]["admin_general"]==1)	$cfg_menu_elem["options_divers"][] = array("icone_src"=>PATH_TPL."divers/ajouter.png", "text"=>$trad["CONTACT_creer_user"], "action_js"=>"confirmer('".addslashes($trad["CONTACT_creer_user_infos"])."','index.php?action=creer_user&id_contact=".$contact_tmp["id_contact"]."');"); 
 
			////	DIV SELECTIONNABLE + OPTIONS
			$cfg_menu_elem["id_div_element"] = div_element_suivi($objet["contact"], $contact_tmp["id_contact"],3);
			$cfg_menu_elem["taille_icone"]="small_inline";
			$cfg_menu_elem["modif"] = true;
			$cfg_menu_elem["suppr"] = true;
			require "element_menu_contextuel_suivi.php"; */
			
			////	CONTENU
			echo "<div class='div_elem_contenu'>";
				echo "<table class='div_elem_table'><tr>";
				////	AFFICHAGE BLOCK
					
				////	AFFICHAGE LISTE
					echo "<td class='div_elem_td td_suivi_tva_col1 cardinalite' >".$cardinalite."</td>";
					echo "<td ".$lien_popup_demat." >".$contact_numero."</td>";
					echo "<td class='div_elem_td td_suivi_tva_col3' ".$lien_popup.">".$contact_pave_ident_info."</td>";
					echo $details_contact;
					
				echo "</tr></table>";
			echo "</div>";
		echo "</div>";
		$cardinalite++;
		}
	//// SEPARATEUR HORIZONTAL
	echo "<div id='div_elem_0' class='div_elem_suivi_vide' style='". $style_div_trimestrielle ."height:30px; '><br/><hr><br/></div>";
	} 
//// FIN TVA TRIMESTRIELLE
	
	
//// LISTE DES CONTACTS EN TVA ANNUELLE
////
	$liste_TVA_annuelle = db_tableau("SELECT * FROM gt_contact T1, gt_sx_tva_".$_SESSION['millesime']." T2
								WHERE T1.id_contact = T2.id_contact 
								AND T1.tva_cabinet = 'Oui'
								AND T1.tva_".$_SESSION['millesime']." = 'ANNUELLE'
								AND T1.debut_activite <= '".$_SESSION['millesime']."' 
								AND (T1.fin_activite = '0' OR T1.fin_activite >= '".$_SESSION['millesime']."')
								AND T1.id_dossier='".intval($_GET["id_dossier"])."'
								".sql_affichage($objet["contact"],$_GET["id_dossier"],"T1.")." 
								ORDER BY ".$ordre);

	// SI TVA ANNUELLE ON AFFICHE
	if(count($liste_TVA_annuelle)>0){
	//// INITIALISATION CARDINALITE DOSSIER (colone de gauche)
	////
	$cardinalite = 1;
?>

	<div id="div_elem_0" class="div_elem_suivi_no_hover" style="<?php echo $style_div_annuelle; ?> height:60px;">
		<div class="div_elem_contenu">
		<table class="div_elem_table">
			<!-- PREMIERE LIGNE DU TABLEAU -->
		<tr style="text-align:center;"  >
			<td colspan="6" ><h2 style="margin: 8px 0px -10px;">TVA Annuelle</h2></td>

		<tr style="text-align:center;">
<?php
		
		echo "<td class='div_elem_td td_suivi_tva_col1' ></td>";
		echo "<td class='div_elem_td td_suivi_tva_col2_a' >Num</td>";
		echo "<td class='div_elem_td td_suivi_tva_col3_a' >Nom</td>";
		echo "<td class='div_elem_td td_suivi_tva_col4_a' >Date</td>";
		echo "<td class='div_elem_td td_suivi_tva_col5_acpt'>Acompte</td>";
		echo "<td class='div_elem_td td_suivi_tva_col5_img'><img src='../templates/divers/info_small.png' ".infobulle($trad["acompte_trim_tva_annuelle"])."/></td>";
		echo "</tr></table></div></div>";


		// LISTE
		foreach($liste_TVA_annuelle as $contact_tmp)
		{
			//echo'<pre>'.print_r($contact_tmp).'</pre>';
            ////	LIEN POPUP DEMAT ARTIC 
			if($contact_tmp["anciennumero"] <> ""){
            $lien_popup_demat = "class='div_elem_td td_suivi_tva_col2_a txt_acces_user' onclick=\"popup('demat.php?dossier=" . $contact_tmp["anciennumero"] . "','aff_contact" . $contact_tmp["id_contact"] . "');\"";
			}else{$lien_popup_demat = "class='div_elem_td td_suivi_tva_col2_a'";}	
			
			// Numero
			$contact_numero = $contact_tmp["numero"];		
			
            ////	LIEN POPUP DOSSIER
            $lien_popup = "";
			
			////	CIVILITE, NOM
			$contact_pave_ident_info = $contact_tmp["civilite"]." ".$contact_tmp["nom"];
			
			////	DETAILS DU CONTACT
			$name	= $contact_tmp["id_contact"];
			$val_date	= $contact_tmp["tva_a_date"];
			$classCase = coulCaseDateTva($val_date);
				
			$cases = "<td class='td_suivi_tva_col4_a'>";
			$cases .= "<input class='".$classCase." input_date_2' name='".$name."-tva_a_date' maxlength='8' value='".$val_date."'/></td>";
						
			////	ACOMPTE TRIMESTRIEL
			$input_acompte = ($contact_tmp['tva_a_montant'] <>"") ? "<input class='input_suivi input_date_2' value='".$contact_tmp['tva_a_montant']."' name='".$name."-tva_a_montant' id='".$name."-tva_a_montant' maxlength='10' style='display:inline;' />" : "<input class='input_suivi input_date_2' value='".$contact_tmp['tva_a_montant']."' name='".$name."-tva_a_montant' id='".$name."-tva_a_montant' maxlength='10' style='display:none;' />" ;
			
			$img_acompte = "<img id='".$name."-img' src='../templates/divers/ajouter_petit.png' onclick=\"affiche_acompte(this.id);\" />";
			


			// MENU CONTEXTUEL
			menu_contextuel_suivi($objet["contact"], $contact_tmp, 4);

			////	CONTENU
			echo "<div class='div_elem_contenu'>";
				echo "<table class='div_elem_table'><tr>";
					
				////	AFFICHAGE LISTE
					echo "<td class='div_elem_td td_suivi_tva_col1 cardinalite' >".$cardinalite."</td>";
					echo "<td ".$lien_popup_demat." >".$contact_numero."</td>";
					echo "<td class='div_elem_td td_suivi_tva_col3_a' ".$lien_popup.">".$contact_pave_ident_info."</td>";
					echo $cases;
					echo "<td class='div_elem_td td_suivi_tva_col5_acpt'>".$input_acompte."</td>";
					echo "<td class='div_elem_td td_suivi_tva_col5_img'>".$img_acompte."</td>";
					
				echo "</tr></table>";
			echo "</div>";
		echo "</div>";
		$cardinalite++;
		}

	} 
//// FIN TVA ANNUELLE	


?>
<p>&nbsp;</p>

  	<!-- BOUTON D'ENREGISTREMENT -->
	<div style="text-align:center; margin-bottom:10px">
		<input type="submit" value="enregistrer" class='submit'/>
		<input type="hidden" name="ajout"/>
		<input type="hidden" name="page_suivi" value="TVA"/>
	</div> 


</form>
<?php

}
	//// FIN TVA GLOBAL
////////////////////////////////////////////
	////	AUCUN CONTACT
	if(@$cpt_div_element<1)  echo "<div class='div_elem_aucun' style ='margin-top:50px;'>".$trad["CONTACT_aucun_contact"]."</div>";



?>
<br />