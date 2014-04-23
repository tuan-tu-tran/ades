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
InfoUser ="";
$(function(){
				
				//attach autocomplete
				$("#to").autocomplete({
					
					//define callback to format results
					source: function(req, add){
					
						//pass request to server
						$.getJSON("friends.php?callback=?", req, function(data) {
							
							//create array for response objects
							var suggestions = [];
							
							//process response
							$.each(data, function(i, val){								
								suggestions.push(val.name);
							});
							
							//pass array to callback
							add(suggestions);
						});
					},
					
					//define select handler
					select: function(e, ui) {
						
						//create formatted friend
						var friend = ui.item.value,
							span = $("<span>").text(friend),
							a = $("<a>").addClass("remove").attr({
								href: "javascript:",
								title: "Remove " + friend
							}).text("x").appendTo(span);
						document.getElementById('to').innerHTML += "<input type=\"hidden\" id=\"infouser\" value =\""+ friend.value+"\">";
						//add friend to friend div
						span.insertBefore("#to");
						
						
					},
					
					//define select handler
					change: function() {
						
						//prevent 'to' field being updated and correct position
						$("#to").val("").css("top", 2);
					}
				});
				
				//add click handler to friends div
				$("#friends").click(function(){
					
					//focus 'to' field
					$("#to").focus();
				});
				
				//add live handler for clicks on remove links
				$(".remove", document.getElementById("friends")).live("click", function(){
				
					//remove current friend
					$(this).parent().remove();
					
					//correct 'to' field position
					if($("#friends span").length === 0) {
						$("#to").css("top", 0);
					}				
				});				
			});
			
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

function EnvoiEmail() {
	//var xhr = getXMLHttpRequest();
	//xhr.open("POST", "ADESenvoiemail.php", true);
	//xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	alert(document.getElementById("message").value+document.getElementsByName("infouser").value+document.getElementById("subject").value);

	//xhr.send("NewEmail="+document.getElementById("message").value+"&Message="+document.getElementById("infouser").value+"&Sujet="+document.getElementById("subject").value);
	//xhr.onreadystatechange = function() {
		//if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			//	alert("Message envoyé");
				//window.location.replace("http://localhost/ades_dev/mail.php");
		//}
	//}
};
