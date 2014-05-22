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
namespace EducAction\AdesBundle;
use EducAction\AdesBundle\Controller\DetentionSlip as Error;

switch ($error) {
    case Error::ERR_NO_CONFIG:
        $msg="Le billet de retenue n'est pas configuré";
        break;
    case Error::ERR_READ_CONFIG:
        $msg="Impossible de lire la configuration du billet de retenue: ".Tools::GetLastError().".";
        break;
    case Error::ERR_CONFIG_CONTENT:
        $err=Tools::GetLastError();
        $msg="La configuration du billet de retenue est incorrect".($err?": $err":"").".";
        break;
    case Error::ERR_IMG_TYPE:
        $msg="Le format du logo de l'établissement n'est pas supporté.";
        break;
    default:
        throw new \Exception("unhandled error type: '$error'");
}
?>

<?php View::StartBlock("content")?>
    <h2>Aperçu du billet retenue</h2>

    <fieldset class="notice impt auto_close">
        <legend>Erreur</legend>
        <p><?php echo $msg?></p>
    </fieldset>

    <p><a href="<?php echo $config_url?>">Configurer le billet de retenue</a></p>
<?php View::EndBlock()?>

<?php View::Render("layout.inc.php")?>


