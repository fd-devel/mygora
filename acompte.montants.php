<?php
////	INIT
require "commun.inc.php";
require_once PATH_INC."header.inc.php";

////	ENVOIE DU MAIL & INSERTION DANS L'HISTORIQUE
////
if(isset($_POST["mail"]))
{
	// Envoi le mail
	modif_php_ini();
	$envoi_mail = envoi_mail($_POST["destinataire"], $_POST["mail"], $_POST["contenu_mail"]);

	////	FERMETURE DU POPUP
	reload_close();
}

////	INFOS + DROIT ACCES + LOGS
$contact_tmp = objet_infos($objet["contact"], $_GET["id_contact"]);
$droit_acces = droit_acces_controler($objet["contact"], $contact_tmp, 1);
//add_logs("consult", $objet["contact"], $_GET["id_contact"]);

//// Recuparation montant tva
$montants = db_ligne("SELECT tva_a_mt_1, tva_a_mt_2, tva_a_mt_3, tva_a_mt_4 FROM gt_sx_tva_".$_SESSION['millesime']." WHERE id_contact = ".$contact_tmp['id_contact']."");

?>


<script type="text/javascript">resize_iframe_popup(600,400);</script>
<style type="text/css">
fieldset		{ background-image:url('<?php echo PATH_TPL; ?>module_contact/fond_popup.png'); background-position: right 10px bottom 10px;}
.tab_user	{ width:100%; border-spacing:3px; font-weight:bold; }
.lib_user	{ width:200px; font-weight:normal; }
</style>

<form style="padding:10px;font-weight:bold;font-size:11px;" method="post" action="#" id="form_mail" >
<fieldset style="margin-top:5px;">
<div>

<table style="width:100%;height:70px;border-spacing:8px;"><tr>
	<td style="max-width:200px;text-align:center;">
	<div style=\"font-size:14px;font-weight:bold;\">
		ACOMPTES TRIMESTRIEL TVA
	</div>
	</td></tr>
	<tr>
	<td style="text-align:left;">
		<table class="tab_user" >
<?php
		////	INFOS SUR L'ACOMPTE
		////
		$texte_mail_acompte = "<br>CECI EST UN MESSAGE GENERE AUTOMATIQUEMENT - MERCI DE NE PAS REPONDRE A CE MESSAGE<br> <br>\n";
		$texte_mail_acompte .= "Pour toute question, merci de contacter votre responsable TVA<br>\n";
		
		$texte_mail_acompte .= "Origine : CERFRANCE MEDITERRANEE<br>";
		$texte_mail_acompte .= "Agent : ".$_SESSION['user']['nom']." ".$_SESSION['user']['prenom']." <br> \n";
		$texte_mail_acompte .= "Procedure : EDI TVA DECLARATION <br><br> \n";
		
		$texte_mail_acompte .= "Destinataire : ". $contact_tmp['mail']."<br>";
		$texte_mail_acompte .= "Dossier : ".$contact_tmp['numero']." - ".$contact_tmp['civilite']." ".$contact_tmp['nom']." <br> \n";
		$texte_mail_acompte .= "Montant acompte: ".$montant;
		$texte_mail_acompte .= "<br><br>";
		$texte_mail_acompte .= "Vous avez deux semaines pour modifier votre acompte si vous le souhaiter.<br><br>";

		
		//echo'<pre>'.print_r($contact_tmp).'</pre>';
		echo "<tr><td class='txt_acces_admin'>Envoi mail d'information?</td></tr>";
		echo "<tr><td class=\"lib_user\"><table style='border:1px solid black;width:95%;margin-left:2.5%;padding-left:10px;'><tr><td> ".$texte_mail_acompte."</td></tr></table> </td></tr>";
		
		echo "<tr><td style='text-align:center;'><input type='submit' name='titre' value='". $trad["envoyer"]."' id='titre' style='width:90%;margin-top:10px;' class='lien_select'/>";
		
		//$mail = array("destinataire"=>$contact_tmp['mail'], "sujet"=>"ACOMPTE TVA", "contenu_mail"=>$texte_mail_acompte);
		echo "<input type='hidden' name='mail' value='ACOMPTE TVA' />";
		echo "<input type='hidden' name='destinataire' value='".$contact_tmp['mail']."' />";
		echo "<input type='hidden' name='contenu_mail' value='".$texte_mail_acompte."' />";
		
		echo "</td></tr>";
	
?>
		</table>
	</td>
</tr></table>


<?php
////	MODIFIER ?
/* if($droit_acces>=2)  echo "<span class=\"lien_select\" style=\"float:right;margin:10px;\" onClick=\"redir('contact_edit.php?id_contact=".$_GET["id_contact"]."');\">".$trad["envoyer"]." <img src=\"".PATH_TPL."divers/crayon.png\" /></span>"; */
?>
</form>
</div>
</fieldset>
<?php
require PATH_INC."footer.inc.php";
?>
