<?php
/**
 * Copyright (c) 2014 Tuan-Tu TRAN
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
?>
<?php View::StartBlock("content")?>
		<form name="form" method="post" action="<?php echo $install->GetDbConfigSubmitUrl()?>">
		<p>
			<label>Serveur Sql :</label>
			<input value="<?echo Html::Encode($install->host)?>" name="sqlserver" id="sqlserver" size="30" maxlength="50" type="text">
		</p>
		<p>
			<label>Utilisateur :</label>
			<input value="<?echo Html::Encode($install->username)?>" name="utilisateursql" id="utilisateur" size="30" maxlength="50" type="text">
		</p>
		<p>
			<label>Mot de Passe :</label>
			<input value="<?echo Html::Encode($install->pwd)?>" name="motdepassesql" id="motdepasse" size="30" maxlength="50" type="password">
		</p>
		<p>
			<label>Nom de la Base de données :</label>
			<input value="<?echo Html::Encode($install->dbname)?>" name="nomdelabasesql" id="nomdelabase" size="30" maxlength="50" type="text">
		</p>

		<input name="Submit" value="Enregistrer" type="submit">

        <?php if(isset($install->missing_fields)):?>
            <p>Veuillez remplir complètement le formulaire</p>
        <?php endif; ?>

        <?php if(isset($install->error)):?>
            <p>Le connexion à la base de données a échoué.</p>
            <p>Le système a renvoyé l'erreur: <?php echo $install->error?></p>
        <?php endif; ?>
	</form>

<?php View::EndBlock()?>

<?php View::Render("Install/layout.inc.php")?>

