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
?><?php View::Embed("header.inc.php")?>

<fieldset id="cadreGauche" style="float:none;margin-left:auto;margin-right:auto">
	<legend>Sécurité</legend>
	<div class="impt">
		<?php if(count($backup_files)==0):?>
			<p>Aucune sauvegarde effectuée.</p>
		<?php else:?>
			<p>
				La dernière sauvegarde
				<?php echo $last_backup?>
				a été effectuée le <?php echo $last_backup_time->format("d/m/Y à H\hi")?>
			</p>
			<p>Il y a
			<?php if($last_backup_since->days > 0):?>
				<?php echo $last_backup_since->days." jour".($last_backup_since->days>1?"s":"")."."?>
			<?php elseif($last_backup_since->h > 0):?>
				<?php echo $last_backup_since->h?> heure(s).
			<?php else:?>
				moins d'une heure.
			<?php endif;?>
			</p>
		<?php endif;?>
	</div>
</fieldset>

<h3>Liste de dernières sauvegardes disponibles</h3>

<table width="50%" border="1" cellpadding="2" style="margin:auto">
	<tr>
		<td>Fichiers de sauvegarde</td>
		<td style="text-align:center">Effacer</td>
	</tr>
	<?php foreach($backup_files as $file):?>
		<tr>
			<td>
				<a href="<?php echo $file["path"]?>"
					target="_blank"
					<?php Overlib::Render('Clic du bouton droit et Enregister la cible sous...')?>
				><?php echo $file["name"]?></a></td>
			<td style="text-align:center">
				<a href="?action=delete&amp;file=<?php echo $file["name"]?>"
					title="Supprimer la sauvegarde <?php echo $file["name"]?>"
					<?php Overlib::Render('Cliquer pour supprimer la sauvegarde.')?>
				><img style="width:16px;height:16px;" border="0" alt="X" src="images/suppr.png"></a></td>
		</tr>
	<?php endforeach;?>
</table>
<?php View::Embed("footer.inc.php")?>
