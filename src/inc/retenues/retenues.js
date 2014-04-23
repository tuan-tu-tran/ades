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
function retenue (type, affiche)
{

}

function écrire (formulaire, champ)
{
var  = document.forms[formulaire][champ].value;

obj = document.getElementById("retenue");
if (obj.innerHTML =='')
	obj.innerHTML = retenue(type, affiche);
montrer();
return true;
}

function cacher()
{
document.getElementById("retenue").style.display = 'none';
}

function montrer()
{
document.getElementById("retenue").style.display = 'inline';
}

function retourner (jour, mois, an, formulaire, champ)
{
document.forms[formulaire][champ].value = ..... ;
cacher ();
}