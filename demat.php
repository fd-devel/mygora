<?php
////	INIT
require "commun.inc.php";
require_once PATH_INC."header.inc.php";

if ((isset ($_POST['dossier']) AND !empty($_POST['dossier'])) or (isset ($_GET['dossier']) AND !empty($_GET['dossier']))){


//if (isset ($_POST['dossier']) AND !empty($_POST['dossier']) AND is_numeric($_POST['dossier'])){

if (isset ($_POST['dossier']))
{
	$demat = htmlspecialchars($_POST['dossier']);
}
elseif (isset ($_GET['dossier']))
{
	$demat = htmlspecialchars($_GET['dossier']);
}
	
//	header ( 'Location: http://10.200.1.11/'.$demat.'/' );

function get_text($filename)
{
	$fp_load = fopen("$filename", "rb");
	
	if ( $fp_load )
	{
		$content="";
		while ( !feof($fp_load) )
		{
			$content .= fgets($fp_load, 8192);
		}
	
		fclose($fp_load);
		return $content;
	}
}
function getInfos($url){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_exec($ch);
    return curl_getinfo($ch);
}
 
$matches = array();
$site = 'http://10.100.1.59/DEMAT11';
//$site = 'http://localhost/demat11';
$dir = $demat;

$docs_presents = 0;
$infos = getInfos($site .'/'. $demat.'/');
if($infos['http_code'] == 200){		$docs_presents = 1;}

	@preg_match_all("/(a href\=\")([^\?\"]*)(\")/i", @get_text($site .'/'. $demat.'/'), $matches);
//echo '<pre>'.print_r($matches[2]).'</pre>';

}
?>

<!--<script type="text/javascript">resize_iframe_popup(800,400);</script> -->
<style type="text/css">
fieldset		{ background-image:url('<?php echo PATH_TPL; ?>module_contact/fond_popup.png'); background-position: right 10px bottom 10px;}
.tab_user	{ width:100%; border-spacing:3px; font-weight:bold; }
.lib_user	{ width:200px; font-weight:normal; }
</style>

<fieldset style="margin-top:5px;">
<div>

<table style="width:100%;height:70px;border-spacing:8px;"><tr>
	<td style="max-width:200px;text-align:center;">
	<div style=\"font-size:14px;font-weight:bold;\">
		Editions Dématérialisées
	</div>
	</td></tr>
	<tr>
	<td style="text-align:left;">
		<table class="tab_user" >

<?php

	echo "<p style='margin-left: 50px; font-size:x-large'>Liste des documents diponibles pour ".$demat." : </p>\n\n";

if($docs_presents) {

// triage tableau
sort($matches[2]);
//echo '<pre>'.print_r($matches[2]).'</pre>';

$i = 0;

foreach($matches[2] as $match)
{
if($match != "/demat11/"){

	$chem[$i] = $match;

		  // numero       $annee         document   
	list($ligne[$i][0], $ligne[$i][1], $ligne[$i][2]) = explode("-", $match);
	$an[$i] = substr($ligne[$i][1], 0, 4);   //année
	$mod[$i] = substr($ligne[$i][2], 0, 2);  //module
	list($solde, $extension[$i]) = explode(".", $ligne[$i][2]); 
	//echo $extension[$i]."<br/>";
	$i++;
}}

// nb de fichiers disponibles
$nb = $i;

asort($mod);

$mille = "";
$modul= "";
for ($j=$i-1 ; $j>=0 ; $j--){

	if($an[$j] != $mille){
		if($mille != ""){echo "</ul>";}
		$mille = $an[$j];
		echo '<p style=\'margin-left: 100px; font-size:x-large; color:red\'><strong>'.$an[$j].'</strong></p>';
					
		echo "<ul style='margin-left: 150px'>";
	}
	if($mod[$j] != $modul){
		$modul = $mod[$j];
			switch ($modul){
				case "LF":
					$titre_mod = "Liasse fiscale";
					break;
				case "JT":
					$titre_mod = "TVA";
					break;
				case "AD":
					$titre_mod = "TVA";
					break;
				case "VS":
					$titre_mod = "Visa";
					break;
				case "IM":
					$titre_mod = "Immobilisations";
					break;
				case "EM":
					$titre_mod = "Emprunts";
					break;
				case "DA":
					$titre_mod = "DAR";
					break;
				case "CO":
					$titre_mod = "Comptabilité";
					break;
				case "MS":
					$titre_mod = "MSA";
					break;
				case "EC":
					$titre_mod = "ECV";
					break;
				case "DO":
					$titre_mod = "Dossiers";
					break;
				case "CA":
					$titre_mod = "Analytique";
					break;
				case "JA":
					$titre_mod = "J.A.";
					break;
				case "CG":
					$titre_mod = "C.G.A.";
					break;
			}
			echo '<p style=\'margin-left: 1px; font-size:large; color:red\'><strong>'.$titre_mod.'</strong></p>';
		}
	
	
// logo suivant extention  pdf, zip, doc ...
	switch ($extension[$j]){
		case "PDF":
			$image = "pdf.jpg";
			break;
		case "ZIP":
			$image = "zip.jpg";
			break;
		case "RAR":
			$image = "zip.jpg";
			break;
		case "DOCX":
			$image = "docx.jpg";
			break;
		case "DOC":
			$image = "doc.jpg";
			break;
		case "XLS":
			$image = "xls.jpg";
			break;
		case "XLSX":
			$image = "xls.jpg";
			break;
		case "JPG":
			$image = "image.jpg";
			break;
		case "JPEG":
			$image = "image.jpg";
			break;
		default:
			$image = "pdf.jpg";
			break;
	}

	echo "<li style='list-style-image: URL($image)'><a href=\"$site/$demat/$chem[$j] \">$chem[$j]</a></li>\n";

}
echo "</ul>";

}

?>

		</table>
	</td>
</tr></table>

</div>
</fieldset>
<?php
require PATH_INC."footer.inc.php";
?>
