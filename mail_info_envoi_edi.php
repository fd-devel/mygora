<?php
////	INIT
require "commun.inc.php";
require_once PATH_INC."header.inc.php";
////	SI BOUTON OK : NE PLUS AFFICHER L'ALERTE
////
if(isset($_POST["generer"]))
{

//require_once PATH_INC."header.inc.php";

////	INFOS + DROIT ACCES + LOGS
$contact_tmp = objet_infos($objet["contact"], $_POST["id_contact"]);
$droit_acces = droit_acces_controler($objet["contact"], $contact_tmp, 1);
add_logs("ajout", $objet["contact"], $_POST["id_contact"], "Envoi mail d'information EDI");

	// A PAYER / CREDIT   //  MONTANT
		$montant = $sens = "";
		$txt_sens = "Le montant à payer sera prélevé automatiquement";
	
	if($_POST["generer"] == 'tva'){
		if($_POST['sens'] == 'sens_1'){
			$sens = "A PAYER"; 
			$montant = $_POST['montant_1'] . " €";
		}
		else{
			$sens = "CREDIT ";
			if($_POST['montant_1'] <> "" ){ 	// si credit a rembourser
				if($_POST['montant_2'] <> "" ){	// et si credit a reporter
					$montant = $_POST['montant_1']." € à rembourser et ". $_POST['montant_2'] ." € à reporter";
					$txt_sens = "Le montant à rembourser devrai l'être d'ici une à deux semaines et le montant à reporter le sera sur la prochaine période de tva.";
				}else{							// ou si pas a reporter (que a rembourser)
					$montant = $_POST['montant_1']." € de remboursement.";
					$txt_sens = "Votre crédit devrai vous être remboursé d'ici une à deux semaines";
				}
			}else{								// sinon que a reporter
				$montant = $_POST['montant_2'] ." € à reporter";
				$txt_sens = "Votre crédit est à reporter pour la période tva suivante";
			}
		}
	}
	elseif($_POST["generer"] == 'is'){
			$sens = "A PAYER"; 
			$montant = $_POST['montant_1'] . " €";
	}
		
		////	DECLARATION EN PIECE JOINTE
		$txt_pj = "Vous pouvez retrouver votre déclaration sur votre bureau en ligne.";
		if(isset($_POST["piece_jointe"]) && $_POST["piece_jointe"] == "oui"){
			$txt_pj .= " Vous la trouverez également en pièce jointe à cet e-mail."; 
		}
		
		
		////	TEXTE MAIL
		
		//	LIGNES COMMUNES
		$txt_L3 = "Origine : CERFRANCE MÉDITERRANÉE";
		$txt_L4 = "Agent : ".$_SESSION['user']['nom']." ".$_SESSION['user']['prenom']."  ";
		$txt_L6 = "Destinataire : ". $contact_tmp['mail']."";
		$txt_L7 = "Dossier : ".$contact_tmp['numero']." - ".$contact_tmp['civilite']." ".$contact_tmp['nom']."  ";
		$txt_L8 = "Déclaration ".$sens;
		$txt_L9 = "Montant : ".$montant;
		$txt_L10 = "Madame, Monsieur,";
		$txt_L12 = $txt_sens ;
		$txt_L13 = $txt_pj;
		$txt_L1 = $txt_L2 = $txt_L5 = $txt_L11 = "";
		
		//	LIGNES TVA
		if($_POST["generer"] == 'tva')
		{
		$txt_L1 = "VOTRE DECLARATION TVA ".$contact_tmp["tva_".$_SESSION['millesime']]."";
		$txt_L2 = "Pour toute question, merci de contacter votre responsable TVA";
		$txt_L5 = "Procédure : EDI TVA DÉCLARATION   ";
		$txt_L11 = "Votre déclaration de TVA à été envoyée par EDI à votre centre des impôts.";
		}
		//	LIGNES IS
		if($_POST["generer"] == 'is')
		{
		$txt_L1 = "VOTRE ". ($_POST['sens'] == "sens_1" ? "ACOMPTE " : "SOLDE ") . "D'IMPOT SOCIETE.";
		$txt_L2 = "Pour toute question, merci de contacter votre responsable IS";
		$txt_L5 = "Procédure : EDI DÉCLARATION ". ($_POST['sens'] == "sens_1" ? "ACOMPTE " : "SOLDE "). "IS";
		$txt_L11 = "Votre déclaration ". ($_POST['sens'] == "sens_1" ? "d'acompte" : "de solde") . " d'IS à été envoyée par EDI à votre centre des impôts.";
		}
		
		////	MAIL OU MAILTO
		$separ = ["<br>", "%0A"];
		$chx_mail = ["texte_mail", "texte_mailto"];
		$texte_mail = $texte_mailto = "";
		
		for($i=0; $i<=1; $i++){		
			$$chx_mail[$i] = $txt_L10 . $separ[$i];
			$$chx_mail[$i] .= $txt_L11 . $separ[$i];

			$$chx_mail[$i] .= $separ[$i] . $txt_L1 . $separ[$i];
			$$chx_mail[$i] .= $txt_L2 . $separ[$i] . $separ[$i];
			
			$$chx_mail[$i] .= $txt_L3 . $separ[$i];
			$$chx_mail[$i] .= $txt_L4 . $separ[$i];
			$$chx_mail[$i] .= $txt_L5 . $separ[$i] . $separ[$i];
			
			$$chx_mail[$i] .= $txt_L6 . $separ[$i];
			$$chx_mail[$i] .= $txt_L7 . $separ[$i] . $separ[$i];
		
			$$chx_mail[$i] .= $txt_L8 . $separ[$i];
			$$chx_mail[$i] .= $txt_L9 . $separ[$i] . $separ[$i];

			$$chx_mail[$i] .= $txt_L12 . $separ[$i] . $separ[$i];			
			
			$$chx_mail[$i] .= $txt_L13 . $separ[$i] . $separ[$i];
		}
		

?>

<script type="text/javascript">resize_iframe_popup(600,500);</script>

<style type="text/css">
fieldset		{ background-image:url('<?php echo PATH_TPL; ?>module_contact/fond_popup.png'); background-position: right 10px bottom 10px;}
.tab_user	{ width:100%; border-spacing:3px; font-weight:bold; }
.lib_user	{ width:200px; font-weight:normal; }
</style>

<form style="padding:10px;font-weight:bold;font-size:11px;" method="post" action="#" id="form_mail" >
<fieldset style="margin-top:25px;">
<legend>Information EDI</legend>


<table style="width:100%;height:70px;">
	<tr>
		<td style="text-align:center;">
			<div style="font-size:14px;font-weight:bold;">
				IMFORMATION EDI
			</div>
		</td>
	</tr>
	<tr>
		<td class="txt_acces_admin">

				<?php
				if($_SESSION['cfg']['navigateur'] == 'undefined' || $_SESSION['cfg']['navigateur'] == 'ie'){
				?>
				<span class="lien_select" style="float:right;margin:10px;" >Texte à copier <img src="<?php echo PATH_TPL ;?>divers/crayon.png" />
				</span> 

				<?php
				}else{
				?>

			<a href="mailto:<?php echo $contact_tmp['mail']."?cc=".$_SESSION['user']['mail']; ?>&Subject=CERFRANCE - déclaration EDI&body=<?php echo $texte_mailto; ?>">
				<span class="lien_select" style="float:right;margin:10px;" >Générer le mail <img src="<?php echo PATH_TPL ;?>divers/crayon.png" />
				</span>
			</a>
				<?php
				}
				?>
		</td>
	</tr>
	<tr>
		<td  class="lib_user">
			<table style="border:1px solid black;width:95%;margin-left:2.5%;padding-left:10px;font-weight:bold;">
				<tr>
					<td style=""> <?php echo $texte_mail ; ?></td>
				</tr>
			</table> 
		</td>
	</tr>
</table>

</form>
</fieldset>
<?php
require PATH_INC."footer.inc.php";
}

//// INFORMATIONS SUR LA DECLARATION ENVOYEE POUR GENERER LE MAIL RENSEIGNé
else{

	////	SELECTION DE LA DECLARATION ENVOYEE
	if(isset($_GET['declaration']) && ($_GET['declaration'] == 'tva' || $_GET['declaration'] == 'is')){
	
		// VARIABLES DE REMPLISSAGE 
		$legend = $titre = "";
		$lbl_radio_1 = $lbl_radio_2 = "";
		$lbl_input_txt_montant_1 = $lbl_input_txt_montant_2 = "";
		
		// EDI TVA
		if($_GET['declaration'] == 'tva'){
			$legend = "Information TVA";	$titre = " Informations sur la Déclaration ";
			$lbl_radio_1 = "A payer";		$lbl_radio_2 = "Crédit";
			$lbl_input_txt_montant_1 = "MONTANT";
			$lbl_input_txt_montant_2 = "A REMBOURSER";
			$lbl_input_txt_montant_1_2 = "A REPORTER";
		?>
		<script type="text/javascript">
		////	SELECTION DE "DEBUT ACTIVITE"
		function changement_credit(click)
		{
		if(click == "2"){	// Si clic credit
			if(document.getElementById("montant_2").style.display == "none" ) 	// si pas deja apparant
				{ 	document.getElementById("montant_2").style.display = "inline";
					document.getElementById("txt_montant_2").style.display = "inline";
					document.getElementById("txt_montant_1").style.display = "none";
					}	
		}
		else{
			if(document.getElementById("montant_2").style.display == "inline" )
				{ 	document.getElementById("montant_2").style.display = "none";
					document.getElementById("txt_montant_2").style.display = "none";
					document.getElementById("txt_montant_1").style.display = "inline";
				}
		}
			
		}
		</script>
		<?php
		}
		elseif($_GET['declaration'] == 'is'){
			$legend = "Information IS";	$titre = " Informations sur la Déclaration ";
			$lbl_radio_1 = "Acompte";		$lbl_radio_2 = "Solde";
			$lbl_input_txt_montant_1 = "MONTANT";
		}

?>


<form style="padding:10px;font-weight:bold;font-size:11px;" method="post" action="#" id="form_mail" >
<fieldset style="margin-top:15px;">
<legend><?php echo $legend; ?></legend>
<table style="width:100%;height:70px;border-spacing:8px;">
	<tr>
		<td >
			<div style="text-align:center;"> <?php echo $titre; ?> </div>
		</td>
	</tr>
	<tr>
		<td style="text-align:left;">
			
			<div class='div_config_suivi'>
				<input type='radio' style='padding-left:20px;margin-left:20px;' id='sens_1' name='sens' value='sens_1' checked='checked' onClick='changement_credit(1);' /> <label for='sens_1'> <?php echo $lbl_radio_1; ?> </label>
				<input type='radio' style='padding-left:20px;margin-left:20px;' id='sens_2' name='sens' value='sens_2' onClick='changement_credit(2);' /> <label for='sens_2'> <?php echo $lbl_radio_2; ?> </label>
				<input type='hidden' value='1' id='clic' />
			</div>
			
			<div class='div_config_suivi'>
				<span id='montant_1'>
					<span id='txt_montant_1' style='display:inline'><?php echo $lbl_input_txt_montant_1; ?> </span>
					<span id='txt_montant_2' style='display:none'><?php echo $lbl_input_txt_montant_2; ?> </span>
					<input style='padding-left:10px;margin-right:40px;width:60px;' name='montant_1' value=''/>
				</span>
				<span id='montant_2' style='display:none'> <?php echo $lbl_input_txt_montant_1_2; ?>
					<input style='padding-left:10px;margin-right:20px;width:60px;' name='montant_2' value=''/>
				</span>
			</div>
			
			<div class='div_config_suivi'> Déclaration en piéce jointe
				<input type='radio' style='padding-left:20px;margin-left:20px;' id='piece_jointe_o' name='piece_jointe' value='oui' /><label for='piece_jointe_o'> Oui</label>
				<input type='radio' style='padding-left:20px;margin-left:20px;' id='piece_jointe_n' name='piece_jointe' value='non' checked='checked' /><label for='piece_jointe_n'> Non</label>
			</div>
		</td>
	</tr>
	<tr>
		<td style="text-align:left;">
		</td>
	</tr>
	<tr>
		<td style="text-align:center;">
			<input type="submit" name="titre" value="Générer le texte" id="titre" style="width:90%;margin-top:10px;" class="lien_select"/>
			<input type="hidden" name="generer" value="<?php echo $_GET['declaration'] ?>" />
			<input type="hidden" name="id_contact" value="<?php echo $_GET["id_contact"]; ?>" />
		</td>
	</tr>
</table>

</form>

<?php
}
}
?>
