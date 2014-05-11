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
function getXMLHttpRequest() {
	var xhr = null;
	
	if (window.XMLHttpRequest || window.ActiveXObject) {
		if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject("Msxml2.XMLHTTP");
			} catch(e) {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
		} else {
			xhr = new XMLHttpRequest(); 
		}
	} else {
		alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
		return null;
	}
	
	return xhr;
};

function SupprimerMemo() {
	
	var tableau = document.getElementsByName("todolist[]");
	var tableauelementaenvoyer = new Array;
	var tableauelementaenvoyerINDICE = 0;
	for (var i=0; i < tableau.length; i++)
	{
		if (tableau[i].checked)
		{
			tableauelementaenvoyer[tableauelementaenvoyerINDICE] = tableau[i].value;
			tableauelementaenvoyerINDICE++;
		}
	}
	var tabElementTraiterAEnvoye = tableauelementaenvoyer.join(',');
	var xhr = getXMLHttpRequest();
	xhr.open("POST", "ADESsupressionmemo.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("todolist="+tabElementTraiterAEnvoye);
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
				document.getElementById("delmemo").innerHTML=xhr.responseText;
		}
	}
};

function AjouterMemo() {
	var xhr = getXMLHttpRequest();
	xhr.open("POST", "ADESajoutmemo.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("memoAAjouter="+document.getElementById("memoAAjouter").value);
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
				document.getElementById("delmemo").innerHTML=xhr.responseText;
				document.getElementById("memoAAjouter").value="";
		}
	}

};




