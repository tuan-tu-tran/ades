<?php
/**
 * Copyright (c) 2014 Educ-Action
 * 
 * This file is part of ADES.
 * 
 * ADES is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * ADES is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with ADES.  If not, see <http://www.gnu.org/licenses/>.
*/
?><!--[if lt IE 7]>
<script type="text/javascript" src="inc/ieHover.js"></script>
<![endif]--><!-- <body onload="IEpatchLiHover('navlist');"> -->
<img src="images/ades.png" alt="Administration de la Discipline dans les Etablissements Scolaires" 
	style= "margin: 0px 25px; width: 130px; height: 130px; float: left; position: aboslute;" class="inv">
<h1 class="inv"><?php echo TITRE ?></h1>
<img src="images/printer.gif" title="Imprimer cette page" alt="Impression"
border="0" class="inv" style="float:right; cursor: pointer;" onclick="javascript:print()">
<a href="index.php">
<img src="images/retour.gif" title="retour à l'index" alt="Retour à l'index"
border="0" class="inv" style="float: right">
</a>
<a href="#">
<img src="images/prec.gif" border="0" title="Page précédente" alt="Page précédente"
 height="32" width="32" onclick="javascript:history.go(-1)" style="float: right" class="inv">
</a>

<?php if(User::IsLogged()):?>
	<ul class="navlist inv">
		<li><a href="index.php"><img src="images/home.png" class="icone" alt="Accueil"></a></li>
		<li><a href="mail.php"><img src="images/minimail.png" class="icone" alt="."><b><?php echo MiniMail::UnreadMailCount()?></b></a></li>
		<li><a href="#"><img src="images/eleve.png" class="icone" alt=".">Elèves</a>
			<ul>
			<li>
				<a href="parclasse.php" title="Recherche d'un &eacute;l&eagrave;ve par classe">
				<img src="images/classe.png" class="icone" alt=".">Par classe</a>
			</li>
			<li>
			<a href="parnom.php?nomOuPrenom=nom" title="Recherche d'un &eacute;l&eagrave;ve par nom">
			<img src="images/noms.png" class="icone" alt=".">
			Par nom</a>
			</li>
			<li>
			<a href="parnom.php?nomOuPrenom=prenom" title="Recherche d'un &eacute;l&eagrave;ve 
		par pr&eacute;nom">
		<img src="images/prenoms.png" class="icone" alt=".">
		Par pr&eacute;nom</a>
			</li>
			<?php if (User::HasAccess("educ", "admin")):?>
				<li>
					<a href="ficheel.php?mode=nouveau" title="Ajouter un &eacute;l&eagrave;ve"><img src="images/nouveau.png" class="icone" alt=".">Nouveau</a>
				</li>
			<?php endif;?>
			</ul>
		</li>
		<li><a href="#"><img class="icone" src="images/retenue.png" alt=".">Retenues</a>
			<ul>
			<li>
			<a href="listeretenues.php" title="Liste des élèves en retenue 
				(par date)">
				<img src="images/listes.png" alt="." class="icone">Listes</a>
			</li>
			<?php
			if (User::HasAccess ("educ", "admin"))
				{
				echo "<li>\n<a href=\"retenue.php\" ";
				echo "title=\"Liste des dates de retenues\">";
				echo "<img src=\"images/dates.png\" class=\"icone\" alt=\".\"> ";
				echo "Dates</a>\n</li>\n";
				}
			?>
			<?php
			if (User::HasAccess ("educ", "admin"))
				{
				echo "<li>\n<a href=\"retenue.php?mode=nouveau\" ";
				echo "title=\"Nouvelle date de retenue\">";
				echo "<img src=\"images/nouvretenue.png\" class=\"icone\" alt=\".\"> ";
				echo "Nouvelle date</a>\n</li>\n";
				}
			?>
			</ul>
		</li>
		<li><a href="#"><img src="images/synthese.png" class="icone" alt=".">Synth&egrave;ses</a>
			<ul>
			<li>
			<a href="synthese.php" title="Que s'est-il pass&eacute; (classe, p&eacute;riode)...">
			<img src="images/syntheses.png" class="icone" alt=".">Synth&egrave;ses</a>
			</li>
			<?php 
			$autorise = array ("educ", "admin");
			if (in_array($_SESSION['identification']['privilege'], $autorise))
				{
				echo "<li>\n<a href=\"synthcsv.php\" title=\"Exporter vers un tableur ";
				echo "(format csv)\">";
				echo "<img src=\"images/tableur.png\" class=\"icone\" alt=\".\">";
				echo "Export tableur</a>\n</li>\n";
				}
			?>
			</ul>
		</li>
		<li><a href="#"><img src="images/utilitaires.png" class="icone" alt=".">Utilitaires</a>
			<ul>
			<?php 
			if (User::HasAccess ("educ", "admin"))
				{
				echo "<li>\n<a href=\"sauver.php\" title=\"Sauvegarde des donn&eacute;es\">";
				echo "<img src=\"images/backup.png\" class=\"icone\" alt=\".\"> ";
				echo "Backup</a>\n</li>\n";
				}
			if (User::HasAccess ("admin"))
				{
				echo "<li>\n<a href=\"vider.php\" title=\"Vider les donn&eacute;es\">";
				echo "<img src=\"images/vider.png\" class=\"icone\" alt=\".\"> ";
				echo "Vider les donn&eacute;es</a>\n</li>\n";
				}
			?>
			
			<?php 
			if (User::HasAccess ("admin"))
				{
				echo "<li>\n<a href=\"importer.php\" title=\"Importer les donn&eacute;es de PROECO\">";
				echo "<img src=\"images/proeco.png\" class=\"icone\" alt=\".\"> ";
				echo "Import ProEco</a>\n</li>\n";
				}
			?>
			<?php 
			if (User::HasAccess ("admin"))
				{
				echo "<li>\n<a href=\"utilisateur.php\" title=\"Gestion des utilisateurs\">";
				echo "<img src=\"images/utilisateur.png\" alt=\".\" class=\"icone\">";
				echo "Utilisateurs</a>\n</li>\n";
				}
			?>
			<?php 
			if (User::HasAccess ("admin"))
				{
				echo "<li>\n<a href=\"option.php\" title=\"Configuration\">";
				echo "<img src=\"images/utilitaires.png\" alt=\".\" class=\"icone\">";
				echo "Configuration</a>\n</li>\n";
				}
			?>
			<li>
			<a href="mdp.php" title="Changer votre mot de passe">
			<img src="images/mdp.png" class="icone" alt=".">Mot de passe</a>
			</li>
			</ul>
			<li>
			<a href="deconnexion.php">
			<img src="images/deconnexion.png" class="icone" alt=".">
			D&eacute;connexion</a>
			</li>
			</ul>
		</li>
	</ul>
<br style="margin-bottom: 2em" />
<?php endif; ?>
