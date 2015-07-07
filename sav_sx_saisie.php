<?php
//// ENREGISTREMENT DES POST
////
if (isset($_POST['ajout'])) { //$startTime = microtime(true); // test temps d'execution
		
	// dossier courant
	$doss = 0;
	$dossiers = $deja_fait_depot = $deja_fait_saisie = $deja_fait_rendu = array();
			
    foreach ($_POST as $key => $val) {
        if ($key != 'ajout') {
            $arr = str_split(ucfirst($val));
            $val = $arr[0];
            if ($val == 'X' || $val == 'S' || $val == 'D' || $val == 'R' || $val == '') {
                list($ligne, $champ_mois) = explode("-", $key);
				
			// si nouveau dossier -> dossier courant = nouveau dossier
				if($ligne <> $doss) {
					$doss = $ligne; 
					$dossiers[$doss] = ""; 
					$deja_fait_depot[$doss] = $deja_fait_saisie[$doss] = $deja_fait_rendu[$doss] = 0;
				}
				else{ $dossiers[$doss] .= ", ";	}	// separation pour requette sql
			
				$dossiers[$doss] .=  $champ_mois . " = '" .$val. "' ";	// Sjanv = 'X'
				
                if ($val == 'D' && $_SESSION['liste_contact'][$ligne][$champ_mois] != 'D' && $deja_fait_depot[$doss] != 1) {
					$dossiers[$doss] .= ", depot_docs = now()";
					$deja_fait_depot[$doss] = 1;
                }
                if ($val == 'S' && $_SESSION['liste_contact'][$ligne][$champ_mois] != 'S' && $deja_fait_saisie[$doss] != 1) {
						$dossiers[$doss] .= ", saisie_docs = now()";
						$deja_fait_saisie[$doss] = 1;
                }
                if ($val == 'R' && $_SESSION['liste_contact'][$ligne][$champ_mois] != 'R' && $deja_fait_rendu[$doss] != 1) {
						$dossiers[$doss] .= ", rendu_docs = now()";
						$deja_fait_rendu[$doss] = 1;
                }
                
            }
        }
    }
    //    echo'<pre>'.print_r($dossiers).'</pre>';
	foreach($dossiers as $dossier => $corps_sql){
                db_query("UPDATE gt_sx_saisie SET ".$corps_sql." WHERE id_contact = '" . $dossier . "'");
	}
	//$endTime = microtime(true); // test d'execution 
	//$elapsed = $endTime - $startTime;  
	//alert("Temps d'exécution : ".$elapsed." secondes");  
}


////	MENU CHEMIN + OBJETS_DOSSIERS
////
// echo menu_chemin($objet["contact_dossier"], $_GET["id_dossier"]);
$cfg_dossiers = array("objet" => $objet["contact_dossier"], "id_objet" => $_GET["id_dossier"]);
require_once PATH_INC . "dossiers.inc.php";

// Alertes
include "alertes.php";

//// ORDRE D'AFFICHAGE DES DOSSIERS
if (isset($_GET['tri'])) {
    $tri = $_GET['tri'];
} else {
    $tri = 'clo1';
}
switch ($tri) {
    case 'clo1': $ordre = "moiscloture_" . $_SESSION['millesime'] . ", civilite, nom";
        break;
    case 'clo2': $ordre = "moiscloture_" . $_SESSION['millesime'] . " DESC,civilite, nom DESC";
        break;
    default: $ordre = "moiscloture_" . $_SESSION['millesime'] . ",civilite, nom";
        break;
    case 'num1': $ordre = "numero";
        break;
    case 'num2': $ordre = "numero DESC";
        break;
    case 'nom1': $ordre = "nom";
        break;
    case 'nom2': $ordre = "nom DESC";
        break;
}

////	LISTE DES CONTACTS
////
$liste_contacts = db_tableau("SELECT * FROM gt_contact T1, gt_sx_saisie T2
	WHERE T1.id_contact = T2.id_contact 
	AND T1.id_dossier='" . intval($_GET["id_dossier"]) . "'
	AND T1.debut_activite <= '" . $_SESSION['millesime'] . "' 
	AND T1.saisie = 'Oui'
	AND (T1.fin_activite = '' OR T1.fin_activite = '0' OR T1.fin_activite >= '" . $_SESSION['millesime'] . "')
	" . sql_affichage($objet["contact"], $_GET["id_dossier"], "T1.") . " 
	 ORDER BY " . $ordre);

if (count($liste_contacts) > 0) {

//// CHOIX D'AFFICHAGE : LE MILLESIME COURANT / TOUT
    $affich = (isset($_COOKIE['cook-affsaisie'])) ? $_COOKIE['cook-affsaisie'] : "";
	

    //// INITIALISATION CARDINALITE DOSSIER (colone de gauche)
    ////
    $cardinalite = 1;
    ?>

    <!-- INFO GAUCHE -->
    <div class="info1 div_elem_infos	 ">
        <p>
            Docs <input type="text" class="input_suivi_jaune input_X" value="D" <?php echo infobulle($trad["saisie_D"]); ?> />
            Saisi <input type="text" class="input_suivi_bleu input_X" value="S" <?php echo infobulle($trad["saisie_X"]); ?> />
        </p>
        <p>
            Revisé <input type="text" class="input_suivi_vert input_X" value="X" <?php echo infobulle($trad["saisie_X"]); ?> />
            Rendu <input type="text" class="input_suivi_brun input_X" value="R" <?php echo infobulle($trad["saisie_R"]); ?> />
        </p>
    </div>

    <!-- INFO DROITE -->
    <div class="info2 div_elem_infos	">
        <p ><a href="index.php?page_suivi=?" title="Voir les depots de documents?">A qui le tour ?</a>
        <hr/>
        Afficher<br/>
        <input type="radio" name="cook-affsaisie" <?php if ($affich == 'tout') echo'checked="cheked"'; ?> value="tout" class="chek_box_petit" onclick="document.location.href = 'commun.inc.php?cook-affsaisie=tout'" />Tout
        <input type="radio" name="cook-affsaisie" <?php if ($affich == 'mille') echo'checked="cheked"'; ?> value="mille" class="chek_box_petit" onclick="document.location.href = 'commun.inc.php?cook-affsaisie=mille'" /><?php echo $_SESSION['millesime']; ?>
    </p>
    </div>
	
    <h1 style="text-align:center;" class="suivi">La saisie</h1>

    <form id="consult" action="#" method="post" style="padding-left:8px; padding-top:10px;" >

        <!-- BOUTON D'ENREGISTREMENT -->
        <div style="text-align:center; margin-bottom:10px">
            <input type="submit" value="enregistrer" class='submit'/>
            <input name="ajout" type="hidden"/>
        </div> 

        <!--	<table id="contenu_principal_table"> -->
        <div class="div_elem_suivi_no_hover" style=" height:40px">
            <div class="div_elem_contenu">
                <table class="div_elem_table">
                    <tr >
                        <td style="width:30px;text-align:center;" ></td>
                        <td rowspan="2" style="width:70px;text-align:center; vertical-align:middle;"><a href="index.php?tri=num1"><img src="<?php echo PATH_TPL; ?>module_suivi/flecheb.png" alt="Fl bas" class="fleche" /></a> Numéro <a href="#index.php?tri=num2"><img src="<?php echo PATH_TPL; ?>module_suivi/flecheh.png" alt="Fl haut" class="fleche" /></a></td>
                        <td rowspan="2" style="text-align:center; vertical-align:middle;"><a href="index.php?tri=nom1"><img src="<?php echo PATH_TPL; ?>module_suivi/flecheb.png" alt="Fl bas" class="fleche" /></a> Nom <a href="index.php?tri=nom2"><img src="<?php echo PATH_TPL; ?>module_suivi/flecheh.png" alt="Fl haut" width="9" class="fleche" /></a></td>
                        
                        <td colspan="12"style="text-align:center;"><strong><?php echo $_SESSION['millesime'] - 1; ?></strong></td>
                        <td ><a href="index.php?tri=clo1"><img src="<?php echo PATH_TPL; ?>module_suivi/flecheb.png" alt="Fl bas" class="fleche" /></a></td>
                        <td ><a href="index.php?tri=clo2"><img src="<?php echo PATH_TPL; ?>module_suivi/flecheh.png" alt="Fl haut" class="fleche" /></a></td>
                        <td colspan="12" style="text-align:center;"><strong><?php echo $_SESSION['millesime']; ?></strong></td>
                    </tr>
                    <tr >
                        <td ></td>
                        <?php
                        for ($i = 1; $i <= 12; $i++) {
                            echo "<td class='div_elem_td' style='width:25px;text-align:center;'>" . $i . "</td>";
                        }
                        for ($i = 1; $i <= 12; $i++) {
                            echo "<td class='div_elem_td' style='width:25px;text-align:center;'>" . $i . "</td>";
                        }
                        ?> 
                    </tr>
                </table>
            </div>
        </div>

        <?php
        //echo'<pre>'.print_r($_SESSION['user']).'</pre>';
		
        ////	EN PERIODE D'ALERTE?
			$tag_alerte = date('Y') * 100 + date('m'); 

        // Alerte is : globale, peut importe la date de cloture du dossier
                        $afficher_acompte_is = 0;
        if ($_SESSION['user']['alerte_is'] == 'oui') {
            $afficher_acompte_is = periode_is();
        }

        foreach ($liste_contacts as $contact_tmp) {
		
            // Alerte tva : par dossier => TVA == ANNUELLE,  
			$afficher_acompte_tva = $afficher_tva_proceder = 0;
            if ($_SESSION['user']['alerte_tva'] == 'oui' && $contact_tmp['alerte_tva'] == 'oui' && $contact_tmp['tva_' . $_SESSION['millesime']] == 'ANNUELLE') {
			
                if ($contact_tmp['alerte_info_tva'] < $tag_alerte) {
                    $afficher_acompte_tva = periode_tva($contact_tmp['imposition_' . $_SESSION['millesime']]);
                }
                if ($contact_tmp['alerte_process_tva'] < $tag_alerte) {
                    $afficher_tva_proceder = periode_tva_proceder($contact_tmp['imposition_' . $_SESSION['millesime']]);
                }
            }

            //echo'<pre>' . print_r($contact_tmp) . '</pre>';
            ////	LIEN POPUP DEMAT ARTIC 
			if($contact_tmp["anciennumero"] <> ""){
            $lien_popup_demat = "class='div_elem_td txt_acces_user' onclick=\"popup('demat.php?dossier=" . $contact_tmp["anciennumero"] . "','aff_contact" . $contact_tmp["id_contact"] . "', 800, 600);\"";
			}else{$lien_popup_demat = "class='div_elem_td'";}			
			
            ////	LIEN POPUP DOSSIER
            $lien_popup = "";
			//"onclick=\"popup('../module_contact/contact.php?id_contact=" . $contact_tmp["id_contact"] . "','aff_contact" . $contact_tmp["id_contact"] . "');\"";

            //// 	NUMERO
            $contact_numero = $contact_tmp["numero"];

            ////	CIVILITE, NOM
            $contact_pave_ident_info =  $contact_tmp["civilite"] . " " . $contact_tmp["nom"] . " " .$contact_tmp["prenom"];
			if($_SESSION['cfg']['resolution_width'] < 1200){ $contact_pave_ident_info = text_reduit($contact_pave_ident_info, 23); }

            ////	ALERTES MAIL :  ACOMPTES TVA / ACOMPTES IS / DOCUMENTS
            $alerte_info = "";
            // Acompte tva - mail d'info
            if ($afficher_acompte_tva) {
                $alerte_info .= alerte_acompte_tva($contact_tmp["id_contact"],$contact_tmp["moiscloture_".$_SESSION['millesime']], $contact_tmp["tvaexercice_".$_SESSION['millesime']]);
            }
            // Acompte tva - envoi de l'acompte
            if ($afficher_tva_proceder) {
                $alerte_info .= alerte_acompte_tva_proceder($contact_tmp["id_contact"],$contact_tmp["moiscloture_".$_SESSION['millesime']], $contact_tmp["tvaexercice_".$_SESSION['millesime']]);
            }
            // Acompte IS - mail d'info
            if ($afficher_acompte_is && $contact_tmp['alerte_is'] == 'oui' && $contact_tmp["alerte_info_is"] < $tag_alerte) {
              $alerte_info .= alerte_acompte_is($contact_tmp["id_contact"]);
            }
              // Demande de documents - mail d'info
            if ($_SESSION['user']['alerte_docs'] == 'oui' && $contact_tmp["alerte_info_docs"] < $tag_alerte && periode_demande_documents($contact_tmp["alerte_docs"])) {
              $alerte_info .= alerte_demande_doc($contact_tmp["id_contact"]);
            }

            ////	DETAILS D'UN CONTACT
            $details_contact = "";

            $cases = "";
            $label_mois = array("Sjanv", "Sfev", "Smar", "Savr", "Smai", "Sjuin", "Sjuil", "Saou", "Ssept", "Soct", "Snov", "Sdec");
            $input_an = array($_SESSION["millesime"] - 1, $_SESSION["millesime"]);

            foreach ($input_an as $an) {
                foreach ($label_mois as $key => $mois) {

                    $cases .= "<td class='div_elem_td' style='width:25px;text-align:center;'>";
                    // affichage de l'input suivant le choix de l'utilisateur (cook-affsaisie) 
                    if (afficheCase($contact_tmp["moiscloture_" . $_SESSION['millesime'] . ""], $key, $an)) {
                        $name = $contact_tmp["id_contact"];
                        $champ = $mois . $an; //alert($champ);
                        $val = $contact_tmp[$champ];
                        $classCase = coulCase($val);
                        $cases .= "<input class='" . $classCase . " input_X' name='" . $name . "-" . $champ . "' maxlength='1' value='" . $val . "'/>";
						
						$_SESSION['liste_contact'][$name][$champ] = $val;
                    }
                    $cases .= "</td>";
                }
            }

            $details_contact .= $cases;

			// MENU CONTEXTUEL
			menu_contextuel_suivi($objet["contact"], $contact_tmp, 1);
		
            ////	CONTENU
            echo "<div class='div_elem_contenu'>";
            echo "<table class='div_elem_table'><tr>";

            ////	AFFICHAGE LISTE
            echo "<td class='div_elem_td' style='width:30px;text-align:center;' >" . $cardinalite . "</td>";
            echo "<td style='width:7%;text-align:center;' " . $lien_popup_demat . " >" . $contact_numero . "</td>";
            echo "<td class='div_elem_td' style='padding-left:20px;' ><span " . $lien_popup . ">" . $contact_pave_ident_info . "</span></td>";
            echo "<td class='div_elem_td'>" . $alerte_info . "</td>";
            echo $details_contact ;

            echo "</tr></table>";
            echo "</div>";
            echo "</div>";

            //// NUMEROTATION LIGNES DE DOSSIERS + SAUT DE LIGNE TOUTES LES 10
            $cardinalite++;
            if (($cardinalite % 10) == 1) {
                echo "<div class='div_elem_suivi_no_hover' style=' height:20px;'><table class='div_elem_table'><tr >";
                echo "<td style='width:70px;'></td>";
                echo "<td style='width:70px;'></td>";
                echo "<td ></td><td ></td>";

                for ($i = 1; $i <= 12; $i++) {
                    echo "<td class='div_elem_td' style='width:25px;text-align:center;'>" . $i . "</td>";
                }
                for ($i = 1; $i <= 12; $i++) {
                    echo "<td class='div_elem_td' style='width:25px;text-align:center;'>" . $i . "</td>";
                }
                echo "</tr></table></div>";
            }
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
if (@$cpt_div_element < 1)
    echo "<div class='div_elem_aucun' style ='margin-top:50px;'>" . $trad["CONTACT_aucun_contact"] . "</div>";
?>
