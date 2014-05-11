<!--
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
-->
<script language="javascript" type="text/javascript">
var minHeure = 07;
var maxHeure = 17
var minMin = 0;
var maxMin = 55;

function changerHeures (increment)
{
var heureMinutes = document.getElementById("heure");
pos = heureMinutes.value.indexOf("h");
heure = heureMinutes.value.substring(0,pos);
minutes = heureMinutes.value.substring(pos+1,5);
var h = parseInt(heure) + increment;
if (h > maxHeure) h = maxHeure;
if (h < minHeure) h = minHeure;
hM = String(h) +"h"+ String(minutes);
heureMinutes.value = hM;
}
function changerMinutes (increment)
{
var heureMinutes = document.getElementById("heure");
pos = heureMinutes.value.indexOf("h");
heure = heureMinutes.value.substring(0,pos);
minutes = heureMinutes.value.substring(pos+1,5);
var min = parseInt(minutes) + increment;
if (min > maxMin) min = minMin;
if (min < minMin) min = maxMin;
if (min < 10) min = "0"+min;
hM = String(heure) +"h"+ String(min);
heureMinutes.value = hM;
}
</script>
<!-- commentaire -->
<span id="horloge">
<a href="#" onclick="javascript:changerHeures(-1)" class="ticker" 
title="cliquer pour diminuer les heures">&nbsp;&lt;&nbsp;</a> h
<a href="#" onclick="javascript:changerHeures(+1)" class="ticker"
 title="cliquer pour augmenter les heures">&nbsp;&gt;&nbsp;</a> 
&nbsp;
<a href="#" onclick="javascript:changerMinutes(-5)" class="ticker"
 title="cliquer pour diminuer les minutes">&nbsp;&lt;&nbsp;</a> m
<a href="#" onclick="javascript:changerMinutes(+5)" class="ticker"
 title="cliquer pour augmenter les minutes">&nbsp;&gt;&nbsp;</a>
</span>