<?php

////	STYLES
////
	// STYLE DIV TITRE 
	$style_div = "margin:0 5% 0 5%;border-bottom:#555 solid 1px;border-radius:2px;";
	$style_titre = "color: #0B243B; font-size: 1.8em; text-shadow: 2px 2px 5px #000000; margin-top:5px;";
	//	style TABLEAUX 
	$style_cloture = "background-color: #EBD14D;";
	$style_tva = "background-color: #C682E1;";
	$style_saisie = "background-color: #82ABE1;";
	
////	STAT AGENT / STAT AGENCE
////
$perimetre = "";
$h1_agence = isset($_GET['stat_agence']) ? "Statistique Agence" : "Statistique Agent" ;
if(isset($_GET['stat_agence'])) { $perimetre = "2";}
$perimetre_stat = sql_affichage($objet["contact"], "1", "T1.", $perimetre );

////	STATISTIQUES SAISIE
////

// Tableaux stat saisie
$baf['nb'] = $baf['saisi'] = $bar['nb'] = $bar['saisi'] = $bic['nb'] = $bic['saisi'] = $bnc['nb'] = $bnc['saisi'] = $foncier['nb'] = $foncier['saisi'] = $autres['nb'] = $autres['saisi'] = $total['saisi'] = $total['nb'] = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);

// conception de la requette
$corps_select 		= "";
$mois		= "moiscloture_".$_SESSION['millesime'];	$corps_select .= " T1.".$mois;
$imposition	= "imposition_".$_SESSION['millesime'];		$corps_select .= ", T1.".$imposition;
$regime		= "regime_".$_SESSION['millesime'];			$corps_select .= ", T1.".$regime;

$champ_mois		 	=  array("","Sjanv","Sfev","Smar","Savr","Smai","Sjuin","Sjuil","Saou","Ssept","Soct","Snov","Sdec");
$ref_annee			=  array($_SESSION["millesime"]-1, $_SESSION["millesime"]);

foreach($ref_annee as $annee){
	for($i=1; $i<=12; $i++){
		$corps_select 		.= ", ".$champ_mois[$i].$annee;
}	}

// Requette
$liste_contacts = db_tableau_2("SELECT T1.id_contact, ".$corps_select."
	FROM gt_contact T1, gt_sx_saisie T2
	WHERE T1.id_contact = T2.id_contact
        AND T1.saisie = 'Oui'
	AND T1.debut_activite <= '".$_SESSION['millesime']."' 
	AND (T1.fin_activite = '' OR T1.fin_activite = '0' OR T1.fin_activite >= '".$_SESSION['millesime']."')
	".$perimetre_stat."");
	
//	echo '<pre>'.print_r($liste_contacts).'</pre>';

//// Nombres de types de dossiers differents (pour hauteur du tableau de stat)
$type_baf = $type_bar = $type_bic = $type_bnc = $type_foncier = $type_autres = 0; 

// Nombre de dossiers à saisir par mois et stats
foreach($liste_contacts as $dossier){
// BAF
//alert( "impo : ".$dossier["imposition_".$_SESSION['millesime']] ." == ". $dossier["regime_".$_SESSION['millesime']]);
	if($dossier["imposition_".$_SESSION['millesime']] == "BA" && $dossier["regime_".$_SESSION['millesime']] == "FORFAIT"){ 
		for($i = 1 ; $i <= 12 ; $i++){
			if(intval($dossier[$mois]) == $i){
				$baf['nb'][$i] = $baf['nb'][$i] +1; 				// Comptage nombre de dossier par mois de cloture
				$baf['nb'][13] = $baf['nb'][13] +1;
				$total['nb'][$i] = $total['nb'][$i] +1;
				$total['nb'][13] = $total['nb'][13] +1;
				$init = 4+$i;	// 1er champ du dossier a controler = 28 champs(27e indices) - 12mois - (mois - cloture)
				for($n=$init; $n <= $init+11 ; $n++){ // 1ere boucle sur $init + 11 autres
					if($dossier[$n] == 'S' OR $dossier[$n] == 'X' OR $dossier[$n] == 'R'){
						$baf['saisi'][$i] = $baf['saisi'][$i]+1; 
						$baf['saisi'][13] = $baf['saisi'][13]+1;
						$total['saisi'][$i] = $total['saisi'][$i] +1;
						$total['saisi'][13] = $total['saisi'][13] +1;
					}
				} 
			}
		}
		if($type_baf == 0) $type_baf =1;
	}
	//echo '<pre>'.print_r($baf).'</pre>';
	
// BAR
	elseif($dossier["imposition_".$_SESSION['millesime']] == "BA" && $dossier["regime_".$_SESSION['millesime']] == "REEL"){
	for($i = 1 ; $i <= 12 ; $i++){
			if(intval($dossier[$mois]) == $i){
				$bar['nb'][$i] = $bar['nb'][$i] +1; 				// Comptage nombre de dossier par mois de cloture
				$bar['nb'][13] = $bar['nb'][13] +1;
				$total['nb'][$i] = $total['nb'][$i] +1;
				$total['nb'][13] = $total['nb'][13] +1;
				$init = 4+$i;	// 1er champ du dossier a controler = 28 champs(27 indices) - 12mois - (mois - cloture)
				for($n=$init; $n <= $init+11 ; $n++){
					if($dossier[$n] == 'S' OR $dossier[$n] == 'X' OR $dossier[$n] == 'R'){
						$bar['saisi'][$i] = $bar['saisi'][$i]+1; 
						$bar['saisi'][13] = $bar['saisi'][13]+1;
						$total['saisi'][$i] = $total['saisi'][$i] +1;
						$total['saisi'][13] = $total['saisi'][13] +1;
					}
				} 
			}
		}
		if($type_bar == 0) $type_bar =1;
	}

// BIC
	elseif($dossier["imposition_".$_SESSION['millesime']] == "BIC" && $dossier["regime_".$_SESSION['millesime']] == "REEL"){
	for($i = 1 ; $i <= 12 ; $i++){
			if(intval($dossier[$mois]) == $i){
				$bic['nb'][$i] = $bic['nb'][$i] +1; 				// Comptage nombre de dossier par mois de cloture
				$bic['nb'][13] = $bic['nb'][13] +1;
				$total['nb'][$i] = $total['nb'][$i] +1;
				$total['nb'][13] = $total['nb'][13] +1;
				$init = 4+$i;	// 1er champ du dossier a controler = 28 champs(27 indices) - 12mois - (mois - cloture)
				for($n=$init; $n <= $init+11 ; $n++){
					if($dossier[$n] == 'S' OR $dossier[$n] == 'X' OR $dossier[$n] == 'R'){
						$bic['saisi'][$i] = $bic['saisi'][$i]+1; 
						$bic['saisi'][13] = $bic['saisi'][13]+1;
						$total['saisi'][$i] = $total['saisi'][$i] +1;
						$total['saisi'][13] = $total['saisi'][13] +1;
					}
				} 
			}
		}
		if($type_bic == 0) $type_bic =1;
	}

// BNC
	elseif($dossier["imposition_".$_SESSION['millesime']] == "BNC" && $dossier["regime_".$_SESSION['millesime']] == "REEL"){
	for($i = 1 ; $i <= 12 ; $i++){
			if(intval($dossier[$mois]) == $i){
				$bnc['nb'][$i] = $bnc['nb'][$i] +1; 				// Comptage nombre de dossier par mois de cloture
				$bnc['nb'][13] = $bnc['nb'][13] +1;
				$total['nb'][$i] = $total['nb'][$i] +1;
				$total['nb'][13] = $total['nb'][13] +1;
				$init = 4+$i;	// 1er champ du dossier a controler = 28 champs(27 indices) - 12mois - (mois - cloture)
				for($n=$init; $n <= $init+11 ; $n++){
					if($dossier[$n] == 'S' OR $dossier[$n] == 'X' OR $dossier[$n] == 'R'){
						$bnc['saisi'][$i] = $bnc['saisi'][$i]+1;
						$bnc['saisi'][13] = $bnc['saisi'][13]+1;
						$total['saisi'][$i] = $total['saisi'][$i] +1;
						$total['saisi'][13] = $total['saisi'][13] +1;
						
					}
				} 
			}
		}
		if($type_bnc == 0) $type_bnc =1;
	}

// FONCIER
	elseif($dossier["imposition_".$_SESSION['millesime']] == "FONCIER" && $dossier["regime_".$_SESSION['millesime']] == "REEL"){
	for($i = 1 ; $i <= 12 ; $i++){
			if(intval($dossier[$mois]) == $i){
				$foncier['nb'][$i] = $foncier['nb'][$i] +1; 				// Comptage nombre de dossier par mois de cloture
				$foncier['nb'][13] = $foncier['nb'][13] +1;
				$total['nb'][$i] = $total['nb'][$i] +1; 
				$total['nb'][13] = $total['nb'][13] +1;
				$init = 4+$i;	// 1er champ du dossier a controler = 28 champs(27 indices) - 12mois - (mois - cloture)
				for($n=$init; $n <= $init+11 ; $n++){
					if($dossier[$n] == 'S' OR $dossier[$n] == 'X' OR $dossier[$n] == 'R'){
						$foncier['saisi'][$i] = $foncier['saisi'][$i]+1; 
						$foncier['saisi'][13] = $foncier['saisi'][13]+1;
						$total['saisi'][$i] = $total['saisi'][$i] +1;
						$total['saisi'][13] = $total['saisi'][13] +1;
					}
				} 
			}
		}
		if($type_foncier == 0) $type_foncier =1;
	}	

// AUTRES (TVA, ...)
	else{
		for($i = 1 ; $i <= 12 ; $i++){
			if(intval($dossier[$mois]) == $i){
				$autres['nb'][$i] = $autres['nb'][$i] +1; 				// Comptage nombre de dossier par mois de cloture
				$autres['nb'][13] = $autres['nb'][13] +1;
				$total['nb'][$i] = $total['nb'][$i] +1;
				$total['nb'][13] = $total['nb'][13] +1;
				$init = 4+$i;	// 1er champ du dossier a controler = 28 champs(27 indices) - 12mois - (mois - cloture)
				for($n=$init; $n <= $init+11 ; $n++){
					if($dossier[$n] == 'S' OR $dossier[$n] == 'X' OR $dossier[$n] == 'R'){
						$autres['saisi'][$i] = $autres['saisi'][$i]+1; 
						$autres['saisi'][13] = $autres['saisi'][13]+1;
						$total['saisi'][$i] = $total['saisi'][$i] +1;	
						$total['saisi'][13] = $total['saisi'][13] +1;					
					}
				} 
			}
		}
		if($type_autres == 0) $type_autres =1;
	}
}
// TRAITEMENT
for($i = 1 ; $i <= 12 ; $i++){
	if($baf['nb'][$i] > 0){		$baf['saisi'][$i] = ceil(($baf['saisi'][$i] / ($baf['nb'][$i]*12))*1000)/10 ;	} 
	if($bar['nb'][$i] > 0){		$bar['saisi'][$i] = ceil(($bar['saisi'][$i] / ($bar['nb'][$i]*12))*1000)/10 ;	}
	if($bic['nb'][$i] > 0){		$bic['saisi'][$i] = ceil(($bic['saisi'][$i] / ($bic['nb'][$i]*12))*1000)/10 ;	}
	if($bnc['nb'][$i] > 0){		$bnc['saisi'][$i] = ceil(($bnc['saisi'][$i] / ($bnc['nb'][$i]*12))*1000)/10 ;	}
	if($foncier['nb'][$i] > 0){	$foncier['saisi'][$i] = ceil(($foncier['saisi'][$i] / ($foncier['nb'][$i]*12))*1000)/10 ;}
	if($autres['nb'][$i] > 0){	$autres['saisi'][$i] = ceil(($autres['saisi'][$i] / ($autres['nb'][$i]*12))*1000)/10 ;}
	if($total['nb'][$i] > 0){	$total['saisi'][$i] = ceil(($total['saisi'][$i] / ($total['nb'][$i]*12))*1000)/10 ;	}
}
if($baf['nb'][13] > 0){		$baf['saisi'][13] = ceil(($baf['saisi'][13] / ($baf['nb'][13]*12))*1000)/10;	} 
if($bar['nb'][13] > 0){		$bar['saisi'][13] = ceil(($bar['saisi'][13] / ($bar['nb'][13]*12))*1000)/10;	}
if($bic['nb'][13] > 0){		$bic['saisi'][13] = ceil(($bic['saisi'][13] / ($bic['nb'][13]*12))*1000)/10;	}
if($bnc['nb'][13] > 0){		$bnc['saisi'][13] = ceil(($bnc['saisi'][13] / ($bnc['nb'][13]*12))*1000)/10;	}
if($foncier['nb'][13] > 0){	$foncier['saisi'][13] = ceil(($foncier['saisi'][13] / ($foncier['nb'][13]*12))*1000)/10;	}
if($autres['nb'][13] > 0){	$autres['saisi'][13] = ceil(($autres['saisi'][13] / ($autres['nb'][13]*12))*1000)/10;	}
if($total['nb'][13] > 0){	$total['saisi'][13] = ceil(($total['saisi'][13] / ($total['nb'][13]*12))*1000)/10;	}

// nombre de types différents de dossiers (hauteur du div)
$nb_type = $type_baf + $type_bar + $type_bic + $type_bnc + $type_foncier + $type_autres ;
	$H = $nb_type*40+50;
	$hauteur = "height:".$H."px;";


/////////////////////////
////	STATISTIQUES TVA
////

// Tableaux stat tva
$mensuelle['nb']=0;	for($i=1 ; $i<=12 ; $i++)	{ $mensuelle['fait'][$i] = 0;	}
$trim['nb'] = 0 ;	for($i=1 ; $i<=4 ; $i++)	{ $trim['fait'][$i] = 0; }
$annuelle['nb'] = $annuelle['fait'] = 0;

$liste_tva = db_tableau_2("SELECT T1.id_contact, T1.tva_".$_SESSION['millesime'].", T2.*
	FROM gt_contact T1, gt_sx_tva_".$_SESSION['millesime']." T2
	WHERE T1.id_contact = T2.id_contact 
	AND T1.debut_activite <= '".$_SESSION['millesime']."' 
	AND (T1.fin_activite = '' OR T1.fin_activite = '0' OR T1.fin_activite >= '".$_SESSION['millesime']."')
	".$perimetre_stat."");

foreach($liste_tva as $dossier){

// MENSUELLE
if($dossier["tva_".$_SESSION['millesime']] == "MENSUELLE"){
	$mensuelle['nb']++; 									// nombre de tva mensuelle
	for($i=1 ; $i<=12 ; $i++){
		$var = "tva_".$i."m_date";
		if($dossier[$var] != "") $mensuelle['fait'][$i]++;	// nombre de tva faites par mois
	}
	
}elseif($dossier["tva_".$_SESSION['millesime']] == "TRIMESTRIELLE"){
	$trim['nb']++; 									// nombre de tva trimestrielle
	for($i=1 ; $i<=4 ; $i++){
		$var = "tva_".$i."t_date";
		if($dossier[$var] != "") $trim['fait'][$i]++;	// nombre de tva faites par trimestre
	}
	
}elseif($dossier["tva_".$_SESSION['millesime']] == "ANNUELLE"){
	$annuelle['nb']++;
	if($dossier["tva_a_date"] != "") $annuelle['fait']++;
	
}
}


////////////////////////////
////	STATISTIQUES CLOTURE
////

// Tableaux stat clôtures
$clot_baf['nb'] = $clot_baf['fini'] = $clot_baf['hdelai'] = $clot_baf['reste'] = $clot_bar['nb'] = $clot_bar['fini'] = $clot_bar['hdelai'] = $clot_bar['reste'] = $clot_bic['nb'] = $clot_bic['fini'] = $clot_bic['hdelai'] = $clot_bic['reste'] = $clot_bnc['nb'] = $clot_bnc['fini'] = $clot_bnc['hdelai'] = $clot_bnc['reste'] = $clot_foncier['nb'] = $clot_foncier['fini'] = $clot_foncier['hdelai'] = $clot_foncier['reste'] = $clot_total['nb'] = $clot_total['fini'] = $clot_total['hdelai'] = $clot_total['reste'] = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);

// conception de la requette
$corps_select 		= "";
$mois		= "moiscloture_".$_SESSION['millesime'];	$corps_select .= " T1.".$mois;
$imposition	= "imposition_".$_SESSION['millesime'];		$corps_select .= ", T1.".$imposition;
$regime		= "regime_".$_SESSION['millesime'];			$corps_select .= ", T1.".$regime;

$liste_cloture = db_tableau_2("SELECT T1.id_contact, ".$corps_select.", T2.*
	FROM gt_contact T1, gt_sx_cloture_".$_SESSION['millesime']." T2
	WHERE T1.id_contact = T2.id_contact 
	AND T1.debut_activite <= '".$_SESSION['millesime']."' 
	AND (T1.fin_activite = '' OR T1.fin_activite = '0' OR T1.fin_activite >= '".$_SESSION['millesime']."')
	".$perimetre_stat."");

	//// DATE LIMITE DE DEPOT
	$limite = db_valeur("SELECT depot FROM gt_delais WHERE an = '".$_SESSION['millesime']."' AND  EXISTS (SELECT depot FROM gt_delais WHERE an = '".$_SESSION['millesime']."')");
	$delai = ($limite == '' || $limite == NULL) ? '30-04-'.($_SESSION['millesime']+1) : $limite;
	
foreach($liste_cloture as $dossier){

//	FORFAIT BA
if($dossier["imposition_".$_SESSION['millesime']] == "BA" && $dossier["regime_".$_SESSION['millesime']] == "FORFAIT"){
	for($i = 1 ; $i <= 12 ; $i++){
		if(intval($dossier[$mois]) == $i){
			$clot_baf['nb'][$i] = $clot_baf['nb'][$i] +1; 			// Comptage nombre de dossier par mois de cloture
			$clot_baf['nb'][13] = $clot_baf['nb'][13] +1;			// total baf
			$clot_total['nb'][$i] = $clot_total['nb'][$i] +1;		// total mensuel
			$clot_total['nb'][13] = $clot_total['nb'][13] +1;		// total général
			if($dossier['dossier'] != ""){
				$delais_forfait = '30-06-'.($_SESSION['millesime']+1); 
				if(delais($dossier['dossier'], $delais_forfait)){
					$clot_baf['fini'][$i]++;	// nombre de dosier de gestion faits par mois
					$clot_total['fini'][$i]++;
				}else{
					$clot_baf['hdelai'][$i]++;
					$clot_total['hdelai'][$i]++;
				}
			}else{
				$clot_baf['reste'][$i]++; 
				$clot_total['reste'][$i]++;
			}
		}
	}
}
//	REEL BA
elseif($dossier["imposition_".$_SESSION['millesime']] == "BA" && $dossier["regime_".$_SESSION['millesime']] == "REEL"){
	for($i = 1 ; $i <= 12 ; $i++){
		if(intval($dossier[$mois]) == $i){
			$clot_bar['nb'][$i] = $clot_bar['nb'][$i] +1; 			// Comptage nombre de dossier par mois de cloture
			$clot_bar['nb'][13] = $clot_bar['nb'][13] +1;			// total bar
			$clot_total['nb'][$i] = $clot_total['nb'][$i] +1;		// total mensuel
			$clot_total['nb'][13] = $clot_total['nb'][13] +1;		// total général
			if($dossier['cloture'] != ""){
				if(delais($dossier['cloture'], $delai)){
					$clot_bar['fini'][$i]++;	// nombre de dosier de reels faits par mois
					$clot_total['fini'][$i]++;
				}else{
					$clot_bar['hdelai'][$i]++;
					$clot_total['hdelai'][$i]++;
				}
			}else{
				$clot_bar['reste'][$i]++; 
				$clot_total['reste'][$i]++;
			}
		}
	}
}
//	BIC
elseif($dossier["imposition_".$_SESSION['millesime']] == "BIC" && $dossier["regime_".$_SESSION['millesime']] == "REEL"){
	for($i = 1 ; $i <= 12 ; $i++){
		if(intval($dossier[$mois]) == $i){
			$clot_bic['nb'][$i] = $clot_bic['nb'][$i] +1; 			// Comptage nombre de dossier par mois de cloture
			$clot_bic['nb'][13] = $clot_bic['nb'][13] +1;			// total baf
			$clot_total['nb'][$i] = $clot_total['nb'][$i] +1;		// total mensuel
			$clot_total['nb'][13] = $clot_total['nb'][13] +1;		// total général
			if($dossier['cloture'] != ""){
				if(delais($dossier['cloture'], $delai)){
					$clot_bic['fini'][$i]++;	// nombre de dosier de gestion faits par mois
					$clot_total['fini'][$i]++;
				}else{
					$clot_bic['hdelai'][$i]++;
					$clot_total['hdelai'][$i]++;
				}
			}else{
				$clot_bic['reste'][$i]++; 
				$clot_total['reste'][$i]++;
			}
		}
	}
}
//	BNC
elseif($dossier["imposition_".$_SESSION['millesime']] == "BNC" && $dossier["regime_".$_SESSION['millesime']] == "REEL"){
	for($i = 1 ; $i <= 12 ; $i++){
		if(intval($dossier[$mois]) == $i){
			$clot_bnc['nb'][$i] = $clot_bnc['nb'][$i] +1; 			// Comptage nombre de dossier par mois de cloture
			$clot_bnc['nb'][13] = $clot_bnc['nb'][13] +1;			// total baf
			$clot_total['nb'][$i] = $clot_total['nb'][$i] +1;		// total mensuel
			$clot_total['nb'][13] = $clot_total['nb'][13] +1;		// total général
			if($dossier['cloture'] != ""){
				if(delais($dossier['cloture'], $delai)){
					$clot_bnc['fini'][$i]++;	// nombre de dosier de gestion faits par mois
					$clot_total['fini'][$i]++;
				}else{
					$clot_bnc['hdelai'][$i]++;
					$clot_total['hdelai'][$i]++;
				}
			}else{
				$clot_bnc['reste'][$i]++; 
				$clot_total['reste'][$i]++;
			}
		}
	}
}
//	FONCIER
elseif($dossier["imposition_".$_SESSION['millesime']] == "FONCIER" && $dossier["regime_".$_SESSION['millesime']] == "REEL"){
	for($i = 1 ; $i <= 12 ; $i++){
		if(intval($dossier[$mois]) == $i){
			$clot_foncier['nb'][$i] = $clot_foncier['nb'][$i] +1; 			// Comptage nombre de dossier par mois de cloture
			$clot_foncier['nb'][13] = $clot_foncier['nb'][13] +1;			// total baf
			$clot_total['nb'][$i] = $clot_total['nb'][$i] +1;		// total mensuel
			$clot_total['nb'][13] = $clot_total['nb'][13] +1;		// total général
			if($dossier['cloture'] != ""){
				if(delais($dossier['cloture'], $delai)){
					$clot_foncier['fini'][$i]++;	// nombre de dosier de gestion faits par mois
					$clot_total['fini'][$i]++;
				}else{
					$clot_foncier['hdelai'][$i]++;
					$clot_total['hdelai'][$i]++;
				}
			}else{
				$clot_foncier['reste'][$i]++; 
				$clot_total['reste'][$i]++;
			}
		}
	}
}

}
//	calcul colonne total
for($i=1; $i<=12; $i++){
	$clot_baf['fini'][13] += $clot_baf['fini'][$i];			$clot_total['fini'][13] += $clot_baf['fini'][$i];
	$clot_bar['fini'][13] += $clot_bar['fini'][$i];			$clot_total['fini'][13] += $clot_bar['fini'][$i];
	$clot_bic['fini'][13] += $clot_bic['fini'][$i];			$clot_total['fini'][13] += $clot_bic['fini'][$i];
	$clot_bnc['fini'][13] += $clot_bnc['fini'][$i];			$clot_total['fini'][13] += $clot_bnc['fini'][$i];
	$clot_foncier['fini'][13] += $clot_foncier['fini'][$i];	$clot_total['fini'][13] += $clot_foncier['fini'][$i];
	
	$clot_baf['hdelai'][13] += $clot_baf['hdelai'][$i];			$clot_total['hdelai'][13] += $clot_baf['hdelai'][$i];
	$clot_bar['hdelai'][13] += $clot_bar['hdelai'][$i];			$clot_total['hdelai'][13] += $clot_bar['hdelai'][$i];
	$clot_bic['hdelai'][13] += $clot_bic['hdelai'][$i];			$clot_total['hdelai'][13] += $clot_bic['hdelai'][$i];
	$clot_bnc['hdelai'][13] += $clot_bnc['hdelai'][$i];			$clot_total['hdelai'][13] += $clot_bnc['hdelai'][$i];
	$clot_foncier['hdelai'][13] += $clot_foncier['hdelai'][$i];	$clot_total['hdelai'][13] += $clot_foncier['hdelai'][$i];
	
	$clot_baf['reste'][13] += $clot_baf['reste'][$i];			$clot_total['reste'][13] += $clot_baf['reste'][$i];
	$clot_bar['reste'][13] += $clot_bar['reste'][$i];			$clot_total['reste'][13] += $clot_bar['reste'][$i];
	$clot_bic['reste'][13] += $clot_bic['reste'][$i];			$clot_total['reste'][13] += $clot_bic['reste'][$i];
	$clot_bnc['reste'][13] += $clot_bnc['reste'][$i];			$clot_total['reste'][13] += $clot_bnc['reste'][$i];
	$clot_foncier['reste'][13] += $clot_foncier['reste'][$i];	$clot_total['reste'][13] += $clot_foncier['reste'][$i];
}



////	AFFICHAGE STATISTIQUES 	////
////							////
	
if(count($liste_contacts)>0){		
	//// CHOIX D'AFFICHAGE : LE MILLESIME COURANT / TOUT
	$affich = isset($_COOKIE['cook-affsaisie']) ? $_COOKIE['cook-affsaisie'] : "";
//	if()	$affich =;
	

?>
	<!-- INFO GAUCHE -->
	<div class="info1 div_elem_infos">
		<p ><a href="<?php echo  isset($_GET['stat_agence']) ? php_self()."\" >Stat Agent?" : php_self()."?stat_agence=tout\" >Stat Agence?" ; ?></a>
		<hr/>
		</p>
	</div>
	
	<!-- INFO DROITE 
	<div class="info2 div_elem_infos	">
		<p ><a href="<?php echo php_self(); ?>?stat_agence=tout" >Stat Agence?</a>
		<hr/>
		</p>
	</div>
	-->
	<h1 style="text-align:center;" class="suivi"><?php echo $h1_agence; ?></h1>

 
<!--	STATISTIQUE SAISIE -->
	<div id="div_elem_0" class="div_elem_suivi_no_hover" style="<?php echo $style_div; ?> height:40px;">
		<div class="div_elem_contenu">
		<table class="div_elem_table">
		<tr >
			<td style="padding-left: 10%;" ><h2 style="<?php echo $style_titre; ?>" >Saisie</h2></td>
		</tr>
		</table>
		</div>
	</div>
	
	<div id="div_elem_0" class="div_elem_suivi_no_hover" style="width:80%;margin-left:10%;border-radius:2px; <?php echo $hauteur; ?>" >
		<div class="div_elem_contenu">
		<table class="div_elem_table" style="border-collapse: collapse">
		<tr class="titre" style="<?php echo $style_saisie; ?>">
			<td style="width:70px;text-align:center;" ></td>
			<?php 
			for($i=1; $i<=12; $i++){ echo "<td class='div_elem_td' style='width:25px;text-align:center;'>".$i."</td>";}
			?>
			<td class='div_elem_td' style='width:25px;text-align:center;' > Total</td>
		</tr>
		
		<?php
		//// BAF
		if($baf['nb'][13]>0){
			echo "<tr class='ligne_survol'><td style='width:70px;text-align:center;vertical-align:middel;".$style_saisie.";' >BAF</td>";
			for($i = 1 ; $i <= 13 ; $i++){
				$pourcent = ($baf['saisi'][$i]>0) ? $baf['saisi'][$i]."%" : "" ; $style ="";
				if($i == 13) $style = $style_saisie;
				echo "<td class='div_elem_td' style='width:25px;text-align:center;".$style."'>".$pourcent."</td>";
				if($i == 13) echo "</tr>";
			}
			echo "<tr class='ligne_survol'><td style='".$style_saisie.";'></td>";
			for($i = 1 ; $i <= 13 ; $i++){
				$nombre = ($baf['nb'][$i]>0) ? $baf['nb'][$i] : "" ; $style ="";
				if($i == 13) $style = $style_saisie;
				echo "<td class='div_elem_td' style='width:25px;text-align:center;".$style."'>".$nombre."</td>";
				if($i == 13) echo "</tr>";
			}
			
		}
		
		//// BAR
		if($bar['nb'][13]>0){
			echo "<tr class='ligne_survol'><td style='width:70px;text-align:center;vertical-align:middel;".$style_saisie.";' >BAR</td>";
			for($i = 1 ; $i <= 13 ; $i++){
				$pourcent = ($bar['saisi'][$i]>0) ? $bar['saisi'][$i]."%" : "" ; $style ="";
				if($i == 13) $style = $style_saisie;
				echo "<td class='div_elem_td' style='width:25px;text-align:center;".$style."'>".$pourcent."</td>";
				if($i == 13) echo "</tr>";
			}
			echo "<tr class='ligne_survol'><td style='".$style_saisie.";'></td>";
			for($i = 1 ; $i <= 13 ; $i++){
				$nombre = ($bar['nb'][$i]>0) ? $bar['nb'][$i] : "" ; $style ="";
				if($i == 13) $style = $style_saisie;
				echo "<td class='div_elem_td' style='width:25px;text-align:center;".$style."'>".$nombre."</td>";
				if($i == 13) echo "</tr>";
			}
			
		}
		
		//// BIC
		if($bic['nb'][13]>0){
			echo "<tr class='ligne_survol'><td style='width:70px;text-align:center;vertical-align:middel;".$style_saisie.";' >BIC</td>";
			for($i = 1 ; $i <= 13 ; $i++){
				$pourcent = ($bic['saisi'][$i]>0) ? $bic['saisi'][$i]."%" : "" ; $style ="";
				if($i == 13) $style = $style_saisie;
				echo "<td class='div_elem_td' style='width:25px;text-align:center;".$style."'>".$pourcent."</td>";
				if($i == 13) echo "</tr>";
			}
			echo "<tr class='ligne_survol'><td style='".$style_saisie.";'></td>";
			for($i = 1 ; $i <= 13 ; $i++){
				$nombre = ($bic['nb'][$i]>0) ? $bic['nb'][$i] : "" ; $style ="";
				if($i == 13) $style = $style_saisie;
				echo "<td class='div_elem_td' style='width:25px;text-align:center;".$style."'>".$nombre."</td>";
				if($i == 13) echo "</tr>";
			}
			
		}
		
		//// BNC
		if($bnc['nb'][13]>0){
			echo "<tr class='ligne_survol'><td style='width:70px;text-align:center;vertical-align:middel;".$style_saisie.";' >BNC</td>";
			for($i = 1 ; $i <= 13 ; $i++){
				$pourcent = ($bnc['saisi'][$i]>0) ? $bnc['saisi'][$i]."%" : "" ; $style ="";
				if($i == 13) $style = $style_saisie;
				echo "<td class='div_elem_td' style='width:25px;text-align:center;".$style."'>".$pourcent."</td>";
				if($i == 13) echo "</tr>";
			}
			echo "<tr class='ligne_survol'><td style='".$style_saisie.";'></td>";
			for($i = 1 ; $i <= 13 ; $i++){
				$nombre = ($bnc['nb'][$i]>0) ? $bnc['nb'][$i] : "" ; $style ="";
				if($i == 13) $style = $style_saisie;
				echo "<td class='div_elem_td' style='width:25px;text-align:center;".$style."'>".$nombre."</td>";
				if($i == 13) echo "</tr>";
			}
			
		}
		
		//// FONCIER
		if($foncier['nb'][13]>0){
			echo "<tr class='ligne_survol'><td style='width:70px;text-align:center;vertical-align:middel;".$style_saisie.";' >FONCIER</td>";
			for($i = 1 ; $i <= 13 ; $i++){
				$pourcent = ($foncier['saisi'][$i]>0) ? $foncier['saisi'][$i]."%" : "" ; $style ="";
				if($i == 13) $style = $style_saisie;
				echo "<td class='div_elem_td' style='width:25px;text-align:center;".$style."'>".$pourcent."</td>";
				if($i == 13) echo "</tr>";
			}
			echo "<tr class='ligne_survol'><td style='".$style_saisie.";'></td>";
			for($i = 1 ; $i <= 13 ; $i++){
				$nombre = ($foncier['nb'][$i]>0) ? $foncier['nb'][$i] : "" ; $style ="";
				if($i == 13) $style = $style_saisie;
				echo "<td class='div_elem_td' style='width:25px;text-align:center;".$style."'>".$nombre."</td>";
				if($i == 13) echo "</tr>";
			}
			
		}
		
		//// AUTRES
		if($autres['nb'][13]>0){
			echo "<tr class='ligne_survol'><td style='width:70px;text-align:center;vertical-align:middel;".$style_saisie.";' >AUTRES</td>";
			for($i = 1 ; $i <= 13 ; $i++){
				$pourcent = ($autres['saisi'][$i]>0) ? $autres['saisi'][$i]."%" : "" ; $style ="";
				if($i == 13) $style = $style_saisie;
				echo "<td class='div_elem_td' style='width:25px;text-align:center;".$style."'>".$pourcent."</td>";
				if($i == 13) echo "</tr>";
			}
			echo "<tr class='ligne_survol'><td style='".$style_saisie.";'></td>";
			for($i = 1 ; $i <= 13 ; $i++){
				$nombre = ($autres['nb'][$i]>0) ? $autres['nb'][$i] : "" ; $style ="";
				if($i == 13) $style = $style_saisie;
				echo "<td class='div_elem_td' style='width:25px;text-align:center;".$style."'>".$nombre."</td>";
				if($i == 13) echo "</tr>";
			}
			
		}
		
		//// TOTAL
		if($total['nb'][13]>0){
			echo "<tr class='ligne_survol'><td style='width:70px;text-align:center;vertical-align:middel;".$style_saisie.";' >TOTAL</td>";
			for($i = 1 ; $i <= 13 ; $i++){
				$pourcent = ($total['saisi'][$i]>0) ? $total['saisi'][$i]."%" : "" ;
				echo "<td class='div_elem_td' style='width:25px;text-align:center;".$style_saisie."'>".$pourcent."</td>";
				if($i == 13) echo "</tr>";
			}
			echo "<tr class='ligne_survol'><td style='".$style_saisie.";'></td>";
			for($i = 1 ; $i <= 13 ; $i++){
				$nombre = ($total['nb'][$i]>0) ? $total['nb'][$i] : "" ;
				echo "<td class='div_elem_td' style='width:25px;text-align:center;".$style_saisie."'>".$nombre."</td>";
				if($i == 13) echo "</tr>";
			}
			
		}
		?>
		</table>
		</div>
	</div>
	
<!--	GRAPHIQUE STATISTIQUE SAISIE -->
		<script type="text/javascript">
$(function () {
    Highcharts.setOptions({
        colors: ['#058DC7', '#24CBE5', '#50B432', '#ED561B', '#DDDF00', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
    });
var cat =['','Janv','Fev','Mar','Avr','Mai','Juin','Juil','Aout','Sept','Oct','Nov','Dec', 'Total'];
var cat_dossier = ['','BAF','BAR','BIC','BNC','FONCIER','AUTRES'];
    Highcharts.setOptions({
        lang: {  drillUpText: '<< Retour {series.name}'  }
    });
     $('#container').highcharts({
        chart: {
            backgroundColor: {
                linearGradient: [0, 0, 500, 500],
                stops: [
                    [0, 'rgb(255, 255, 255)'],
                    [1, 'rgb(226, 226, 255)']
                ]
            },
            type: 'column'
        },
        title: {
            text: 'Saisie'
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
			max: 100,
            title: {
                text: 'Pourcentage'
            }
        },
		subtitle: {
            text: 'Cliquez sur une colone pour voir le détail.',
            align: 'center'
        },
		plotOptions: {
			series: {
				dataLabels: {
					enabled: true,
					format: '{point.y:.1f}%'
				}
			}
		},
		credits: {
            enabled: false
        },
        series: [{
            name: 'Sasie',
            colorByPoint: true,
            data: [
			<?php for($i=1 ; $i<=13 ; $i++){
				echo "{name: cat[".$i."], \t\n ";
				echo "y: ".$total['saisi'][$i].", \t\n ";
				echo "drilldown: cat[".$i."] \t\n";
				echo "} \t\n";
				if($i<13) echo ", \t\n";
			}
			?>
			]
        }],
        drilldown: {
            series: [
			<?php for($i=1 ; $i<=13 ; $i++){
				echo "{ \t\n";
				echo "id: cat[".$i."], \t\n";
				echo "data: [ \t\n";
					echo "['BAF', ".$baf['saisi'][$i]."], \t\n";
                    echo "['BAR', ".$bar['saisi'][$i]."], \t\n";
                    echo "['BIC', ".$bic['saisi'][$i]."], \t\n";
                    echo "['BNC', ".$bnc['saisi'][$i]."], \t\n";
                    echo "['FONCIER', ".$foncier['saisi'][$i]."], \t\n";
                    echo "['AUTRES', ".$autres['saisi'][$i]."] \t\n";
				echo "] \t\n";
				echo "} \t\n";
				if($i<13) echo ", ";
			}
			?> 
			]
        },
    });
});
		</script>	
<script src="../Highcharts-3.0.10/js/highcharts.js"></script>
<script src="../Highcharts-3.0.10/js/modules/data.js"></script>
<script src="../Highcharts-3.0.10/js/modules/drilldown.js"></script>
<!-- <script src="../Highcharts-3.0.10/js/themes/gray.js"></script>	// THEMES DU GRAPHIQUE -->

	<div id="div_elem_0" class="div_elem_suivi_no_hover" style="width:80%;margin-left:10%;border-radius:2px;margin-bottom:30px; " >
		<div class="div_elem_contenu" style="">
			<div id="container" style="width:90%;height:300px;padding-left:5%;margin-top:20px;"></div>
		</div>
	</div>
		

	
<!--	STATISTIQUE TVA -->
	<div id="div_elem_0" class="div_elem_suivi_no_hover" style="<?php echo $style_div; ?> height:40px;">
		<div class="div_elem_contenu">
		<table class="div_elem_table">
		<tr >
			<td style="padding-left: 10%;" ><h2 style="<?php echo $style_titre; ?>" >TVA</h2></td>
		</tr>
		</table>
		</div>
	</div>

	<div id="div_elem_0" class="div_elem_suivi_no_hover" style="width:80%;margin-left:10%;border-radius:2px;margin-bottom:30px; ">
		<div class="div_elem_contenu" style="text-align:center;">
		<table class="div_elem_table" style="text-align:center;border-collapse: collapse; border: 2px solid #C682E1;" >
		<tr class='ligne_survol'>
			<td style="<?php echo $style_tva; ?>">Annuelles</td><td>Réalis.</td>	<td colspan='12' class='div_elem_td' ><?php echo $annuelle['fait']; ?></td>
		</tr>
		<tr class='ligne_survol'>
			<td style="<?php echo $style_tva; ?>"></td>	<td>a faire</td><td colspan='12' class='div_elem_td' ><?php echo $annuelle['nb']; ?></td>
		</tr>
		<tr class='ligne_survol'>
			<td style="<?php echo $style_tva; ?>">trimestrielles</td><td>Réalis.</td>
			<?php 
			for($i=1; $i<=4; $i++){ echo "<td colspan='3' class='div_elem_td' >".$trim['fait'][$i]."</td>";}
			?>
		</tr>
		<tr class='ligne_survol'>
			<td style="<?php echo $style_tva; ?>"></td>	<td>a faire</td>
			<?php 
			for($i=1; $i<=4; $i++){ echo "<td colspan='3' class='div_elem_td' >".$trim['nb']."</td>";}
			?>
		</tr>
		<tr class='ligne_survol'>
			<td style="<?php echo $style_tva; ?>">Mensuelle</td><td>Réalis.</td>
			<?php 
			for($i=1; $i<=12; $i++){ echo "<td class='div_elem_td' >".$mensuelle['fait'][$i]."</td>";}
			?>
		</tr>
		<tr class='ligne_survol'>
			<td style="<?php echo $style_tva; ?>"></td>	<td>a faire</td>
			<?php 
			for($i=1; $i<=12; $i++){ echo "<td class='div_elem_td' >".$mensuelle['nb']."</td>";}
			?>
		</tr>
		</table>
		</div>
	</div>


<!--	STATISTIQUE CLOTURES -->
	<div id="div_elem_0" class="div_elem_suivi_no_hover" style="<?php echo $style_div; ?> height:40px;">
		<div class="div_elem_contenu">
		<table class="div_elem_table">
		<tr >
			<td style="padding-left: 10%;" ><h2 style="<?php echo $style_titre; ?>" >Clôtures</h2></td>
		</tr>
		</table>
		</div>
	</div>
	
	<div id="div_elem_0" class="div_elem_suivi_no_hover" style="width:80%; margin-left:10%;border-radius:2px; ">
		<div class="div_elem_contenu" style="text-align:center;">
		<table class="div_elem_table" style="text-align:center;" cellspacing="0">
		<tr style="<?php echo $style_cloture; ?>">
			<td colspan = "2"></td>
			<td>31/01</td>
			<td>28/02</td>
			<td>31/03</td>
			<td>30/04</td>
			<td>31/05</td>
			<td>30/06</td>
			<td>31/07</td>
			<td>31/08</td>
			<td>30/09</td>
			<td>31/10</td>
			<td>30/11</td>
			<td>31/12</td>
			<td>Total</td>
		</tr>
		<!-- baf -->
<?php if($clot_baf['nb'][13]>0){ ?>
		<tr class='ligne_survol' >
			<td rowspan="4" style="<?php echo $style_cloture; ?>">BAF<p>(Dossiers économique)</p>
			<td>Délais</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_baf['fini'][$i] == 0) ? "" : $clot_baf['fini'][$i] ;
				if($i == 13) {$td = "<td style='".$style_cloture."'>";}
				else { $td = "<td >";}
				echo $td.$val."</td>";
			} ?>
		</tr>
		<tr class='ligne_survol'><td>Hors délais</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_baf['hdelai'][$i] == 0) ? "" : $clot_baf['hdelai'][$i] ;
				if($i == 13) {$td = "<td style='".$style_cloture."'>";}
				else { $td = "<td >";}
				echo $td.$val."</td>";
			} ?>
		</tr>
		<tr class='ligne_survol'><td>Reste à faire</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_baf['reste'][$i] == 0) ? "" : $clot_baf['reste'][$i] ;
				if($i == 13) {$td = "<td style='".$style_cloture."'>";}
				else { $td = "<td >";}
				echo $td.$val."</td>";
			} ?>
		</tr>
		<tr style="<?php echo $style_cloture; ?>"><td>total</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_baf['nb'][$i] == 0) ? "" : $clot_baf['nb'][$i] ;
				echo "<td>".$val."</td>";
			} ?>
		</tr>
<?php } ?>
		<!-- bar -->
<?php if($clot_bar['nb'][13]>0){ ?>
		<tr class='ligne_survol'>
			<td rowspan="4" style="<?php echo $style_cloture; ?>">BAR<p>(Liasse)</p>
			<td>Délais</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_bar['fini'][$i] == 0) ? "" : $clot_bar['fini'][$i] ;
				if($i == 13) {$td = "<td style='".$style_cloture."'>";}
				else { $td = "<td >";}
				echo $td.$val."</td>";
			} ?>
		</tr>
		<tr class='ligne_survol'><td>Hors délais</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_bar['hdelai'][$i] == 0) ? "" : $clot_bar['hdelai'][$i] ;
				if($i == 13) {$td = "<td style='".$style_cloture."'>";}
				else { $td = "<td >";}
				echo $td.$val."</td>";
			} ?>
		</tr>
		<tr class='ligne_survol'><td>Reste à faire</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_bar['reste'][$i] == 0) ? "" : $clot_bar['reste'][$i] ;
				if($i == 13) {$td = "<td style='".$style_cloture."'>";}
				else { $td = "<td >";}
				echo $td.$val."</td>";
			} ?>
		</tr>
		<tr style="<?php echo $style_cloture; ?>"><td>total</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_bar['nb'][$i] == 0) ? "" : $clot_bar['nb'][$i] ;
				echo "<td>".$val."</td>";
			} ?>
		</tr>
<?php } ?>
		<!-- bic -->
<?php if($clot_bic['nb'][13]>0){ ?>
		<tr class='ligne_survol'>
			<td rowspan="4" style="<?php echo $style_cloture; ?>">BIC<p>(Liasse)</p>
			<td>Délais</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_bic['fini'][$i] == 0) ? "" : $clot_bic['fini'][$i] ;
				if($i == 13) {$td = "<td style='".$style_cloture."'>";}
				else { $td = "<td >";}
				echo $td.$val."</td>";
			} ?>
		</tr>
		<tr class='ligne_survol'><td>Hors délais</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_bic['hdelai'][$i] == 0) ? "" : $clot_bic['hdelai'][$i] ;
				if($i == 13) {$td = "<td style='".$style_cloture."'>";}
				else { $td = "<td >";}
				echo $td.$val."</td>";
			} ?>
		</tr>
		<tr class='ligne_survol'><td>Reste à faire</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_bic['reste'][$i] == 0) ? "" : $clot_bic['reste'][$i] ;
				if($i == 13) {$td = "<td style='".$style_cloture."'>";}
				else { $td = "<td >";}
				echo $td.$val."</td>";
			} ?>
		</tr>
		<tr style="<?php echo $style_cloture; ?>"><td>total</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_bic['nb'][$i] == 0) ? "" : $clot_bic['nb'][$i] ;
				echo "<td>".$val."</td>";
			} ?>
		</tr>
<?php } ?>
		<!-- bnc -->
<?php if($clot_bnc['nb'][13]>0){ ?>
		<tr class='ligne_survol'>
			<td rowspan="4" style="<?php echo $style_cloture; ?>">BNC<p>(Liasse)</p>
			<td>Délais</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_bnc['fini'][$i] == 0) ? "" : $clot_bnc['fini'][$i] ;
				if($i == 13) {$td = "<td style='".$style_cloture."'>";}
				else { $td = "<td >";}
				echo $td.$val."</td>";
			} ?>
		</tr>
		<tr class='ligne_survol'><td>Hors délais</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_bnc['hdelai'][$i] == 0) ? "" : $clot_bnc['hdelai'][$i] ;
				if($i == 13) {$td = "<td style='".$style_cloture."'>";}
				else { $td = "<td >";}
				echo $td.$val."</td>";
			} ?>
		</tr>
		<tr class='ligne_survol'><td>Reste à faire</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_bnc['reste'][$i] == 0) ? "" : $clot_bnc['reste'][$i] ;
				if($i == 13) {$td = "<td style='".$style_cloture."'>";}
				else { $td = "<td >";}
				echo $td.$val."</td>";
			} ?>
		</tr>
		<tr style="<?php echo $style_cloture; ?>"><td>total</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_bnc['nb'][$i] == 0) ? "" : $clot_bnc['nb'][$i] ;
				echo "<td>".$val."</td>";
			} ?>
		</tr>
<?php } ?>
		<!-- foncier -->
<?php if($clot_foncier['nb'][13]>0){ ?>
		<tr class='ligne_survol'>
			<td rowspan="4" style="<?php echo $style_cloture; ?>">Foncier<p>(2072)</p>
			<td>Délais</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_foncier['fini'][$i] == 0) ? "" : $clot_foncier['fini'][$i] ;
				if($i == 13) {$td = "<td style='".$style_cloture."'>";}
				else { $td = "<td >";}
				echo $td.$val."</td>";
			} ?>
		</tr>
		<tr class='ligne_survol'><td>Hors délais</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_foncier['hdelai'][$i] == 0) ? "" : $clot_foncier['hdelai'][$i] ;
				if($i == 13) {$td = "<td style='".$style_cloture."'>";}
				else { $td = "<td >";}
				echo $td.$val."</td>";
			} ?>
		</tr>
		<tr class='ligne_survol'><td>Reste à faire</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_foncier['reste'][$i] == 0) ? "" : $clot_foncier['reste'][$i] ;
				if($i == 13) {$td = "<td style='".$style_cloture."'>";}
				else { $td = "<td >";}
				echo $td.$val."</td>";
			} ?>
		</tr>
		<tr style="<?php echo $style_cloture; ?>"><td>total</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_foncier['nb'][$i] == 0) ? "" : $clot_foncier['nb'][$i] ;
				echo "<td>".$val."</td>";
			} ?>
		</tr>
<?php } ?>
		<!-- total -->
		<tr class='ligne_survol'>
			<td rowspan="4" style="<?php echo $style_cloture; ?>">TOTAL
			<td>Délais</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_total['fini'][$i] == 0) ? "" : $clot_total['fini'][$i] ;
				if($i == 13) {$td = "<td style='".$style_cloture."'>";}
				else { $td = "<td >";}
				echo $td.$val."</td>";
			} ?>
		</tr>
		<tr class='ligne_survol'><td>Hors délais</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_total['hdelai'][$i] == 0) ? "" : $clot_total['hdelai'][$i] ;
				if($i == 13) {$td = "<td style='".$style_cloture."'>";}
				else { $td = "<td >";}
				echo $td.$val."</td>";
			} ?>
		</tr>
		<tr class='ligne_survol'><td>Reste à faire</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_total['reste'][$i] == 0) ? "" : $clot_total['reste'][$i] ;
				if($i == 13) {$td = "<td style='".$style_cloture."'>";}
				else { $td = "<td >";}
				echo $td.$val."</td>";
			} ?>
		</tr>
		<tr style="<?php echo $style_cloture; ?>"><td>total</td>
			<?php 
			for($i=1 ; $i <=13 ; $i++){
				$val = ($clot_total['nb'][$i] == 0) ? "" : $clot_total['nb'][$i] ;
				echo "<td>".$val."</td>";
			} ?>
		</tr>
	</table>
	</div>
	</div>
	
	
	<!--	GRAPHIQUE STATISTIQUE CLOTURES -->
		<script type="text/javascript">
$(function () {
    Highcharts.setOptions({
        colors: ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9', 
   '#f15c80', '#e4d354', '#8085e8', '#8d4653', '#91e8e1']
    });
     $('#container2').highcharts({
        chart: {
            type: 'pie'
        },
        title: {
            text: 'Clôtures'
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
			max: 100,
            title: {
                text: 'Pourcentage'
            }
        },
		subtitle: {
            text: 'Cliquez pour voir le détail.',
            align: 'left',
			floating: true
        },
		plotOptions: {
			series: {
				dataLabels: {
					enabled: true,
					format: '{point.name}: {point.y:.0f}'
				}
			}
		},
		credits: {
            enabled: true
        },
        series: [{
            name: 'Clôtures',
            colorByPoint: true,
            data: [
				{name: 'Délai',
				y: <?php echo $clot_total['fini'][13];?>,
				drilldown: 'delai'
				},
				{name: 'Hors délai',
				y: <?php echo $clot_total['hdelai'][13];?>,
				drilldown: 'hdelai'
				},
				{name: 'à faire',
				y: <?php echo $clot_total['reste'][13];?>,
				drilldown: 'reste'
				}
			]
        }],
        drilldown: {
            series: [
				{
				id: 'delai',
				data: [
					<?php $vide = 0;
					if($clot_baf['fini'][13]>0) {echo "['BAF', ".$clot_baf['fini'][13]."],"; $vide++;} 
					if($clot_bar['fini'][13]>0) {echo "['BAR', ".$clot_bar['fini'][13]."],"; $vide++;} 
					if($clot_bic['fini'][13]>0) {echo "['BIC', ".$clot_bic['fini'][13]."],"; $vide++;} 
					if($clot_bnc['fini'][13]>0) {echo "['BNC', ".$clot_bnc['fini'][13]."],"; $vide++;} 
					if($clot_foncier['fini'][13]>0) {echo "['FONCIER', ".$clot_foncier['fini'][13]."],"; $vide++;}
					
					if($vide == 0) echo "['vide', 0]";  ?>
					],
				name: 'Délais'
				},
				{
				id: 'hdelai',
				data: [
	<?php $vide = 0;
					if($clot_baf['hdelai'][13]>0) {echo "['BAF', ".$clot_baf['hdelai'][13]."],"; $vide++;} 
					if($clot_bar['hdelai'][13]>0) {echo "['BAR', ".$clot_bar['hdelai'][13]."],"; $vide++;} 
					if($clot_bic['hdelai'][13]>0) {echo "['BIC', ".$clot_bic['hdelai'][13]."],"; $vide++;} 
					if($clot_bnc['hdelai'][13]>0) {echo "['BNC', ".$clot_bnc['hdelai'][13]."],"; $vide++;} 
					if($clot_foncier['hdelai'][13]>0) {echo "['FONCIER', ".$clot_foncier['hdelai'][13]."],"; $vide++;}
					
					if($vide == 0) echo "['vide', 0]";  ?>
					],
				name: 'Hors Délais'
				},
				{
				id: 'reste',
				data: [
	<?php $vide = 0;
					if($clot_baf['reste'][13]>0) {echo "['BAF', ".$clot_baf['reste'][13]."],"; $vide++;} 
					if($clot_bar['reste'][13]>0) {echo "['BAR', ".$clot_bar['reste'][13]."],"; $vide++;} 
					if($clot_bic['reste'][13]>0) {echo "['BIC', ".$clot_bic['reste'][13]."],"; $vide++;} 
					if($clot_bnc['reste'][13]>0) {echo "['BNC', ".$clot_bnc['reste'][13]."],"; $vide++;} 
					if($clot_foncier['reste'][13]>0) {echo "['FONCIER', ".$clot_foncier['reste'][13]."],"; $vide++;}
					
					if($vide == 0) echo "['vide', 0]";  ?>
					],
				name: 'A Clôturer'
				}
			]
        },
    });
});
		</script>	
<script src="../Highcharts-3.0.10/js/highcharts.js"></script>
<script src="../Highcharts-3.0.10/js/modules/data.js"></script>
<script src="../Highcharts-3.0.10/js/modules/drilldown.js"></script>
<!-- <script src="../Highcharts-3.0.10/js/themes/gray.js"></script>	// THEMES DU GRAPHIQUE -->

	<div id="div_elem_0" class="div_elem_suivi_no_hover" style="width:80%;margin-left:10%;border-radius:2px;margin-bottom:30px; " >
		<div class="div_elem_contenu" style="">
			<div id="container2" style="width:90%;height:300px;padding-left:5%;margin-top:20px;"></div>
		</div>
	</div>


<?php
}else{

	////	AUCUN CONTACT
	if(@$cpt_div_element<1)  echo "<div class='div_elem_aucun' style ='margin-top:50px;'>".$trad["CONTACT_aucun_contact"]."</div>";
}
?>