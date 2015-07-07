<?php 

function formate_tableau($tab, $nom)
{
$retour = "";
	if(is_array($tab)){ 
		for($i=1 ; $i <=13 ; $i++){
			$retour .= $nom." ".$i." ".$tab[$i]."% \t ";
		}
	}
	return $retour;
}

function drawTree($myarray, $level = 0)
{

    // Boucle sur chaque élément du tableau
    foreach($myarray as $key => $value)
    {	
   
        // En cas d'objet on convertit en tableau
        if (is_object($value)) $value = (array)$value;
       
        // Si l'élément est un tableau
        if (is_array($value)) {
       
            // On l'affiche en tant que noeud
            echo '<div style="padding-left: ' . ($level * 20) . 'px">[' . $key . ']</div>';
           
            // Puis on affiche son arborescence, à un niveau supérieur
            drawTree($value, $level + 1);
           
        } else {
       
            // C'est une valeur, on l'affiche
            echo '<div style="padding-left: ' . ($level * 20) . 'px">' . $key . ' = <b>' . $value . '</b></div>';
           
        }
       
    }

}
// -----------------------------------------------
//                  STATISTIQUE AGENTS
//------------------------------------------------

	//  compte le nombre de dossiers par AGENT , MOIS DE CLOTURE , REG FISCAL, TYP FISCAL -----
function compte($mois, $reg, $type) {
	$nb = mysql_query("SELECT iD FROM sx_dossiers 
								WHERE ag_".$_SESSION['millesime']." = '".$_SESSION['num_agent']."' 
								AND mois_cloture = '".$mois."' 
								AND benef_fiscal = '".$reg."' 
								AND reg_fiscal_".$_SESSION['millesime']." = '".$type."' 
								AND saisi_adh <= 1 ") or die(mysql_error());
	$nbre = mysql_num_rows($nb);
	if($nbre>0) {return $nbre;} else {return NULL;}
	}   
	
	
    //  La meme mais pour les totaux...	
function comptetotal($benef, $regime) {
	$nb = mysql_query("SELECT iD FROM sx_dossiers 
								WHERE ag_".$_SESSION['millesime']." = '".$_SESSION['num_agent']."' 
								AND benef_fiscal = '".$benef."' 
								AND reg_fiscal_".$_SESSION['millesime']." = '".$regime."'
								AND saisi_adh <= 1") or die(mysql_error());
	$nbre = mysql_num_rows($nb);
	if($nbre>0) {return $nbre;} else {return NULL;}
	}
	
	
	//  POUR SAVOIR SI LE MOIS EST DANS L EXERCICE ET EST SAISI OU PAS
function saisieOuPas($MoisCloture, $moisCase, $anneeCase, $valeur)  {
  	$debut = mktime(12, 0, 0, $MoisCloture, 1, $_SESSION['millesime']-1);
	$case= mktime(12, 0, 0, $moisCase, 1, $anneeCase);
	$fin = mktime(12, 0, 0, $MoisCloture, 1, $_SESSION['millesime']);
	
	if($debut < $case){
		if($case <= $fin){
			if($valeur == 'X' OR $valeur == 'S' OR $valeur == 'R')
			{	return 1;}
			else
			{	return 0;}
		}
		else{
			return 0;
		}
	}
	else{
		return 0;
	}
  }
  
    
	// COMPTAGE DES mois saisis suivant ......
function compte_mois_saisie($moisCloture, $imposition, $regime, $an2) { 

$saisiefait = 0; //comptage
$an1 = $an2-1;
$champ_mois	=  array("","Sjanv","Sfev","Smar","Savr","Smai","Sjuin","Sjuil","Saou","Ssept","Soct","Snov","Sdec");

$req = db_tableau("SELECT Sfev".$an1.", Smar".$an1.", Savr".$an1.", Smai".$an1.", Sjuin".$an1.", Sjuil".$an1.", Saou".$an1.", Ssept".$an1.", Soct".$an1.", Snov".$an1.", Sdec".$an1.", Sjanv".$an2.", Sfev".$an2.", Smar".$an2.", Savr".$an2.", Smai".$an2.", Sjuin".$an2.", Sjuil".$an2.", Saou".$an2.", Ssept".$an2.", Soct".$an2.", Snov".$an2.", Sdec".$an2.", nom
					  FROM gt_sx_saisie T1, gt_contact T2
					  WHERE T1.id_contact = T2.id_contact 
					  AND T2.imposition_".$_SESSION['millesime']." = '".$imposition."' 
					  AND T2.regime_".$_SESSION['millesime']." = '".$regime."' 
					  AND T2.moiscloture_".$_SESSION['millesime']." = '".$moisCloture."'
					  AND T2.saisie = 'Oui'");

//echo '<pre>'.print_r($req).'</pre>';
//$sql = mysql_fetch_assoc($req);
foreach ($req as $champ => $val) {				// Le tableau contient 23 cases
	$init = 12-(12-$moisCloture);				// premier case du millesime du dossier
	for($i = $init ; $i <= $init+12 ; $i++){
		if( $val ='S' OR $val ='X' OR  $val ='R') $saisiefait++;
	}
	}
return $saisiefait;} 

	// COMPTAGE DES mois saisis suivant ...... 2 .....
function comptesaisiet($moisCloture, $regFiscal, $typeFiscal, $an1, $an2) { 
$mzfio = mysql_query("SELECT Sfev$an1, Smar$an1, Savr$an1, Smai$an1, Sjuin$an1, Sjuil$an1, Saou$an1, Ssept$an1, Soct$an1, Snov$an1, Sdec$an1, Sjanv$an2, Sfev$an2, Smar$an2, Savr$an2, Smai$an2, Sjuin$an2, Sjuil$an2, Saou$an2, Ssept$an2, Soct$an2, Snov$an2, Sdec$an2, s.mois_cloture, tvaexercice_".$_SESSION['millesime']."
					  FROM sx_saisie s, sx_dossiers d
					  WHERE s.iD = d.iD 
					  AND s.ag_".$_SESSION['millesime']." = '" . $_SESSION['num_agent'] . "' 
					  AND d.benef_fiscal = '".$regFiscal."' 
					  AND d.reg_fiscal_".$_SESSION['millesime']." = '".$typeFiscal."' 
					  AND s.mois_cloture = '".$moisCloture."'
					  AND d.saisi_adh <= 1") or die(mysql_error());
		$saisiefait = 0;
while ($sql = mysql_fetch_assoc($mzfio)) {
$exerc = 'tvaexercice_'.$_SESSION['millesime'];
		if($sql['$exerc'] == 1){$MsClt = $moisCloture;}
		else {$MsClt = 12;}
//		if( saisieOuPas($MsClt, 1,  $an1, $sql['Sjanv'.$an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 2,  $an1, $sql['Sfev'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 3,  $an1, $sql['Smar'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 4,  $an1, $sql['Savr'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 5,  $an1, $sql['Smai'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 6,  $an1, $sql['Sjuin'.$an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 7,  $an1, $sql['Sjuil'.$an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 8,  $an1, $sql['Saou'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 9,  $an1, $sql['Ssept'.$an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 10, $an1, $sql['Soct'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 11, $an1, $sql['Snov'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 12, $an1, $sql['Sdec'. $an1.''])) $saisiefait++;
		
		if( saisieOuPas($MsClt, 1,  $an2, $sql['Sjanv'.$an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 2,  $an2, $sql['Sfev'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 3,  $an2, $sql['Smar'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 4,  $an2, $sql['Savr'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 5,  $an2, $sql['Smai'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 6,  $an2, $sql['Sjuin'.$an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 7,  $an2, $sql['Sjuil'.$an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 8,  $an2, $sql['Saou'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 9,  $an2, $sql['Ssept'.$an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 10, $an2, $sql['Soct'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 11, $an2, $sql['Snov'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 12, $an2, $sql['Sdec'. $an2.''])) $saisiefait++;
	}
return $saisiefait;} 

	// COMPTAGE DES mois saisis totaux suivant ......
function comptesaisietotal($regFiscal, $typeFiscal, $an1, $an2) { 
$mzfio = mysql_query("SELECT Sfev$an1, Smar$an1, Savr$an1, Smai$an1, Sjuin$an1, Sjuil$an1, Saou$an1, Ssept$an1, Soct$an1, Snov$an1, Sdec$an1, Sjanv$an2, Sfev$an2, Smar$an2, Savr$an2, Smai$an2, Sjuin$an2, Sjuil$an2, Saou$an2, Ssept$an2, Soct$an2, Snov$an2, Sdec$an2, s.mois_cloture
					  FROM sx_saisie s, sx_dossiers d
					  WHERE s.iD = d.iD 
					  AND s.ag_".$_SESSION['millesime']." = '" . $_SESSION['num_agent'] . "' 
					  AND d.benef_fiscal = '".$regFiscal."' 
					  AND d.reg_fiscal_".$_SESSION['millesime']." = '".$typeFiscal."' 
					  AND d.saisi_adh <= 1") or die(mysql_error());
		$saisiefait = 0;
while ($sql = mysql_fetch_assoc($mzfio)) {
	$MsClt = $ligne['$mois_cloture'];
//		if( saisieOuPas($MsClt, 1,  $an1, $sql['Sjanv'.$an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 2,  $an1, $sql['Sfev'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 3,  $an1, $sql['Smar'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 4,  $an1, $sql['Savr'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 5,  $an1, $sql['Smai'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 6,  $an1, $sql['Sjuin'.$an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 7,  $an1, $sql['Sjuil'.$an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 8,  $an1, $sql['Saou'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 9,  $an1, $sql['Ssept'.$an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 10, $an1, $sql['Soct'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 11, $an1, $sql['Snov'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 12, $an1, $sql['Sdec'. $an1.''])) $saisiefait++;
		
		if( saisieOuPas($MsClt, 1,  $an2, $sql['Sjanv'.$an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 2,  $an2, $sql['Sfev'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 3,  $an2, $sql['Smar'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 4,  $an2, $sql['Savr'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 5,  $an2, $sql['Smai'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 6,  $an2, $sql['Sjuin'.$an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 7,  $an2, $sql['Sjuil'.$an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 8,  $an2, $sql['Saou'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 9,  $an2, $sql['Ssept'.$an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 10, $an2, $sql['Soct'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 11, $an2, $sql['Snov'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 12, $an2, $sql['Sdec'. $an2.''])) $saisiefait++;
	}
return $saisiefait;} 



//------------------------------------------------------------------------------
//                     STATISTIQUE AGENCE
//-----------------------------------------------------------------------------

// COMPTAGE DE DOSSIERS
function compte_mois($agent, $reg, $type, $mois) {
	$nb = mysql_query("SELECT iD FROM sx_dossiers 
								WHERE ag_".$_SESSION['millesime']." = '".$agent."' 
								AND mois_cloture = '".$mois."' 
								AND benef_fiscal = '".$reg."' 
								AND reg_fiscal_".$_SESSION['millesime']." = '".$type."' ") or die(mysql_error());
	$nbr = mysql_num_rows($nb);
	return $nbr;
}
	//  DONNE LE NOMBRE DE MOIS SAISIS
function compte_Avanc($agent, $reg, $type, $mois) {

	$nbre = compte_mois($agent, $reg, $type, $mois);
	
	if($nbre<1) {return NULL;}
	else{
	$res = comptesaisieA($agent, $mois, $reg, $type, $_SESSION['millesime']-1, $_SESSION['millesime']);
	return $res;
	}
}
// COMPTAGE DES DOSSIERS PAR MOIS
function compte_dos($agent, $mois) {
	$nb = mysql_query("SELECT iD FROM sx_dossiers 
								WHERE ag_".$_SESSION['millesime']." = '".$agent."' 
								AND mois_cloture = '".$mois."' ") or die(mysql_error());
	$nbr = mysql_num_rows($nb);
	return $nbr;
}
	//  DONNE LE NOMBRE DE MOIS SAISIS
function compte_Av_saisie($agent, $mois) {

	$nbre = compte_dos($agent, $mois);
	
	if($nbre<1) {return NULL;}
	else{
	$res = comptesaisieA($agent, $mois, $reg, $type, $_SESSION['millesime']-1, $_SESSION['millesime']);
	return $res;
	}
}

	
	// DONNE LE NOMBRE DE MOIS CLOTURER  delais respecter
function compte_AvancCd($agent, $reg, $type, $mois) {

	$nbre = compte_mois($agent, $reg, $type, $mois);
	
	if($nbre < 1 ) return NULL;
	else{
		if($type == 'FORFAIT'){
			$champ = 'g.gestion_date';
			$cham  = 'g.iD';
			$table = 'sx_gest_$an2 g';
			$delais_depot = mktime(0,0,0,06,30,$_SESSION['millesime']);   // DELAIS GESTION 30-06-XXXX
		}
		if($type == 'REEL'){
			$champ = 'f.liasse';
			$cham  = 'f.iD';
			$table = 'sx_fisc_$an2 f' ;
			
				$sql = mysql_query("SELECT depot FROM delais WHERE an = '".$_SESSION['millesime']."'") or die(mysql_error());
				while($delais = mysql_fetch_assoc($sql)){
					list($a, $m, $j) = explode('-', $delais['depot']);
					$delais_depot = mktime(0,0,0,$m,$j,$a);
				}
		}
		
		$sql = "SELECT $champ FROM $table, sx_dossiers d
								WHERE $cham = d.iD
								AND d.ag_".$_SESSION['millesime']." = .$agent
								AND d.reg_fiscal_".$_SESSION['millesime']." = $type
								AND d.mois_cloture = '".$mois."' ";
		$res1 = mysql_query($sql) or die ("Erreur : ".mysql_error());
			
		$dos = 0;
		$dl_dos = 0;

		while($res = mysql_fetch_row($res1)){
			if($res[0] != '' ){
				$dl_dos = $dl_dos + delais($res[0], $delais_depot);
			}
			elseif($res[1] != '' ){
				$dl_dos = $dl_dos + delais($res[1], $delais_depot);
			}
			$dos = $dl_dos;
		}
		return  $dos;

		}
	}

	// DONNE LE NOMBRE DE MOIS CLOTURER  delais NON respecter
function compte_AvancCHd($agent, $reg, $type, $mois) {

	$nbre = compte_mois($agent, $reg, $type, $mois);
	
	if($nbre < 1 ) return NULL;
	else{
		if($type == 'FORFAIT'){
			$champ = 'g.gestion_date';
			$cham  = 'g.iD';
			$table = 'sx_gest_$an2 g';
			$delais_depot = mktime(0,0,0,06,30,$_SESSION['millesime']);   // DELAIS GESTION 30-06-XXXX
		}
		if($type == 'REEL'){
			$champ = 'f.liasse';
			$cham  = 'f.iD';
			$table = 'sx_fisc_$an2 f' ;
			
				$sql = mysql_query("SELECT depot FROM delais WHERE an = '".$_SESSION['millesime']."'") or die(mysql_error());
				while($delais = mysql_fetch_assoc($sql)){
					list($a, $m, $j) = explode('-', $delais['depot']);
					$delais_depot = mktime(0,0,0,$m,$j,$a);
				}
		}
		
		$sql = "SELECT $champ FROM $table, sx_dossiers d
								WHERE $cham = d.iD
								AND d.ag_".$_SESSION['millesime']." = $agent
								AND d.reg_fiscal_".$_SESSION['millesime']." = $type
								AND d.mois_cloture = '".$mois."' ";
		$res1 = mysql_query($sql) or die ("Erreur : ".mysql_error());
			
		$dos = 0;
		$dl_dos = 0;

		while($res = mysql_fetch_row($res1)){
			if($res[0] != '' ){
				$dl_dos = $dl_dos + pasDelais($res[0], $delais_depot);
			}
			elseif($res[1] != '' ){
				$dl_dos = $dl_dos + pasDelais($res[1], $delais_depot);
			}
			$dos = $dl_dos;
		}
		return  $dos;

		}
	}


	
    //  La meme mais pour les totaux...	
function comptetotalA($reg, $type) {
	$nb = mysql_query("SELECT iD FROM sx_dossiers 
								WHERE ag_".$_SESSION['millesime']." = '".$_SESSION['num_agent']."' 
								AND benef_fiscal = '".$reg."' 
								AND reg_fiscal_".$_SESSION['millesime']." = '".$type."' ") or die(mysql_error());
	$nbre = mysql_num_rows($nb);
	if($nbre>0) {return $nbre;} else {return NULL;}
	}
	
	
	// COMPTAGE DES mois saisis suivant ......
function comptesaisieA($agent, $moisCloture, $regFiscal, $typeFiscal, $an1, $an2) { 
$mzfio = mysql_query("SELECT Sfev$an1, Smar$an1, Savr$an1, Smai$an1, Sjuin$an1, Sjuil$an1, Saou$an1, Ssept$an1, Soct$an1, Snov$an1, Sdec$an1, Sjanv$an2, Sfev$an2, Smar$an2, Savr$an2, Smai$an2, Sjuin$an2, Sjuil$an2, Saou$an2, Ssept$an2, Soct$an2, Snov$an2, Sdec$an2, s.mois_cloture
					  FROM sx_saisie s, sx_dossiers d
					  WHERE s.iD = d.iD 
					  AND s.ag_".$_SESSION['millesime']." = '" . $agent. "' 
					  AND d.benef_fiscal = '".$regFiscal."' 
					  AND d.reg_fiscal_".$_SESSION['millesime']." = '".$typeFiscal."' 
					  AND s.mois_cloture = '".$moisCloture."'") or die(mysql_error());
		$saisiefait = 0;
while ($sql = mysql_fetch_assoc($mzfio)) {
	$MsClt = $moisCloture;
//		if( saisieOuPas($MsClt, 1,  $an1, $sql['Sjanv'.$an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 2,  $an1, $sql['Sfev'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 3,  $an1, $sql['Smar'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 4,  $an1, $sql['Savr'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 5,  $an1, $sql['Smai'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 6,  $an1, $sql['Sjuin'.$an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 7,  $an1, $sql['Sjuil'.$an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 8,  $an1, $sql['Saou'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 9,  $an1, $sql['Ssept'.$an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 10, $an1, $sql['Soct'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 11, $an1, $sql['Snov'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 12, $an1, $sql['Sdec'. $an1.''])) $saisiefait++;
		
		if( saisieOuPas($MsClt, 1,  $an2, $sql['Sjanv'.$an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 2,  $an2, $sql['Sfev'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 3,  $an2, $sql['Smar'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 4,  $an2, $sql['Savr'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 5,  $an2, $sql['Smai'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 6,  $an2, $sql['Sjuin'.$an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 7,  $an2, $sql['Sjuil'.$an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 8,  $an2, $sql['Saou'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 9,  $an2, $sql['Ssept'.$an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 10, $an2, $sql['Soct'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 11, $an2, $sql['Snov'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 12, $an2, $sql['Sdec'. $an2.''])) $saisiefait++;
	}
return $saisiefait;} 

	// COMPTAGE DES mois saisis suivant ......
function comptesaisieAgMois($agent, $moisCloture, $regFiscal, $typeFiscal, $an1, $an2) { 
$mzfio = mysql_query("SELECT Sfev$an1, Smar$an1, Savr$an1, Smai$an1, Sjuin$an1, Sjuil$an1, Saou$an1, Ssept$an1, Soct$an1, Snov$an1, Sdec$an1, Sjanv$an2, Sfev$an2, Smar$an2, Savr$an2, Smai$an2, Sjuin$an2, Sjuil$an2, Saou$an2, Ssept$an2, Soct$an2, Snov$an2, Sdec$an2, s.mois_cloture
					  FROM sx_saisie s, sx_dossiers d
					  WHERE s.iD = d.iD 
					  AND s.ag_".$_SESSION['millesime']." = '" . $agent. "' 
					  AND d.benef_fiscal = '".$regFiscal."' 
					  AND d.reg_fiscal_".$_SESSION['millesime']." = '".$typeFiscal."' 
					  AND s.mois_cloture = '".$moisCloture."'") or die(mysql_error());
		$saisiefait = 0;
while ($sql = mysql_fetch_assoc($mzfio)) {
	$MsClt = $moisCloture;
//		if( saisieOuPas($MsClt, 1,  $an1, $sql['Sjanv'.$an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 2,  $an1, $sql['Sfev'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 3,  $an1, $sql['Smar'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 4,  $an1, $sql['Savr'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 5,  $an1, $sql['Smai'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 6,  $an1, $sql['Sjuin'.$an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 7,  $an1, $sql['Sjuil'.$an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 8,  $an1, $sql['Saou'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 9,  $an1, $sql['Ssept'.$an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 10, $an1, $sql['Soct'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 11, $an1, $sql['Snov'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 12, $an1, $sql['Sdec'. $an1.''])) $saisiefait++;
		
		if( saisieOuPas($MsClt, 1,  $an2, $sql['Sjanv'.$an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 2,  $an2, $sql['Sfev'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 3,  $an2, $sql['Smar'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 4,  $an2, $sql['Savr'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 5,  $an2, $sql['Smai'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 6,  $an2, $sql['Sjuin'.$an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 7,  $an2, $sql['Sjuil'.$an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 8,  $an2, $sql['Saou'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 9,  $an2, $sql['Ssept'.$an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 10, $an2, $sql['Soct'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 11, $an2, $sql['Snov'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 12, $an2, $sql['Sdec'. $an2.''])) $saisiefait++;
	}
return $saisiefait;} 



//---------------------------------------------------------------------------------
//                         SYNTHESE AGENCE
//---------------------------------------------------------------------------------


function compte_dossiers($reg, $type, $mois) {
	$nb = mysql_query("SELECT ag_".$_SESSION['millesime']." FROM sx_dossiers 
								WHERE mois_cloture = '".$mois."' 
								AND benef_fiscal = '".$reg."' 
								AND reg_fiscal_".$_SESSION['millesime']." = '".$type."' ") or die(mysql_error());
	$nbr = mysql_num_rows($nb);
	return $nbr;
}

function comptesaisie_synth($moisCloture, $regFiscal, $typeFiscal, $an1, $an2) { 
$mzfio = mysql_query("SELECT Sfev$an1, Smar$an1, Savr$an1, Smai$an1, Sjuin$an1, Sjuil$an1, Saou$an1, Ssept$an1, Soct$an1, Snov$an1, Sdec$an1, Sjanv$an2, Sfev$an2, Smar$an2, Savr$an2, Smai$an2, Sjuin$an2, Sjuil$an2, Saou$an2, Ssept$an2, Soct$an2, Snov$an2, Sdec$an2, s.mois_cloture
					  FROM sx_saisie s, sx_dossiers d
					  WHERE s.iD = d.iD 
					  AND d.benef_fiscal = '".$regFiscal."' 
					  AND d.reg_fiscal_".$_SESSION['millesime']." = '".$typeFiscal."' 
					  AND s.mois_cloture = '".$moisCloture."'") or die(mysql_error());
		$saisiefait = 0;
while ($sql = mysql_fetch_assoc($mzfio)) {
	$MsClt = $moisCloture;
//		if( saisieOuPas($MsClt, 1,  $an1, $sql['Sjanv'.$an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 2,  $an1, $sql['Sfev'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 3,  $an1, $sql['Smar'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 4,  $an1, $sql['Savr'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 5,  $an1, $sql['Smai'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 6,  $an1, $sql['Sjuin'.$an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 7,  $an1, $sql['Sjuil'.$an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 8,  $an1, $sql['Saou'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 9,  $an1, $sql['Ssept'.$an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 10, $an1, $sql['Soct'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 11, $an1, $sql['Snov'. $an1.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 12, $an1, $sql['Sdec'. $an1.''])) $saisiefait++;
		
		if( saisieOuPas($MsClt, 1,  $an2, $sql['Sjanv'.$an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 2,  $an2, $sql['Sfev'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 3,  $an2, $sql['Smar'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 4,  $an2, $sql['Savr'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 5,  $an2, $sql['Smai'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 6,  $an2, $sql['Sjuin'.$an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 7,  $an2, $sql['Sjuil'.$an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 8,  $an2, $sql['Saou'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 9,  $an2, $sql['Ssept'.$an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 10, $an2, $sql['Soct'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 11, $an2, $sql['Snov'. $an2.''])) $saisiefait++;
		if( saisieOuPas($MsClt, 12, $an2, $sql['Sdec'. $an2.''])) $saisiefait++;
	}
return $saisiefait;} 



?>