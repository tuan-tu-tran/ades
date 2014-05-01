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

<p>La sauvegarde a échoué</p>

<?php if($dump_launched):?>
	<p>Le système a renvoyé l'erreur:</p>
	<p><?php echo htmlspecialchars($error);?></p>
<?php else:?>
	<p>L'utilitaire de sauvegarde n'a pas pu être exécuté</p>
<?php endif;?>

<?php View::Embed("footer.inc.php")?>
