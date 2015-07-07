<?php
////	INIT
require "commun.inc.php";

////	SI BOUTON OK : NE PLUS AFFICHER L'ALERTE
////
if(isset($_POST["fait"]))
{	$tag_alerte = date('Y') * 100 + date('m');
	db_query("UPDATE gt_sx_saisie SET alerte_process_tva ='".$tag_alerte."' WHERE id_contact='".$_POST['id_contact']."'");

	////	FERMETURE DU POPUP
	reload_close();
}

require_once PATH_INC."header.inc.php";

////	INFOS + DROIT ACCES + LOGS
$contact_tmp = objet_infos($objet["contact"], $_GET["id_contact"]);
$droit_acces = droit_acces_controler($objet["contact"], $contact_tmp, 1);
//add_logs("consult", $objet["contact"], $_GET["id_contact"]);

	////	INFOS SUR L'ACOMPTE
	////
	$texte_acompte = "<br>L'acompte trimestriel est-il envoyé?<br> <br>\n";
	$texte_acompte .= "Dossier : ".$contact_tmp['numero']." - ".$contact_tmp['civilite']." ".$contact_tmp['nom']." <br> \n";
	$texte_acompte .= "<br><br>";
	
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
		ENVOI DE L'ACOMPTE TRIMESTRIEL TVA
	</div>
	</td></tr>
	<tr>
	<td style="text-align:left;">
		<table class="tab_user" >

		<tr><td class='txt_acces_admin'>Envoi Acompte TVA trimestriel?</td></tr>
		<tr><td class=\"lib_user\"><table style='border:1px solid black;width:95%;margin-left:2.5%;padding-left:10px;'><tr><td><?php echo $texte_acompte; ?></td></tr></table> </td></tr>
		
		<tr><td style='text-align:center;'><input type='submit' name='titre' value='Oui : ne plus me prévenir.' id='titre' style='width:90%;margin-top:10px;' class='lien_select'/>
		
		<input type='hidden' name='fait' value='EDI TVA' />
		<input type='hidden' name='id_contact' value='<?php echo $contact_tmp['id_contact']; ?>' />
		
		<tr><td style='text-align:center;'><input type='submit' name='titre' value='Non : garder l alerte active' id='titre' style='width:90%;margin-top:10px;' class='lien_select' onclick='window.close()'/>
		
		</td></tr>	
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
