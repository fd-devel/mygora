<?php
////	INIT
define("NO_MODULE_CONTROL",true);
require "commun.inc.php";
require_once PATH_INC."header.inc.php";


////	ENVOIE DU MAIL & INSERTION DANS L'HISTORIQUE
////
if(isset($_POST["titre"]))
{
	// Envoi le mail
	modif_php_ini();
	$options["envoi_fichiers"] = true;
	$options["header_footer"] = false;
	$options["afficher_dest_message"] = true;
	$envoi_mail = envoi_mail($_POST["mails"], magicquotes_strip($_POST["titre"]), magicquotes_strip($_POST["description"]), $options);
	// Ajoute dans l'historique
	$destinataires = "";
	foreach($_POST["mails"] as $mail)	{ $destinataires .= $mail.", "; }
	if($envoi_mail==true)	db_query("INSERT INTO gt_historique_mails SET destinataires=".db_format(substr($destinataires,0,-2)).", titre=".db_format($_POST["titre"]).", description=".db_format($_POST["description"]).", date_crea='".db_insert_date()."', id_utilisateur='".$_SESSION["user"]["id_utilisateur"]."'");
	
	////	FERMETURE DU POPUP
	reload_close();
}
?>

<script type="text/javascript">resize_iframe_popup(800,500);</script>

<script type="text/javascript">

////    On contrôle les champs
function controle_formulaire()
{
	// Il doit y avoir un titre
	if (get_value("titre").length==0 || get_value("titre")=="<?php echo $trad["MAIL_titre"]; ?>")	{ alert("<?php echo $trad["specifier_titre"]; ?>"); return false; }
}
</script>

<form action="<?php echo php_self(); ?>" method="POST" id="form_mail" enctype="multipart/form-data" OnSubmit="return controle_formulaire();" >
	<table id="contenu_principal_table"><tr>
		<td>
			<div class="content">
				<?php 	echo "<fieldset style='text-align:center;'>";
		echo $trad["titre"]." &nbsp;<input type='text' name='titre' id='titre' value='Demande de docments' style='width:65%' /> &nbsp; &nbsp; ";
		echo "<span onClick=\"afficher_dynamic('block_description');afficher_tinymce();\" class='lien'>".$trad["description"]." <img src=\"".PATH_TPL."divers/derouler.png\" /></span>";
		echo "<span id='block_description'><br><br><textarea name='description' id='description' class='tinymce_textarea'>".urldecode($_GET['mail'])."</textarea></span>";
		init_editeur_tinymce("description","block_description");
	echo "</fieldset>";				?>
					
				</div>
				<table style="width:100%;margin-top:10px;">
					<tr>
						<td>
							<?php
							////	PAS DE HEADER-FOOTER / AFFICHER LES DESTINATAIRES DANS LE MESSAGE / ACCUSE DE RECEPTION
			/* 				pref_user("MAIL_afficher_dest_message","afficher_dest_message");
							echo "<div ".infobulle($trad["MAIL_no_header_footer_infos"])."><input type='checkbox' name='no_header_footer' value='1' id='box_no_header_footer' onClick=\"checkbox_text(this);\" /><span id='txt_no_header_footer' class='lien' onClick=\"checkbox_text(this);\">".$trad["MAIL_no_header_footer"]."</span></div>";
							echo "<div ".infobulle($trad["MAIL_afficher_destinataires_message_infos"])."><input type='checkbox' name='afficher_dest_message' value='1' id='box_afficher_dest_message' onClick=\"checkbox_text(this);\" ".(@$_REQUEST["afficher_dest_message"]>0?"checked":"")." /><span id='txt_afficher_dest_message' class='".(@$_REQUEST["afficher_dest_message"]>0?"lien_select":"lien")."' onClick=\"checkbox_text(this);\">".$trad["MAIL_afficher_destinataires_message"]."</span></div>";
			 				if($_SESSION["user"]["mail"]!="")	echo "<div ".infobulle($trad["MAIL_accuse_reception_infos"])."><input type='checkbox' name='accuse_reception' value='1' id='box_accuse_reception' onClick=\"checkbox_text(this);\" /><span id='txt_accuse_reception' class='lien' onClick=\"checkbox_text(this);\">".$trad["MAIL_accuse_reception"]."</span></div>";
			  */				?>
						</td>
						<td style="text-align:right;">
							<?php
							/* for($i=1; $i<=10; $i++){
								echo "<div id='div_fichier".$i."' ".($i>1?"class='cacher'":"")." ".infobulle(libelle_upload_max_filesize()).">".$trad["MAIL_fichier_joint"]." &nbsp;<input type='file' name='fichier".$i."' onChange=\"if(this.value!='') afficher_dynamic('div_fichier".($i+1)."',true);\" /></div>";
							} */
							?>
						</td>
					</tr>
					<tr><td colspan="2" style="text-align:center;margin-top:20px;">
						<input type="submit" value="<?php echo $trad["envoyer"]; ?>" class="button_big" style="width:200px;" />
						<input type="hidden" value="<?php echo $_GET["adresse"]; ?>" name ="mails[]" />
						<input type="hidden" value="<?php echo $_SESSION['user']['mail']; ?>" name ="mails[]" />
					</td></tr>
				</table>
			</div>
		</td>
	</tr></table>
</form>


<?php require PATH_INC."footer.inc.php"; ?>