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
// limites de la période horaire
var minHeure = 07;
var maxHeure = 17
var minMinutes = 0;
var maxMinutes = 60;

function cacherHorloge ()
{
document.getElementById('horloge').style.display = 'none';
// supprimer la ligne suivante pour conserver les modifications d'heures et de minutes après un ESC
document.getElementById('horloge').innerHTML = '';
}
function montrerHorloge()
{
document.getElementById('horloge').style.display = 'block';
}
// fonction Javascript affichant l'horloge
// les objets 'ticker' servent à faire évoluer les valeurs des heures et des minutes
function afficheHorloge (heure, minutes, formulaire, champ)
{
hor = '<span onClick="javascript:changerHeures('+heure+',-1,'+formulaire+','+champ+')" ';
hor += 'class="ticker" title="Cliquer pour reculer l\'heure">&nbsp;&lt;&nbsp;</span>&nbsp;'+heure+'\n';
hor += '<span onClick="javascript:changerHeures('+heure+',+1,'+formulaire+','+champ+');" ';
hor += 'class="ticker" title="Cliquer pour avancer l\'heure">&nbsp;&gt;&nbsp;</span>&nbsp;h<br />\n';

hor += '<span onClick="javascript:changerMinutes('+minutes+',-5,'+formulaire+','+champ+')" ';
hor += 'class="ticker" title="Cliquer pour reculer les minutes">&nbsp;&lt;&nbsp;</span>&nbsp;'+minutes+'\n';
hor += '<span onClick="javascript:changerMinutes('+minutes+',+5,'+formulaire+','+champ+');" ';
hor += 'class="ticker"  title="Cliquer pour avancer les minutes">&nbsp;&gt;&nbsp;</span>&nbsp;m<br />\n';

hor += '<div class="barre">\n';
hor += '<div class="btn" title="Cliquer poupr annuler" onclick="cacherHorloge()">ESC</div>&nbsp;|&nbsp;\n';
hor += '<div onclick="javascript:retourneHeure('+heure+','+minutes+','+formulaire+','+champ+');" ';
hor += 'class="btn" title="Cliquer pour confirmer">&nbsp;OK&nbsp;</div> \n';
hor += '</div>\n';
return (hor);
}
// fonction qui renvoie les valeurs d'heure et de minutes au champ appelant du formulaire
function retourneHeure (formulaire, champ)
{
var heure = document.getElementById("heure").innerHTML;
var minutes = document.getElementById("minutes").innerHTML;
if (heure < 10) heure = '0'+heure;
if (minutes < 10) minutes = '0'+minutes;
document.forms[formulaire][champ].value = heure+'h'+minutes;
cacherHorloge();
}

// incrémentation ou décrémentation du compteur des minutes les minutes sont toujours présentées à deux chiffres
function changerMinutes (actuel, increment, formulaire, champ)
{
minutes = actuel + increment;
if (minutes < minMinutes) minutes = maxMinutes + minutes;
if (minutes >= maxMinutes) minutes = minMinutes;
if (minutes < 10) minutes = '0'+minutes;
document.getElementById("horloge").innerHTML = afficheHorloge(heure, minutes, formulaire, champ);
}

// incrémentation ou décrémentation du compteur des heures la plage des heures permises est actuellement 7h à 17 h
// les heures sont toujours présentées à deux chiffres
function changerHeures (actuel, increment, formulaire, champ)
{
heure = actuel + increment;
if (heure < minHeure) heure = maxHeure;
if (heure > maxHeure) heure = minHeure;
if (heure < 10) heure = '0'+heure;
document.getElementById("horloge").innerHTML = afficheHorloge(heure, minutes, formulaire, champ);
}

// fonction principale doit être appelée lorsque le champ qui présente l'heure reçoit le focus -onFocus()
function horaire (formulaire, champ)
{
var heureminutes = document.forms[formulaire][champ].value;
if (heureminutes == '') heureminutes = "00h00";
// séparer les heures des minutes de l'heure passée en paramètre
pos = heureminutes.indexOf("h");
heure = heureminutes.substring(0,pos);
minutes = heureminutes.substring(pos+1,5);

obj = document.getElementById("horloge");
if (obj.innerHTML =='')
	obj.innerHTML = afficheHorloge(heure, minutes, formulaire, champ);
montrerHorloge();
return true;
}