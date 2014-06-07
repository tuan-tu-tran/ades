/**
 * Copyright (c) 2014 Tuan-Tu TRAN
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
EducAction.Ades.TabPanel=new (function(){
	var $=jQuery;
	this.select=function(elem, id)
	{
		var tabs=$(elem).parent().next().children();
		if(tabs.filter("#"+id+":visible").length==0){
			tabs.filter(":visible").hide();
			tabs.filter("#"+id).show();
			$(elem).siblings().removeClass("tab-panel-selected");
			$(elem).addClass("tab-panel-selected");
		}
	}

	$(function(){
		$("div.tab-panel-panels").each(function(i,div){
			var max_height=0;
			$(div).children().each(function(j, panel){
				max_height=Math.max($(panel).outerHeight(true), max_height);
			}).outerHeight(max_height);
		});
	});
})();
