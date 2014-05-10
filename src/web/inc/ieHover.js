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
// this emulates the "LI:hover" CSS selector for IE
// requires a "li.hover" duplicate selector in the stylesheet
function IEpatchLiHover(name) {
    var ulList = document.getElementsByTagName("UL");
    for (var j = 0; j < ulList.length; j++) {
        //~ if (ulList[j].className == name) {
        if (ulList[j].className.indexOf(name) >= 0) {
            var liList = ulList[j].getElementsByTagName("LI");
            for (var i = 0; i < liList.length; i++) {
                // normal code
                //~ liList[i].setAttribute("onmouseover", "this.className='hover'");
                //~ liList[i].setAttribute("onmouseout", "this.className=''");
                // IE code
                liList[i].onmouseover = function() { this.className='hover' };
                liList[i].onmouseout = function() { this.className='' };
            }
        }
    }
}
//~ IEpatchLiHover("navlist"); // applies the patch to every UL.navlist
window.onload = function() { IEpatchLiHover("navlist") };