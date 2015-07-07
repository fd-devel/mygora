
<table class="noprint navigation_suivi <?php echo $_SESSION['couleur']; ?>"><td>
<!--<div id="navigation_suivi" > -->
	<!-- gauche -->
	<div class="navigation_suivi_gauche">
		<form id="menu2" method="post" action="index.php">
			<li>
				<input type="submit" name="page_suivi" value="Saisie" />
			</li>
			<li>
				<input type="submit" name="page_suivi" value="TVA" />
			</li>
			<li>
				<input type="submit" name="page_suivi" value="Cloture" />
			</li>
			<li>
				<input type="submit" name="page_suivi" value="Statistiques" />
			</li>
		</form>
	</div>
	<!-- droite -->
	<div class="navigation_suivi_droite">
		<li>
			<form id="anmoins" method="post" action="#">
				<input type="hidden" name="page_suivi" value="<?php echo PAGE_SUIVI; ?>">
				<input type="hidden" name="anmoins" />
				<input type="submit" value="&lt;&lt;" />
			</form>
		</li>
		<li>
			<div style="font-size:1.5em"><?php echo $_SESSION['millesime'];?> </div>
		</li>
		<li>
			<form id="anplus" method="post" action="#">
				<input type="hidden" name="page_suivi" value="<?php echo PAGE_SUIVI; ?>">
				<input type="hidden" name="anplus" />
				<input type="submit" value="&gt;&gt;" />
			</form>
		</li>
	</div>
	<!-- centre  -->
<!-- 	<div class="navigation_suivi_centre">
		<form id="ajout_dossier" method="post" action="gestion_dossiers.php">
			<li>
				<input type="submit" name="Submit" value="Portefeuille" />
			</li>
		</form>
		<form id="agence" method="post" action="agence.php">
			<li>
				<input type="submit" name="page" value="Agence" />
			</li>
		</form>
	</div> -->
<!-- </div> -->
</td></table>
