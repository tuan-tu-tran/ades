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
function date_php_mysql (ladate)
{
pos1 = ladate.indexOf("/");
pos2 = ladate.lastIndexOf("/");
jour = ladate.substring(0, pos1);
mois = ladate.substring(pos1+1,pos2);
annee = ladate.substring(pos2+1,10);
return (annee+"-"+mois+"-"+jour);
}
function lejour (ladate)
{
pos1 = ladate.indexOf("/");
return (ladate.substring(0,pos1))
}
function lemois (ladate)
{
pos1 = ladate.indexOf("/");
pos2 = ladate.lastIndexOf("/");
return ladate.substring(pos1+1,pos2);
}
function lannee (ladate)
{
pos2 = ladate.lastIndexOf("/");
return (ladate.substring(pos2+1,10));
}

 function verifOrdreDates (date1, date2)
 {
 date1 = date1.value;
 date2 = date2.value;
 if ((date1 == "") || (date2 == ""))
	return true;
 date1Obj = new Date (lannee(date1), lemois(date1)-1, lejour(date1));
 date2Obj = new Date (lannee(date2), lemois(date2)-1, lejour(date2));
 if (date1Obj < date2Obj)
	return true
	else
	{
	alert('La deuxième date est antérieure ou égale à la première.\nVeuillez vérifier.');
	return false;
	}
 }