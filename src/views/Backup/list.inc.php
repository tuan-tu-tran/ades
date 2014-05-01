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
?>

<?php View::StartBlock("post_head")?>
	<?php Html::Script("js/jquery-1.11.0.min.js")?>
	<script type="text/javascript">
		$(function(){
			$("div#notice").click(function(){
				$(this).slideUp();
				nd();
			});
		});
	</script>
	<style type="text/css">
		tr.backup:hover{background-color:yellow}
	</style>
<?php View::EndBlock()?>

<?php View::Embed("header.inc.php")?>

<h2>Sauvegarde de la base de données</h2>

<fieldset class="notice">
	<legend>Dernière sauvegarde</legend>
	<div class="<?php echo (count($backup_files)==0 || $last_backup_since->days>0)?"impt":""?> ">
		<?php if(count($backup_files)==0):?>
			<p>Aucune sauvegarde effectuée.</p>
		<?php else:?>
			<p>
				La dernière sauvegarde
				<?php echo $last_backup?>
				a été effectuée le <?php echo $last_backup_time->format("d/m/Y à H\hi")?>.
			</p>
			<p>Il y a
			<?php if($last_backup_since->days > 0):?>
				<?php echo $last_backup_since->days." jour".($last_backup_since->days>1?"s":"")."."?>
			<?php elseif($last_backup_since->h > 0):?>
				<?php echo $last_backup_since->h." heure".($last_backup_since->h>1?"s":"")."."?>
			<?php else:?>
				moins d'une heure.
			<?php endif;?>
			</p>
		<?php endif;?>
	</div>
</fieldset>

<div id="notice" style="cursor:pointer"
	<?php Overlib::Render("Cliquer pour fermer ce message")?>
>
<?php if($backup):?>
	<?php if($backup->failed):?>
		<fieldset class="notice">
			<legend>Erreur</legend>
			<p class="impt">La sauvegarde a échoué!</p>

			<?php if($backup->dump_launched):?>
				<p>Le système a renvoyé l'erreur:</p>
				<p><?php echo htmlspecialchars($backup->error);?></p>
			<?php else:?>
				<p>L'utilitaire de sauvegarde n'a pas pu être exécuté.</p>
			<?php endif;?>
		</fieldset>
	<?php else:?>
		<p class="success">Une nouvelle sauvegarde a été effectuée dans le fichier <?php echo $backup->filename;?></p>
	<?php endif;?>
<?php endif;?>

<?php if($delete):?>
	<?php if($delete->failed):?>
		<fieldset class="notice">
			<legend>Erreur</legend>
			<p class="impt">La sauvegarde <?php echo $delete->filename?> n'a pas pu être effacée!</p>

			<p>Le système a renvoyé l'erreur:</p>
			<p><?php echo htmlspecialchars($delete->error);?></p>
		</fieldset>
	<?php else:?>
		<p class="success">La sauvegarde <?php echo $delete->filename?> a été effacée.</p>
	<?php endif;?>
<?php endif;?>
</div>

<form method="POST" action="?action=create" style="border:none;padding:0">
<input type="submit" value="Créer une nouvelle sauvegarde"/>
</form>
<?php if(count($backup_files)>0):?>
<h3>Liste de dernières sauvegardes disponibles</h3>

<table width="50%" border="1" cellpadding="2" style="margin:auto">
	<tr>
		<td>Fichiers de sauvegarde</td>
		<td style="text-align:center">Date</td>
		<td style="text-align:center">Taille</td>
		<td style="text-align:center">Effacer</td>
	</tr>
	<?php foreach($backup_files as $file):?>
		<tr class="backup">
			<td>
				<a href="<?php echo $file["path"]?>"
					target="_blank"
					<?php Overlib::Render('Cliquer pour télécharger cette sauvegarde')?>
				><?php echo $file["name"]?></a></td>
			<td style="text-align:center"><?php echo $file["time"]->format("d/m/Y à H\hi")?></td>
			<td style="text-align:right"><?php ViewHelper::FileSize($file["size"])?></td>
			<td style="text-align:center">
				<a href="?action=delete&amp;file=<?php echo $file["name"]?>"
					<?php Overlib::Render('Cliquer pour supprimer cette sauvegarde.')?>
					onclick="return confirm('Êtes vous sûr de vouloir effacer cette sauvegarde?\nCette action est IRREVERSIBLE!');"
				><img style="width:16px;height:16px;" border="0" alt="X" src="images/suppr.png"></a></td>
		</tr>
	<?php endforeach;?>
</table>
<?php endif;?>
<?php View::Embed("footer.inc.php")?>
