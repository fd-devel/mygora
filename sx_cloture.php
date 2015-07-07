<?php

//// ENREGISTREMENT DES POST
////
if(isset($_POST['ajout'])){ //$startTime = microtime(true); // test temps d'execution
		
	// dossier courant
	$doss = 0;
	$table_fisc = "gt_sx_cloture_".$_SESSION['millesime'];	
	foreach($_POST as $key => $val)	{
		if($key != 'ajout' && $key != 'page_suivi' )		{
			list($ligne, $champ_mois) = explode("-", $key);
				
			// si nouveau dossier -> dossier courant = nouveau dossier
				if($ligne <> $doss) {
					$doss = $ligne; 
					$dossiers[$doss] = ""; 
				}
				else{ $dossiers[$doss] .= ", ";	}	// separation pour requette sql
				
				$val = db_format_date($val);
			
				$dossiers[$doss] .=  $champ_mois . " = '" .$val. "' ";	// cloture = 'X' / '123456' / '01/01/13'
							
//			db_query("UPDATE ".$table_fisc." SET ".$champ." = '".$val."' WHERE id_contact = '".$ligne."'");
			
		}
	}
	    //    echo'<pre>'.print_r($dossiers).'</pre>';
	foreach($dossiers as $dossier => $corps_sql){
                db_query("UPDATE ".$table_fisc." SET ".$corps_sql." WHERE id_contact = '" . $dossier . "'");
	}
	//$endTime = microtime(true); // test d'execution 
	//$elapsed = $endTime - $startTime;  
	//alert("Temps d'exécution : ".$elapsed." secondes");
}

	
////	MENU CHEMIN + OBJETS_DOSSIERS
////
// echo menu_chemin($objet["contact_dossier"], $_GET["id_dossier"]);
$cfg_dossiers = array("objet"=>$objet["contact_dossier"], "id_objet"=>$_GET["id_dossier"]);
require_once PATH_INC."dossiers.inc.php";

//// ORDRE D'AFFICHAGE DES DOSSIERS
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

////	LISTE DES CONTACTS
////
$liste_contacts = db_tableau("SELECT * FROM gt_contact T1, gt_sx_cloture_".$_SESSION['millesime']." T2
	WHERE T1.id_contact = T2.id_contact 
	AND T1.id_dossier='".intval($_GET["id_dossier"])."'
	AND T1.debut_activite <= '".$_SESSION['millesime']."' 
	AND T1.regime_".$_SESSION['millesime']." <> 'TVA UNIQMT'
	AND (T1.fin_activite = '0' OR T1.fin_activite >= '".$_SESSION['millesime']."')
	".sql_affichage($objet["contact"],$_GET["id_dossier"],"T1.")." 
	 ORDER BY ".$ordre);	
	
if(count($liste_contacts)>0){		



	////	CHOIX D'AFFICHAGE : LE MILLESIME COURANT / TOUT
	$affich = "";
	if(isset($_COOKIE['cook-affsaisie']))	$affich = $_COOKIE['cook-affsaisie'];
	
	//// 	STYLE DIV TITRE + DIV NUMEROTATION COLONNES MOIS
	$style_div = "width:99%;margin:0 0 0 5%;border-bottom:#555 solid 1px;border-radius:2px;";

	
	//// 	INITIALISATION CARDINALITE DOSSIER (colone de gauche)
	////
	$cardinalite = 1;
	
	////	DATE LIMITE DE DEPOT
	////
	$date_limite = date_limite();
?>

	<!-- INFO GAUCHE -->
	<div class="info1 div_elem_infos">
		<p>Date : <img src='../templates/divers/info_small.png' <?php echo infobulle($trad["comment_saisir_une_date"]); ?>/></p>
		<p><input type="text" class="input_suivi_vert input_date_2" maxlength="8" value="xx/xx/xx" /></p>
	</div>

	<!-- INFO DROITE -->
	<div class="info2 div_elem_infos	">
		<p >Date limite
		<hr/>
		<?php echo $date_limite; ?>
		</p>
	</div>
	<h1 style="text-align:center;" class="suivi">Clôture</h1>

	<form id="consult" action="#" method="post" style="padding-left:8px; padding-top:10px;" >

	<!-- BOUTON D'ENREGISTREMENT -->
	<div style="text-align:center; margin-bottom:10px">
		<input type="submit" value="enregistrer" class='submit'/>
		<input name="ajout" type="hidden"/>
		<input type="hidden" name="page_suivi" value="Cloture"/>
	</div> 

<!--	<table id="contenu_principal_table"> -->
	<div id="div_elem_0" class="div_elem_suivi_no_hover" style="<?php echo $style_div; ?> height:40px;">
		<div class="div_elem_contenu">
		<table class="div_elem_table">
		<tr class="titre" >
			<td style="width:70px;text-align:center;" rowspan="2" ></td>
			<td rowspan="2" style="width:70px;text-align:center; vertical-align:middle;"><a href="index.php?tri=num1"><img src="<?php echo PATH_TPL; ?>module_suivi/flecheb.png" alt="Fl bas" class="fleche" /></a> Numéro <a href="#index.php?tri=num2"><img src="<?php echo PATH_TPL; ?>module_suivi/flecheh.png" alt="Fl haut" class="fleche" /></a></td>
			<td rowspan="2" colspan="2" style="text-align:center; vertical-align:middle;"><a href="index.php?tri=nom1"><img src="<?php echo PATH_TPL; ?>module_suivi/flecheb.png" alt="Fl bas" class="fleche" /></a> Nom <a href="index.php?tri=nom2"><img src="<?php echo PATH_TPL; ?>module_suivi/flecheh.png" alt="Fl haut" width="9" class="fleche" /></a></td>
			
			<?php 
			$colonne_cloture = array('FISCAL' => 'colspan=\'3\'', 'GESTION' => '', 'JURIDIQUE' => '', 'Rem. Resultat' => 'colspan=\'2\'');
			foreach($colonne_cloture as $colonne => $col_libelle){
			echo "<td class='div_elem_td' style='text-align:center;'".$col_libelle.">".$colonne."</td>";}
		echo "</tr>";
		echo "<tr>";
			$colonne_travaux = array('cloturé', 'visa', 'tdfc', 'dossier', 'AGOA', 'Edition', 'Date');
			foreach($colonne_travaux as $colonne ){
			echo "<td class='div_elem_td td_suivi_cloture' style='text-align:center;'>".$colonne."</td>";}
		echo "</tr>";
			?> 

		</table>
		</div>
	</div>
	
	<?php
	foreach($liste_contacts as $contact_tmp)
	{
		//echo'<pre>'.print_r($contact_tmp).'</pre>';
        ////	LIEN POPUP DEMAT ARTIC 
		if($contact_tmp["anciennumero"] <> ""){
            $lien_popup_demat = "class='div_elem_td txt_acces_user' onclick=\"popup('demat.php?dossier=" . $contact_tmp["anciennumero"] . "','aff_contact" . $contact_tmp["id_contact"] . "');\"";
		}else{$lien_popup_demat = "class='div_elem_td'";}			
			
        ////	LIEN POPUP DOSSIER
        $lien_popup = "onclick=\"popup('../module_contact/contact.php?id_contact=" . $contact_tmp["id_contact"] . "','aff_contact" . $contact_tmp["id_contact"] . "');\"";
			
		// Numero
		$contact_numero = $contact_tmp["numero"];
			
		// civilite, Nom
		$contact_pave_ident_info = $contact_tmp["civilite"]." ".$contact_tmp["nom"];
			
 		////	DETAILS D'UN CONTACT
		$details_contact = "";
		
		$cases = "";
		$label_cloture = array('cloture', 'visa', 'tdfc', 'dossier', 'agoa', 'edition', 'remise');
		
	foreach($label_cloture as $champ ){
		$cases .= "<td class='td_suivi_cloture' >";
			$name = $contact_tmp["id_contact"];
			$champ = $champ;
			$val_date = $contact_tmp[$champ];
			$classCase = coulCaseDate($val_date, $champ);
		// CLOTURE FISCALE
		if( $champ == 'cloture')
		{	if($contact_tmp["regime_".$_SESSION['millesime'].""]=="REEL"){
				$cases .= "<input class='".$classCase." input_date_2' name='".$name."-".$champ."' maxlength='8' value='".$val_date."'/>";
			}else{		$input = "";}	
		}
		// VISA
		elseif($champ == 'visa')
		{	if($contact_tmp["regime_".$_SESSION['millesime'].""]=="REEL"){				
				$cases .= "<input class='".$classCase." input_date_2' name='".$name."-".$champ."' maxlength='8' value='".$val_date."'/>";
			}else{		$input = "";}
		}
		// TDFC
		elseif($champ == 'tdfc')
		{	if($contact_tmp["regime_".$_SESSION['millesime'].""]=="REEL"){				
				$cases .= "<input class='".$classCase." input_date_2' name='".$name."-".$champ."' maxlength='8' value='".$val_date."'/>";
			}else{		$input = "";}
		}
		// dossier de gestion
		elseif( $champ == 'dossier')
		{				
				$cases .= "<input class='".$classCase." input_date_2' name='".$name."-".$champ."' maxlength='8' value='".$val_date."'/>";
		}
/* 		// FASJA
		elseif( $champ == 'fasja')
		{	if($contact_tmp["fasja"]=="Oui"){				
				$cases .= "<input class='".$classCase." input_date_2' name='".$name."-".$champ."' maxlength='8' value='".$val_date."'/>";
			}else{		$input = "";}
		} */
		// AGOA
		elseif( $champ == 'agoa')
		{	if($contact_tmp["pv_ag"]=="Oui"){			
				$cases .= "<input class='".$classCase." input_date_2' name='".$name."-".$champ."' maxlength='8' value='".$val_date."'/>";
			}else{		$input = "";}
		}
		// EDITION
		elseif( $champ == 'edition')
		{				
				$cases .= "<input class='".$classCase." input_date_2' name='".$name."-".$champ."' maxlength='8' value='".$val_date."'/>";
		}
		// TOUS LES AUTRES INPUT
		else{				
				$cases .= "<input class='".$classCase." input_date_2' name='".$name."-".$champ."' maxlength='8' value='".$val_date."'/>";
		}
		
		
	
	}
		$cases .= "</td>";
			
		$details_contact .= $cases;

 		////	MODIF / SUPPR / INFOS / CREATION USER (admin général)
 		$cfg_menu_elem = array("objet"=>$objet["contact"], "objet_infos"=>$contact_tmp);
		$contact_tmp["droit_acces"] = ($_GET["id_dossier"]>1)  ?  $droit_acces_dossier  :  droit_acces($objet["contact"],$contact_tmp);
		if($contact_tmp["droit_acces"]>=2)	{			}
 			
 		 if($_SESSION["user"]["admin_general"]==1)	$cfg_menu_elem["options_divers"][] = array("icone_src"=>PATH_TPL."divers/ajouter.png", "text"=>$trad["CONTACT_creer_user"], "action_js"=>"confirmer('".addslashes($trad["CONTACT_creer_user_infos"])."','index.php?action=creer_user&id_contact=".$contact_tmp["id_contact"]."');"); 
 
		////	DIV SELECTIONNABLE + OPTIONS
		$cfg_menu_elem["id_div_element"] = div_element_suivi($objet["contact"], $contact_tmp["id_contact"],1);
//		require PATH_INC."element_menu_contextuel.inc.php";
		////	CONTENU
		echo "<div class='div_elem_contenu'>";
			echo "<table class='div_elem_table'><tr>";
			////	AFFICHAGE BLOCK
					
			////	AFFICHAGE LISTE
			echo "<td class='div_elem_td lien cardinalite' style='width:70px;' >".$cardinalite."</td>";
            echo "<td style='width:70px;text-align:center;' " . $lien_popup_demat . " >" . $contact_numero . "</td>";
			echo "<td class='div_elem_td' style='padding-left:20px;' ".$lien_popup.">".$contact_pave_ident_info."</td>";
			echo "<td class='div_elem_td div_elem_td_right'>".$details_contact."</td>";
					
			echo "</tr></table>";
		echo "</div>";
	echo "</div>";
	
		//// NUMEROTATION LIGNES DE DOSSIERS
		$cardinalite++;
/* 		if (($cardinalite % 10) == 1){
			echo "<div class='div_elem_suivi_no_hover' style='". $style_div ." height:20px;'><table class='div_elem_table'><tr class='titre' >";
			echo "<td style='width:70px;'></td>";
			echo "<td style='width:70px;'></td>";
			echo "<td ></td><td ></td>";
			
			for($i=1; $i<=12; $i++){ echo "<td class='div_elem_td' style='width:25px;text-align:center;'>".$i."</td>";}
			for($i=1; $i<=12; $i++){ echo "<td class='div_elem_td' style='width:25px;text-align:center;'>".$i."</td>";}
			echo "</tr></table></div>";
		} */
	}
?>
	</td>
	</tr></table>
		
	<!-- BOUTON D'ENREGISTREMENT -->
	<div style="text-align:center; padding-top:10px;">
		<input type="submit" value="enregistrer" class='submit'/>
		<input name="ajout" type="hidden"/>
	</div> 

	</form>
<?php
}

	////	AUCUN CONTACT
	if(@$cpt_div_element<1)  echo "<div class='div_elem_aucun' style ='margin-top:50px;'>".$trad["CONTACT_aucun_contact"]."</div>";
?>