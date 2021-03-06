<?php

////////	ALERTES
////////

////	VIGADE DES INFOS ALERTES PERIMÉES
////
function vider_infos_alertes()
{
	// Alerte tva 			du 5 au 20 	pour les mois 1 - 4 - 7 - 10
	// Alerte tva proceder 	du 22 au 28 pour les mois 1 - 4 - 7 - 10
	// Alerte is 			du 5 au 15 	pour les mois 3 - 6 - 9 - 12
	// Alertes docs 		du 5 au 15 	pour tous les mois
	$jour_actuel = date('d');	
	if($jour_actuel == 1 || $jour_actuel == 3 || $jour_actuel == 5){ // Trois jours differents au cas ou...
	if(db_valeur("SELECT alertes_info FROM gt_suivi WHERE id_suivi = '1'") != 1){ // Si vidage pas encore fait
/* 		// alerte docs
		db_query("UPDATE gt_sx_saisie SET alerte_info_docs='' 
			WHERE EXISTS (SELECT id_contact FROM gt_contact WHERE id_contact<>'')"); */
		
		$mois_actuel = date('m');	
		// acomptes tva (le mois suivant l'acompte) tous regimes
		if( $mois_actuel == 5 || $mois_actuel == 8 || $mois_actuel == 11 ) {
			db_query("UPDATE gt_sx_tva_".$_SESSION['millesime']." T1, gt_sx_saisie T2 SET T1.tva_a_ac1='', T2.alerte_info_tva = '' 
			WHERE EXISTS (SELECT id_contact FROM gt_contact WHERE id_contact<>'')");
		}
		elseif( $mois_actuel == 1){ // pour les BA
			db_query("UPDATE gt_sx_tva_".$_SESSION['millesime']." T1, gt_sx_saisie T2 SET T1.tva_a_ac1='', T2.alerte_info_tva = '' 
			WHERE EXISTS (SELECT id_contact FROM gt_contact WHERE imposition_".$_SESSION['millesime']." == 'BA')");
		}
		elseif( $mois_actuel == 12){ // pour les BIC, BNC, FONCIER
			db_query("UPDATE gt_sx_tva_".$_SESSION['millesime']." T1, gt_sx_saisie T2 SET T1.tva_a_ac1='', T2.alerte_info_tva = '' 
			WHERE EXISTS (SELECT id_contact FROM gt_contact WHERE imposition_".$_SESSION['millesime']." == 'BIC' OR imposition_".$_SESSION['millesime']." == 'BNC' OR imposition_".$_SESSION['millesime']." == 'FONCIER')");
		}
		
		// acomptes is
		if($mois_actuel == 1 || $mois_actuel == 4 || $mois_actuel == 7 || $mois_actuel == 10){
			db_query("UPDATE gt_sx_saisie SET alerte_info_is='' 
			WHERE EXISTS (SELECT id_contact FROM gt_contact WHERE id_contact<>'')");
		}
		
		// vidage alertes fait : ne plus faire
		db_query("UPDATE gt_suivi SET alertes_info = '1' WHERE id_suivi='1'");
	}}
	elseif($jour_actuel >= 21 && !isset($_COOKIE['agora_alerte'])){
		db_query("UPDATE gt_suivi SET alertes_info = '' WHERE id_suivi='1'");
		// Envoi d'un cookie pour ne pas refaire a chaque clic sur la page saisie
		setcookie("agora_alerte", "cookie", time() + 10*24*3600); // 10 jours
	
	}
	else{
		db_query("UPDATE gt_suivi SET alertes_info = '' WHERE id_suivi='1'");
	}
}

////	ALERTES ACOMPTES TVA	////
////

////	TEST DE DATE POUR PERIODE DES ACOMPTES TVA
////
function periode_tva($imposition){
	// Mois sur lesquels les acomptes doivent être envoyés.
	if($imposition == 'BA'){		$periodes_tva = array(1, 4, 7, 10);}
	elseif($imposition == 'BIC' || $imposition == 'BNC' || $imposition == 'FONCIER'){
									$periodes_tva = array(4, 7, 10, 12);}
	else{							$periodes_tva = array(0);}

	$mois_actuel = date('m');	
	$periode = 0;
	foreach($periodes_tva as $mois){
		if($mois == $mois_actuel) {	$periode = 1;	break;	}
	}
	if($periode){
		$date_d = mktime(0,0,0,$mois,05,date('Y'));		//	Alerte du 05
		$date_f = mktime(23,59,59,$mois,20,date('Y'));	//	au 15 du mois
		if((time() > $date_d) && (time() < $date_f)){	return 1;}
		else{	return 0;}
	}else{	return 0;}
}

////	TEST DE DATE POUR ENVOI DES ACOMPTES TVA
////
function periode_tva_proceder($imposition){
	// Mois sur lesquels les acomptes doivent être envoyés.
	if($imposition == 'BA'){		$periodes_tva = array(1, 4, 7, 10); $jour_d = 22;}
	elseif($imposition == 'BIC' || $imposition == 'BNC' || $imposition == 'FONCIER'){
									$periodes_tva = array(4, 7, 10, 12);$jour_d = 15;}
	else{							$periodes_tva = array(0);}
	$mois_actuel = intval(date('m'));	
	$periode = 0;
	foreach($periodes_tva as $mois){
		if($mois == $mois_actuel) {	$periode = 1;	break;	}
	}
	if($periode){
		$date_d = mktime(0,0,0,$mois,$jour_d,date('Y'));	//	Alerte du 15 / 22
		$date_f = mktime(23,59,59,$mois,28,date('Y'));		//	au 28 du mois
		if((time() > $date_d) && (time() < $date_f)){	return 1;}
		else{	return 0;}
	}else{	return 0;}
}

function quel_acompte($id_contact, $ms_cloture, $tva_ex)

{
	$annee = $controle = $acompte = 0;

	$mois_cloture	= intval($ms_cloture);

	$mois_actuel	= intval(date('m'));

    $an_actuel		= intval(date('Y'));

	if($mois_cloture <= 3 && $tva_ex == 'Oui'){
		if($mois_actuel == 1 || $mois_actuel == 2 ){
			$annee = $an_actuel-1; $controle = 1;
		}
		elseif($mois_actuel == 4 || $mois_actuel == 5 ){
			$annee = $an_actuel;
		// Controle si table existe
		if(db_maj_table_ajoute('no_control', 'gt_sx_tva_'.$annee.'')){ $controle = 1;}
		}
		elseif($mois_actuel == 7 || $mois_actuel == 8 ){
			$annee = $an_actuel;
		// Controle si table existe
		if(db_maj_table_ajoute('no_control', 'gt_sx_tva_'.$annee.'')){ $controle = 1;}
		}
		elseif($mois_actuel == 10 || $mois_actuel == 11 ){
			$annee = $an_actuel;
		// Controle si table existe
		if(db_maj_table_ajoute('no_control', 'gt_sx_tva_'.$annee.'')){ $controle = 1;}
		}
	}
	elseif($mois_cloture <= 6 && $tva_ex == 'Oui'){
		if($mois_actuel == 1 || $mois_actuel == 2 ){
			$annee = $an_actuel-1; $controle = 1;
		}
		elseif($mois_actuel == 4 || $mois_actuel == 5 ){
			$annee = $an_actuel-1; $controle = 1;
		}
		elseif($mois_actuel == 7 || $mois_actuel == 8 ){
			$annee = $an_actuel-1;
		// Controle si table existe
		if(db_maj_table_ajoute('no_control', 'gt_sx_tva_'.$annee.'')){ $controle = 1;}
		}
		elseif($mois_actuel == 10 || $mois_actuel == 11 ){
			$annee = $an_actuel;
		// Controle si table existe
		if(db_maj_table_ajoute('no_control', 'gt_sx_tva_'.$annee.'')){ $controle = 1;}
		}
	}
	elseif($mois_cloture <= 9 && $tva_ex == 'Oui'){
		if($mois_actuel == 1 || $mois_actuel == 2 ){
			$annee = $an_actuel-1; $controle = 1;
		}
		elseif($mois_actuel == 4 || $mois_actuel == 5 ){
			$annee = $an_actuel-1; $controle = 1;
		}
		elseif($mois_actuel == 7 || $mois_actuel == 8 ){
			$annee = $an_actuel-1; $controle = 1;
		}
		elseif($mois_actuel == 10 || $mois_actuel == 11 ){
			$annee = $an_actuel;
		// Controle si table existe
		if(db_maj_table_ajoute('no_control', 'gt_sx_tva_'.$annee.'')){ $controle = 1;}
		}
	}
	elseif($mois_cloture <= 12 || $tva_ex == 'Non'){
		if($mois_actuel == 1 || $mois_actuel == 2 ){
			$annee = $an_actuel-1; $controle = 1;
		}
		elseif($mois_actuel == 4 || $mois_actuel == 5 ){
			$annee = $an_actuel-1; $controle = 1;
		}
		elseif($mois_actuel == 7 || $mois_actuel == 8 ){
			$annee = $an_actuel-1; $controle = 1;
		}
		elseif($mois_actuel == 10 || $mois_actuel == 11 ){
			$annee = $an_actuel-1; $controle = 1;
		}
	}
	else{

	}


	if($controle){

		$acompte = db_valeur("SELECT tva_a_montant FROM gt_sx_tva_".$annee." WHERE id_contact = ".$id_contact.";");

               // alert($acompte);

	}

	return $acompte;

}

////	LIEN D'AFFICHAGE DU POPUP GENERATION DU MAIL D'INFORMATION ACOMPTE TVA 
////
function alerte_acompte_tva($id_contact, $ms_cloture, $tva_ex)
{
	// acompte?
	$acompte = quel_acompte($id_contact, $ms_cloture, $tva_ex);

	if($acompte != '' || $acompte != 0){
		$mail_ac_tva = "<span class='lien alertes' onclick=\"popup('mail_ac_tva.php?id_contact=".$id_contact."&acpt=".$acompte."','Acompte TVA', 600, 450);\">Acpt TVA</span>";
		return $mail_ac_tva;
	}
}

////	LIEN D'AFFICHAGE DU POPUP ACOMPTE TVA PROCEDER 
////
function alerte_acompte_tva_proceder($id_contact, $ms_cloture, $tva_ex)
{ 
	// acompte?
	$acompte = quel_acompte($id_contact, $ms_cloture, $tva_ex);
	
	//echo'<pre>'.print_r($test_acompte).'</pre>';
	if($acompte != '' || $acompte != 0){
		$mail_ac_tva = "<span class='lien alertes' onclick=\"popup('info_ac_tva.php?id_contact=".$id_contact."','EDI Acpte');\">EDI Acpte</span>";
		return $mail_ac_tva;
	}
}

////	ALERTES ACOMPTES is	////
////

////	TEST DE DATE POUR PERIODE DES ACOMPTES IS
////
function periode_is(){
	$periode_is = array(3, 6, 9, 12);		// Mois sur lesquels les acomptes doivent être envoyés.
	$mois_actuel = intval(date('m'));	
	$periode = 0;
	foreach($periode_is as $mois){
		if($mois == $mois_actuel) {	$periode = 1;	break;	}
	} 
	if($periode){
		$date_d = mktime(0,0,0,$mois,05,date('Y'));		//	Alerte du 05
		$date_f = mktime(23,59,59,$mois,15,date('Y'));	//	au 15 du mois
		if((time() > $date_d) && (time() < $date_f)){	return 1; }
		else{	return 0;}
	}else{	return 0;}
}

////	LIEN D'AFFICHAGE DU POPUP GENERATION DU MAIL D'INFORMATION ACOMPTE IS
////
function alerte_acompte_is($id_contact)
{
	$mail_ac_is = "<span class='lien alertes' onclick=\"popup('mail_ac_is.php?id_contact=".$id_contact."','Acompte IS');\">Acpt. IS</span>";
	return $mail_ac_is;

}

////	ALERTES DEMANDE DE DOCUMENTS	////
////

////	TEST DEMANDE DE DOCUMENTS
////
function periode_demande_documents( $chois_alerte_docs)
{
if($chois_alerte_docs == 0){ return 0;}
else{	$mois_alerte = array();
	switch ($chois_alerte_docs){
		case 0 :
		//	$mois_alerte = ();
			break;
		case 1 :
			$mois_alerte = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
			break;
		case 2 :
			$mois_alerte = array( 2, 4, 6, 8, 10, 12);
			break;
		case 3 :
			$mois_alerte = array(1, 3, 5, 7, 9, 11);
			break;
		case 4 :
			$mois_alerte = array(1, 4, 7, 10);
			break;
		case 5 :
			$mois_alerte = array(2, 5, 8, 11);
			break;
		case 6 :
			$mois_alerte = array(3, 6, 9, 12);
			break;
		case 7 :
			$mois_alerte = array(1, 5, 9);
			break;
		case 8 :
			$mois_alerte = array(2, 6, 10,);
			break;
		case 9 :
			$mois_alerte = array(3, 7, 11);
			break;
		case 10 :
			$mois_alerte = array(4, 8, 12);
			break;
		case 11	:
			$mois_alerte = array(1, 7, 10);
			break;
		default :
		//	$mois_alerte = ();
			break;
	}
	$mois_actuel = intval(date('m'));	
	$periode = 0;
	foreach($mois_alerte as $mois){
		if($mois == $mois_actuel) {	$periode = 1;	break;	}
	}  
	if($periode){
		$date_d = mktime(0,0,0,$mois,05,date('Y'));		//	Alerte du 05
		$date_f = mktime(23,59,59,$mois,30,date('Y'));	//	au 15 du mois
		if((time() > $date_d) && (time() < $date_f)){	return 1; }
		else{	return 0;}
	}else{	return 0;}
}
}

////	MAIL DEMANDE DOCUMENTS
////
function alerte_demande_doc($id_contact)
{
$mail_dd_doc = "<span class='lien alertes' onclick=\"popup('mail_dde_docs.php?id_contact=".$id_contact."','Demande docs','720','250');\">Dde docs</span>";
		return $mail_dd_doc;
}









?>
