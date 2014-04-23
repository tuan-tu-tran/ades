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
// ------------------------------------------------------------------------
// liste de tous les elements dont le ClassName commence par "OnCherche"
// ------------------------------------------------------------------------
function getElementsByClassName (onCherche)
	{
	var tableau = document.getElementsByTagName("*");
	var retvalue = new Array();
	var i, j;
	for (i = 0, j = 0; i < tableau.length; i++) {
	var c = " " + tableau[i].className + " ";
	if (c.indexOf(" " + onCherche) != -1) retvalue[j++] = tableau[i];
	}
	return retvalue;
}
// ------------------------------------------------------------------------
// montre un objet dont le ClassName est 'sousBloc' suivi d'un numéro
// ce numéro est le même que celui qui termine 'element'
// montre ("machin12", "sousBloc") rend visible le "sousBloc12" et cache tous
// les autres "sousBlocxxx"
// ------------------------------------------------------------------------
/* function montre (onglet, sousBloc)
	{
	var idOnglet = onglet.id;
	var Numero = idOnglet.match('[0-9]*$');
	// cacher tous les blocs de ClassName = 'sousBlocXXX'
	var TousLesBlocs = document.getElementsByClassName (sousBloc);
	for (var j = 0; j < TousLesBlocs.length; ++j) 
		TousLesBlocs[j].style.display='none';
	
	// rechercher le numéro du 'sousBloc' à montrer
	var lequel = sousBloc+parseInt(Numero);
	var element = document.getElementById(lequel);
		document.getElementById(lequel).style.display='block';
		
	// donner le style "onglet" à tous les objet dont le ClassName commence par 'onglet'
	var TousLesOnglets = document.getElementsByClassName ('onglet');
	for (var j = 0; j < TousLesOnglets.length; ++j) 
		TousLesOnglets[j].className = "onglet";
	
	// donner le style "ongletActif" à l'onglet numéro 'Numero'
	var lequel = "onglet"+parseInt(Numero);
	var element = document.getElementById(lequel);
	element.className="ongletDiscActif";
	}*/

// cache tous les éléments dont le "id" commence par "debut"
// sauf l'élément dont l'id est debutxxx (où xxx est le numéro)
// indique comme "actif" l'onglet correspondant au numéro
function cacher (debut, numero)
{
var liste = document.getElementsByTagName ("*");
for (var i=0; i < liste.length; i++)
	{
	if (liste[i].id.match ('^'+debut))
		liste[i].className="tableauInvisible";
	if (liste[i].id.match ('^ongletDisc'))
		liste[i].className="ongletDiscNormal";
	}
var tableau = 'tableau'+numero;
document.getElementById(tableau).className="tableauVisible";
document.getElementById('ongletDisc'+numero).className="ongletDiscActif"
}

// montre tous les éléments dont le "id" commence par "debut"
// marque tous les onglets comme non activés
function montrerTous (debut)
{
var liste = document.getElementsByTagName ("*");
for (var i=0; i < liste.length; i++)
	{
	if (liste[i].id.match('^'+debut))
		liste[i].className = "tableauVisible";
	if (liste[i].id.match('^ongletDisc'))
		liste[i].className="ongletDiscNormal";
	}
}