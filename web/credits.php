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
require ("inc/prive.inc.php");
require ("inc/fonctions.inc.php");
require ("config/constantes.inc.php");
Normalisation();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1"
 http-equiv="content-type">
  <title><?php echo ECOLE ?></title>
  <link media="screen" rel="stylesheet" href="config/screen.css" type="text/css">
  <link media="print" rel="stylesheet" href="config/print.css" type="text/css">
  <link rel="stylesheet" href="config/menu.css" type="text/css" media="screen">
</head>
<body>
<?php // autorisations pour la page
autoriser(); // tout le monde
// menu
require ("inc/menu.inc.php");
?>
<div id="texte">
<h2>Crédits</h2>
<h4>Yves de Voghel (<a href="http://www.ond.irisnet.be/2025/"
 target="_blank">Notre-Dame de la Sagesse</a> à Ganshoren).</h4>
Optimisation et simplification du module d'<a
 href="proeco%20vers%20ades.pdf" target="_blank">importation des
données depuis ProEco</a>. Nombreuses suggestions et relevé de
nombreuses imperfections (corrigées ou à corriger).<br>
<h2><img
 style="margin: 15px 65px; width: 93px; height: 93px; float: right;"
 src="images/monogramme.jpg" alt="Nicolas Boulanger"></h2>
<h4>Nicolas Boulanger</h4>
<p>Le logo ADES présent sur toutes les pages&nbsp;et le monogramme
ci-contre sont l'oeuvre de Nicolas Boulanger.</p>
<p style="text-align: center;">N.Boulanger.P<img src="images/at.png"
 style="width: 16px; height: 16px;" alt="@">gmail.com</p>
<h4>Sébastien Truyens (<a href="http://www.saintdominique.be/"
 target="_blank">Institut Saint Dominique</a> à Schaerbeek)</h4>
<p>Tests intensifs de la version 20080414 et relevé de nombreuses
imperfections (certaines encore à corriger)</p>
<h4>Ressources informatiques</h4>
<p>Les icones utilisées dans l'interface principale proviennent du "<a
 target="_blank"
 href="http://tango.freedesktop.org/Tango_Desktop_Project">Tango
Desktop Project</a>" distribuées sous <a
 href="http://creativecommons.org/licenses/by-sa/2.5/" target="_blank">licence
Cc</a>.</p>
Le programme a été réalisé en utilisant uniquement des logiciels libres:<br>
<ul>
  <li>Système d'exploitation GNU/Linux <a
 href="http://www.ubuntu-fr.org/">Ubuntu</a>&nbsp;</li>
  <li>Plateforme Apache/PHP/MySQL <a
 href="http://www.apachefriends.org/fr/xampp-linux.html">XAMPP</a></li>
  <li>Editeur HTML Wysiwyg <a href="http://www.kompozer.net/">KompoZer</a></li>
  <li>Navigateur Mozilla <a
 href="http://www.mozilla-europe.org/fr/products/firefox/">Firefox</a>
+ les extensions <a
 href="https://addons.mozilla.org/en-US/firefox/addon/60">WebDeveloper</a>
et <a href="https://addons.mozilla.org/fr/firefox/addon/1843">FireBug</a></li>
  <li>Editeur de texte <a href="http://www.scintilla.org/SciTE.html">Scite</a></li>
  <li>Traitement de texte <a href="http://fr.openoffice.org/">OpenOffice.org
Writer</a> (pour la documentation)</li>
  <li>Editeur graphique <a href="http://www.gimp.org/">Gimp</a> (pour
certaines images)</li>
</ul>
<h2>Licence</h2>
<p>Le logiciel ADES est un logiciel libre et gratuit distribué selon
les termes de la <strong>Licence publique
générale GNU</strong> dont le texte est disponible dans
le fichier <strong>gpl.txt</strong> qui doit figurer dans le fichier
d'archive qui a été mis à votre disposition.</p>
<p>Le texte original de cette licence peut être trouvé à l'adresse <a
 href="http://www.gnu.org/licenses/gpl-3.0.txt">http://www.gnu.org/licenses/gpl-3.0.txt</a>
(en anglais) et une traduction non officielle se trouve à l'adresse <a
 href="http://www.rodage.org/gpl-3.0.fr.html">http://www.rodage.org/gpl-3.0.fr.html</a>.</p>
<p>Le logiciel ADES est fourni dans l'état et sans aucune garantie
d'aucune sorte. Chacun est libre de consulter le code source et de
vérifier qu'il correspond à ses besoins, voire de le modifier pour
l'adapter à ses besoins.</p>
<h2>Auteur</h2>
<p>Yves Mairesse</p>
<p><a href="http://www.sio2.be/ades">http://www.sio2.be/ades</a></p>
<p>Me contacter (ymairesse_chez_sio2.be -remplacer _chez_ par @) pour
toute question relative aux mises à jour ou à
l'adaptation du logiciel à votre établissement scolaire.</p>
<p>Merci de me signaler tout problème, bug ou amélioration
indispensable.</p>
<p>Le 29/08/2007</p>
</div>
<div id="pied"><?php require ("inc/notice.inc.php"); ?></div>
</body>
</html>
