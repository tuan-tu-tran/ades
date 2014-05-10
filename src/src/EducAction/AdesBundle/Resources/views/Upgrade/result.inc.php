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
<?php View::FillBlock("title", "Mise à jour de la base de données ADES");?>

<?php View::StartBlock("post_head")?>
	<style type="text/css">
		div{padding-left:5px;}
	</style>
<?php View::EndBlock()?>

<?php View::StartBlock("body");?>
<h1>Mise à jour de la base de données</h1>

<div style="font-weight:bold; text-align:center; font-size:1.5em">
	<?php if($currentVersion==$result->toVersion):?>
		<p style="color:green">La mise à jour s'est bien passée</p>
	<?php else:?>
		<p style="color:red">La mise à jour a échoué.</p>
	<?php endif?>
</div>

<div>
	<p>Version de base: <?php echo $result->fromVersion?></p>
	<p>Version cible: <?php echo $result->toVersion?></p>
	<p>Version actuelle: <?php echo $currentVersion?></p>
	<p>Scripts à exécuter:</p>
	<ul>
	<?php foreach($result->scriptsToExecute as $script):?>
		<li><?php echo $script?></li>
	<?php endforeach;?>
	</ul>

	<?php if(count($result->executedScripts)>0):?>
		<p>Scripts correctement exécutés:</p>
		<ul>
		<?php foreach($result->executedScripts as $script):?>
			<li><?php echo $script?></li>
		<?php endforeach;?>
		</ul>
	<?php endif?>

	<?php if(isset($result->failedScript)):?>
		<p class="impt">L'exécution du script <?php echo $result->failedScript?> a échoué.</p>
		<p>Le système a renvoyé l'erreur:</p>
		<p><?php echo htmlspecialchars($result->failedScriptError)?></p>
	<?php endif?>
</div>

<?php if($currentVersion==$result->toVersion):?>
	<p style="font-weight:bold; text-align:center"><a href="index.php">Retour à la page d'accueil d'ADES</a></p>
<?php endif?>

<?php View::EndBlock();?>

<?php View::Embed("base.inc.php")?>
