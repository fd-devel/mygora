<?php 
		// FONCTION COULEUR DE LA CASE
		
	function coulCase($val)
	{
		switch ($val){
		case 'D':
			$c = 'laCaseSaisieD';
			break;
		case 'S':
			$c = 'laCaseSaisieS';
			break;
		case 'X':
			$c = 'laCaseSaisieX';
			break;
		default:
			$c = 'laCaseSaisie';
			break;
		}
		return $c;
	}

		// FONCTION POUR SAVOIR SI ON AFFICHE LA CASE DU MOIS OU PAS
  function afficheCase($MoisCloture, $moisCase, $anneeCase)
  {
  	$debut = mktime(12, 0, 0, $MoisCloture, 1, $_SESSION['millesime']-1);
	$case= mktime(12, 0, 0, $moisCase, 1, $anneeCase);
	$fin = mktime(12, 0, 0, $MoisCloture, 1, $_SESSION['millesime']);
	
	if($debut < $case)
	{
		if($case <= $fin)	{	return 1;}
		else				{	return 0;}
	}
	else	{		return ;}
  }

?>