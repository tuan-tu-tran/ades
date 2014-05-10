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
// vérification de n'importe quel formulaire
// les champs obligatoires reçoivent la classe 'obligatoire'
// les champs d'adresse e-mail reçoivent l'id 'email'
// si un champ a été oublié ou une adresse e-mail invalide a été détectée
// on change la classe du champ correspondant
// en utilisant une feuille de style ad-hoc, tous les problèmes sont 
// clairement visibles dans le formulaire. Par exemple
// <style>
// .obligatoire {background-color: blue}
// .oublie {background-color: red}
// </style>

function verifForm(formulaire) 
{
var champ;
var isOk = true; 
   // RegEx qui permet de controler qu'une adresse mail est valide
var reg = new RegExp('^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+$', 'i');
   // parcours des elements du formulaire
   var nb = formulaire.elements.length;
   var i = 0;
   // remise à la valeur initiale des classes après le test précédent du formulaire
   while (i<nb)
   		{
		champ = formulaire.elements[i];
   		if (champ.className=='oublie') champ.className='obligatoire';
		i++;
		}
   i = 0;
   while (isOk && (i < nb))
   	{
	champ = formulaire.elements[i];
	
	if (champ.className =='obligatoire')
		{
		if (champ.id =='email')
			isOk = (reg.test(champ.value)) 
			else
			isOk = (!(champ.value ==''))
		}
		else if (champ.id =='email') isOk = (reg.test(champ.value) || (champ.value == ''))

	if (!(isOk))
		{
		if (champ.id == 'email')
			{
			alert('Erreur!\nAdresse e-mail invalide.\nVeuillez corriger.');
			if (champ.className == 'obligatoire') champ.className = 'oublie'
			}
			else
			{
			alert('Erreur!\nCe champ est obligatoire.\nVeuillez corriger.');
			champ.className = 'oublie';
			}
		champ.focus()
		}
	i++;
	}
	return isOk;
}

<!-- Vérification de la fiche "élève" // -->
function verifEleve (formulaire)
{
var erreur = '';
if (formulaire['nom'].value =='')
	{
	erreur = "Vous avez oublié d\'indiquer le nom de l\'élève.\n";
	formulaire.nom.focus ();
	}
if (formulaire.prenom.value =='')
	{
	erreur = erreur+"Vous avez oublié d\'indiquer le prénom de l\'élève.\n";
	formulaire.prenom.focus();
	}
if (formulaire.classe.value =='')
	{
	erreur = erreur+"Vous avez oublié d\'indiquer la classe de l\'élève.\n";
	formulaire.classe.focus();
	}

return (erreur == '');
}

function verifNouvelleRetenue (formulaire)
{
OK1 = verifForm(formulaire);
var occupation = parseInt(formulaire.elements.occupation.value);
var places = parseInt(formulaire.elements.places.value);
OK2 = (places >= occupation);
if (!(OK2))
	{
	alert('Vous ne pouvez indiquer moins de '+occupation+' place(s).');
	formulaire.elements.places.select();
	}
OK = (OK1 && OK2);
return OK;
}

function cacheElement (cacher, idElement)
{
var Element = document.getElementById(idElement);
if (cacher)
	Element.style.display = "none"
	else
	Element.style.display = "block"
}

 <!-- onglets // -->

function swap (c1, c2, o1, o2)
{
document.getElementById(c1).style.display = "none";
document.getElementById(o1).className="ongletInactif";
document.getElementById(o1).title="Cliquer pour activer l'onglet";
document.getElementById(c2).style.display = "block";
document.getElementById(o2).className="ongletActif";
document.getElementById(o2).title="";
}
function titreOnglets (o1, o2)
{
var titre = "Cliquer pour activer l'onglet";
if (document.getElementById(o1).className=="ongletActif")
	document.getElementById(o1).title = "";
	else document.getElementById(o1).title = titre;
if (document.getElementById(o2).className=="ongletActif")
	document.getElementById(o2).title = ""
	else document.getElementById(o2).title = titre;
}

<!-- redirection // -->
function redirection (adresse)
{
window.location = adresse;
}

function textLimit(champ, maxlen) {
if (champ.value.length > maxlen)
	{
	champ.value = champ.value.substring(0, maxlen);
	champ.style.color = "red";
	}
	else
	champ.style.color = "";
}function completer (classe)
{
var zone = document.getElementById('formClasse');
zone.value = classe;
}

<!-- fonction AJAX ------------------------------ -->
function xhr ()
{
var x = null;
if (window.XMLHttpRequest)
	x = new XMLHttpRequest();
	else if (window.ActiveXObject)
		x = new ActiveXObject("Microsoft.XMLHTTP");
		else
		die('Votre navigateur de supporte pas la technologie AJAX(XMLHttpRequest)...');
return (x);
}


function $(id)
{
var x = document.getElementById(id);
return (x)
}

function verif (formulaire)
{
if ((formulaire.sujet.value == "") || (formulaire.matiere.value ==""))
	{
	alert('Il faut compléter toutes les rubriques.');
        return  false;
        }
        else return true;
    }
    
function verifMdp (formulaire)
{
if (formulaire.mdp1.value == formulaire.mdp2.value)
	if (formulaire.mdp1.value == '')
    	{
    	alert('Mot de passe manquant.');
		return (false);
		}
		else
		return (true);
	else
	{
	alert('Les deux versions du nouveau mot de passe ne sont pas identiques.\nRecommencer.');
	return (false);
    }
}

function completer (classe)
{
var zone = document.getElementById('formClasse');
zone.value = classe;
}