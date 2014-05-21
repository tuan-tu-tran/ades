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

use EducAction\AdesBundle\View;
use EducAction\AdesBundle\Html;

require_once DIRNAME(__FILE__)."/../../../../../web/config/constantes.inc.php";
?>
<?php View::FillBlock("title",ECOLE);?>

<?php View::StartBlock("post_head");?>
	<link rel="stylesheet" href="config/menu.css" type="text/css" media="screen">
	<script language="javascript" type="text/javascript" src="inc/fonctions.js"></script>
	<script type="text/javascript" type="text/javascript" src="ADESMemo.js"></script>
	<script type="text/javascript" src="inc/overlib/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
<?php View::EndBlock(View::PREPEND);?>

<?php View::StartBlock("body");?>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

<?php require ("menu.inc.php"); ?>

<div id="texte">
	<?php View::Block("content");?>
</div>

<div id="popup"></div>

<div class="inv" style="background-color: ##ffca00; height:4em;">
	<!-- non visible à l'impression -->
	<div style="width: 80px;display:inline-block; vertical-align:top;margin-top:2em;margin-left:10px">
		<a href="index.php"><img src="images/retour.gif" alt="retour à l'index" title="retour à l'index" border="0"></a>
		<a href="javascript:history.go(-1)"><img src="images/prec.gif" alt="page précédente" title="page précédente" border="0"></a>
	</div>

	<div style="float: right;margin-right:5px">
		<span class="micro">
			L'accès à cette page est réservé.<br/>
			<?php echo quiEstLa(); ?>
		</span>
	</div>

	<div style="display:inline-block;vertical-align:top;margin-top:2em;">
		<?php 
		$version = date ("Ymd", filemtime("./version"));
		echo "<i style='vertical-align:middle'>ADES Version: $version</i>   ";
		?>
		<a href="http://validator.w3.org/check?uri=referer"><img
		style="border: 0px solid ; width: 80px; height: 15px;vertical-align:middle"
		src="images/html.png" alt="Valid HTML 4.01 Transitional"></a> <a
		href="http://jigsaw.w3.org/css-validator/"><img
		style="border: 0px solid ; width: 80px; height: 15px;vertical-align:middle"
		src="images/css.png" alt="Valid CSS"></a>
		<a href="credits.php">A propos</a>
	</div>
</div>
<?php View::EndBlock()?>

<?php View::Embed("base.inc.php");?>
