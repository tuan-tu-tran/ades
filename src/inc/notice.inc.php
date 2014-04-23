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
?><div id="popup"></div>
<div class="inv" style="background-color: ##ffca00; height:3em;">
<!-- non visible � l'impression -->
	<div style="float:left; width: 80px;">
	<a href="index.php">
	<img src="images/retour.gif" alt="retour à l'index"
		title="retour � l'index" border="0">
	</a>
	<a href="javascript:history.go(-1)">
	<img src="images/prec.gif" alt="page pr�c�dente" title="page pr�c�dente" border="0">
	</a>
</div>

<div style="float: right;">
<span class="micro">
L'acc�s � cette page est r�serv�.
<?php echo quiEstLa(); ?>
</span>
</div>
<?php 
$version = date ("Ymd", filemtime("./version"));
echo "<i>ADES Version: $version</i>   ";
?>
<a href="http://validator.w3.org/check?uri=referer"><img
 style="border: 0px solid ; width: 80px; height: 15px;"
 src="images/html.png" alt="Valid HTML 4.01 Transitional"></a> <a
 href="http://jigsaw.w3.org/css-validator/"><img
 style="border: 0px solid ; width: 80px; height: 15px;"
 src="images/css.png" alt="Valid CSS"></a>
 <a href="credits.php">A propos</a>
 </div>
