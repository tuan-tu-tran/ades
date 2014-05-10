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
var d = new Date();
var dm = d.getMonth() + 1;
var dan = d.getYear();
if(dan < 999) dan+=1900;

nom_mois = new Array
	("Janvier","F&eacute;vrier","Mars","Avril","Mai","Juin","Juillet",
	"Ao&ucirc;t","Septembre","Octobre","Novembre","D&eacute;cembre");
jour = new Array ("Lu","Ma","Me","Je","Ve","Sa","Di");

var maintenant = new Date();
var ce_mois = maintenant.getMonth() + 1;
var cette_annee = maintenant.getYear();
if (cette_annee < 999) cette_annee+=1900;
var ce_jour = maintenant.getDate();

var formulaire = '';
var champ= '';
var zone = '';

function calendrier(mois,an)
{
var temps = new Date(an,mois-1,1);
// Start = jour de la semaine du premier jour du mois
var Start = temps.getDay();
if (Start > 0) Start--;
	else Start = 6;
// Stop = nombre de jours du mois (variable en fonction du mois)
var Stop = 31;
if(mois==4 ||mois==6 || mois==9 || mois==11 ) --Stop;
if(mois==2) 
	{
	Stop = Stop - 3;
	if(an%4==0) Stop++;
	if(an%100==0) Stop--;
	if(an%400==0) Stop++;
	}
cal = '<table cellpadding="0" cellspacing="1" width="150" class="calendrier">\n';
// en-tête du calendrier
cal += '<tr><td colspan="7" class="titreMois">\n';
cal += '<div style="float:left; width:10%;"><span class="fleche" title="reculer d\'un mois" onclick="javascript:changerMois('+mois+',-1)">&lt;</span></div>\n';
cal += '<div style="float:left; width:68%;">'+ nom_mois[mois-1] + ' ' + an+'</div>\n';
cal += '<div style="float:left; width:10%;"><span class="fleche" title="avancer d\'un mois" onclick="javascript:changerMois('+mois+',+1)">&gt;</span></div>\n';
cal += '<div style="float:left; width:10%;" class="case" title="cliquer pour fermer" onclick="javascript:cacher()";>x</div>\n';
cal += '</td></tr>\n';
cal += '<tr>\n';

// noms des jours
for(var i=0;i<=6;i++)
	cal +='<td class="jourSemaine">'+jour[i]+'</td>\n';
cal += '</tr>\n';

// on commence par le premier jour du mois
var date_jour = 1;
for(var i=0;i<=5;i++) 
 	{
	// passer 5 semaines en revue
	cal += '<tr>';
	for(var j=0;j<=6;j++) 
		// 7 jours de la semaine
		{
		if (((i==0)&&(j < Start)) || (date_jour > Stop))
		// nous sommes dans la première semaine (i==0) et avant le premier jour du mois (j < start) 
		// ou on a dépassé le dernier jour du mois
  		cal+='<td>&nbsp;</td>\n';
		else 
			{
			retour = ce_jour+'/'+ce_mois+'/'+cette_annee;
			if (j>=5)
				{
				fstyle="we";
				cal += '<td class="'+fstyle+'">'+date_jour+'</td>\n'
				// les dates de w.e. ne sont pas cliquables
				}
				else
				{
				if ((an==cette_annee)&&(mois==ce_mois)&&(date_jour==ce_jour))
					fstyle="aujourdhui";
					else
					fstyle="unJour";
				// aujourd'hui: mise en évidence de la date			
				cal += '<td class="'+fstyle+'" title="cliquer pour sélectionner" onclick="retourneDate('+date_jour+','+mois+','+an+')">'+date_jour+'</td>\n';
				}
			date_jour++;
        	}
      	}
    cal += '</tr>\n';
	// fin de la semaine
  }
cal +='</table>\n';
return (cal);
}

function changerMois (actuel,increment)
{
mois = actuel+increment;
if (mois == 0)
	{
	mois = 12;
	annee--
	}
	else
	if (mois > 12)
		{
		mois=1;
		annee++;
		};
obj = document.getElementById(zone);
obj.innerHTML = calendrier(mois,annee);
}

function date_php_mysql (ladate)
{
pos1 = ladate.indexOf("/");
pos2 = ladate.lastIndexOf("/");
jour = ladate.substring(0, pos1);
mois = ladate.substring(pos1+1,pos2);
annee = ladate.substring(pos2+2,10);
return (annee+"-"+mois+"-"+jour);
}

function dater (nomFormulaire, nomChamp, nomZone)
{
// retenir le nom du formulaire et du champ demandeurs
formulaire = nomFormulaire;
champ = nomChamp;
zone = nomZone;

var leJour = document.forms[formulaire][champ].value;
// séparer le mois et l'année de la date passée en paramètre
pos1 = leJour.indexOf("/");
pos2 = leJour.lastIndexOf("/");
mois = leJour.substring(pos1+1,pos2);
annee = leJour.substring(pos2+1,10);
// si le mois ou l'année n'existent pas, ajuster à la date du jour
if (((mois <1) || (mois > 12)) || (annee < 1582))
	{
	mois = ce_mois;
	annee = cette_annee;
	}
// fabriquer le calendrier correspondant au mois et à l'année
obj = document.getElementById(zone);
if (obj.innerHTML =='')
	obj.innerHTML = calendrier(mois,annee);
montrer();
return true;
}

function cacher()
{
document.getElementById(zone).style.display = 'none';
}

function montrer()
{
document.getElementById(zone).style.display = 'block';
}

function retourneDate (jour, mois, an)
{
document.forms[formulaire][champ].value = jour+'/'+mois+'/'+an;
cacher ();
}