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
//***************************************
// fonctions AJAX
//***************************************
// retourne un objet xmlHttpRequest.
// méthode compatible entre tous les navigateurs (IE/Firefox/Opera)
function getXMLHTTP()
	{
	var xhr=null;
	if (window.XMLHttpRequest) // Firefox et autres
		xhr = new XMLHttpRequest();
	else if (window.ActiveXObject){ // Internet Explorer
		try 
		{xhr = new ActiveXObject("Msxml2.XMLHTTP");}
			catch (e) 
			{
			try 
			{ xhr = new ActiveXObject("Microsoft.XMLHTTP");} 
			catch (e1) 
			{ xhr = null;}
			}
		}
		else { // XMLHttpRequest non supportï¿½ par le navigateur
		alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
		}
	return xhr;
}


function $(id)
{
var x = document.getElementById(id);
return (x)
}
