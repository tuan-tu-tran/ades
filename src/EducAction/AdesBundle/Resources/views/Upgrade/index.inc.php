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

<?php use EducAction\AdesBundle\View;?>

<?php View::FillBlock("title", "Mise à jour de la base de données ADES");?>

<?php View::StartBlock("body");?>
<h1>Mise à jour de la base de données</h1>

<?php if($fromVersion == $toVersion):?>
	<p class="avertissement">La base de données a déjà la version
	<?php echo $fromVersion?>
	et n'a pas besoin d'être mise à jour.</p>
	<p style="text-align:center"><a href="index.php">Retour à la page d'accueil</a></p>
<?php elseif($fromBeforeTo):?>
	<form method="POST" class="no_border">
	<p>La base de données doit être mise à jour de la version
	<?php echo $fromVersion?>
	vers la version
	<?php echo $toVersion?>
	</p>

	<?php if(count($scriptsToExecute)>0):?>
		<p>Les scripts de mise à jours suivant seront exécutés:</p>
		<ul>
		<?php foreach($scriptsToExecute as $script):?>
			<li><?php echo $script?></li>
		<?php endforeach;?>
		</ul>
	<?php else:?>
		<p class="impt">Aucun script de mise à jour ne sera exécuté!</p>
		<p class="impt">ATTENTION, CECI N'EST PAS NORMAL!</p>
	<?php endif;?>

	<?php if(count($upgradeScripts)>0):?>
		<p>Scripts de mise à jour disponibles:</p>
		<ul>
		<?php foreach($upgradeScripts as $script):?>
			<li><?php echo $script?></li>
		<?php endforeach;?>
		</ul>
	<?php else:?>
		<p class="impt">Aucun script de mise à jour disponible.</p>
	<?php endif?>
			
	<p>Un backup de la db actuelle sera créé avant de faire la mise à jour</p>
	<?php if(count($scriptsToExecute)>0):?>
		<input type="submit" value="Mettre à jour"/>
	<?php endif;?>
	</form>
<?php else:?>
	<div class="impt avertissement">
		<p>La version du code
		est antérieure à celle de la base de données:
		<?php echo $toVersion?> &lt;
		<?php echo $fromVersion?>
		</p>
	</div>
<?php endif;?>

<?php View::EndBlock();?>

<?php View::Embed("base.inc.php")?>
