<?php
////	INIT
require "commun.inc.php";

////	SI BOUTON OK : NE PLUS AFFICHER L'ALERTE
////
if(isset($_POST["fait"]))
{	$tag_alerte = date('Y') * 100 + date('m');
	db_query("UPDATE gt_sx_saisie SET alerte_info_is ='".$tag_alerte."' WHERE id_contact='".$_POST['id_contact']."'");

	////	FERMETURE DU POPUP
	reload_close();
}

require_once PATH_INC."header.inc.php";

////	INFOS + DROIT ACCES + LOGS
$contact_tmp = objet_infos($objet["contact"], $_GET["id_contact"]);
$droit_acces = droit_acces_controler($objet["contact"], $contact_tmp, 1);
//add_logs("consult", $objet["contact"], $_GET["id_contact"]);


		////	TEXTE MAIL auto
		////
		$texte_mail = "Bonjour,<br>";
		$texte_mail .= "Votre acompte Impôt Société va être envoyé a votre centre des impôts pour le 15 du mois.<br><br>";
		$texte_mail .= "<br>CE MAIL EST GÉNÉRÉ AUTOMATIQUEMENT :<br> <br>";
		$texte_mail .= "<br>ACOMPTE IMPOTS SOCIETE  <br>";
		$texte_mail .= "Pour toute question, merci de contacter votre responsable IS<br>";
		
		$texte_mail .= "Origine : CERFRANCE MEDITERRANEE<br>";
		$texte_mail .= "Agent : ".$_SESSION['user']['nom']." ".$_SESSION['user']['prenom']." <br> ";
		$texte_mail .= "Procedure : ACOMPTE IS <br><br> ";
		
		$texte_mail .= "Destinataire : ". $contact_tmp['mail']."<br>";
		$texte_mail .= "Dossier : ".$contact_tmp['numero']." - ".$contact_tmp['civilite']." ".$contact_tmp['nom']." <br> ";
		$texte_mail .= "<br><br>";
		
		////	TEXTE MAIL mailto
		////
		$texte_mailto = "Bonjour,%0A";
		$texte_mailto .= "Votre acompte Impôt Société va être envoyé a votre centre des impôts pour le 15 du mois.%0A%0A";
		$texte_mailto .= "%0A CE MAIL EST GÉNÉRÉ AUTOMATIQUEMENT : %0A %0A";
		$texte_mailto .= "%0A ACOMPTE IMPOTS SOCIETE  %0A";
		$texte_mailto .= "Pour toute question, merci de contacter votre responsable IS%0A";
		
		$texte_mailto .= "Origine : CERFRANCE MEDITERRANEE%0A";
		$texte_mailto .= "Agent : ".$_SESSION['user']['nom']." ".$_SESSION['user']['prenom']." %0A ";
		$texte_mailto .= "Procedure : ACOMPTE IS %0A%0A ";
		
		$texte_mailto .= "Destinataire : ". $contact_tmp['mail']."%0A";
		$texte_mailto .= "Dossier : ".$contact_tmp['numero']." - ".$contact_tmp['civilite']." ".$contact_tmp['nom']." %0A ";
		$texte_mailto .= "%0A%0A";



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
		ACOMPTE IS
	</div>
	</td></tr>
	<tr>
	<td style="text-align:left;">
		<table class="tab_user" >

		<tr>
			<td class='txt_acces_admin'>Envoi mail d'information?<a href="mailto:<?php echo $contact_tmp['mail']."?cc=".$_SESSION['user']['mail']; ?>&Subject=CERFRANCE Acompte IS&body=<?php echo $texte_mailto; ?>"><span class="lien_select" style="float:right;margin:10px;" >Envoyer<img src="<?php echo PATH_TPL ;?>divers/crayon.png" /></span></a></td>
		</tr>
		<tr><td  class=\"lib_user\">
			<table style='border:1px solid black;width:95%;margin-left:2.5%;padding-left:10px;'>
			<tr>
				<td> <?php echo $texte_mail ; ?></td>
			</tr></table> 
			</td>
		</tr>
		
		<tr><td style='text-align:center;'><input type='submit' name='titre' value='OK : ne plus me prévenir.' id='titre' style='width:90%;margin-top:10px;' class='lien_select'/>
		
		<input type='hidden' name='fait' value='DDe Docs' />
		<input type='hidden' name='id_contact' value='<?php echo $contact_tmp['id_contact']; ?>' />
		
		<tr><td style='text-align:center;'><input type='submit' name='titre' value='Non : garder l alerte active' id='titre' style='width:90%;margin-top:10px;' class='lien_select' onclick='window.close()'/>
		
		</table>
	</td>
</tr></table>

</form>
</div>
</fieldset>
<?php
require PATH_INC."footer.inc.php";
?>
