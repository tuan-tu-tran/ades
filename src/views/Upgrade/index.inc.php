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

<?php View::StartBlock("body");?>
<h1>Mise à jour de la base de données</h1>

<?php if($fromVersion == $toVersion):?>
	<p>La base de données a déjà la version
	<?php echo $fromVersion?>
	et n'a pas besoin d'être mise à jour.</p>
<?php else:?>
	<p>La base de données doit être mise à jour de la version
	<?php echo $fromVersion?>
	à la version
	<?php echo $toVersion?>
	</p>
<?php endif;?>

<?php View::EndBlock();?>

<?php View::Embed("base.inc.php")?>
