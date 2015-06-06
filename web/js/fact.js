/**
 * Copyright (c) 2015 Tuan-Tu Tran
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
(function(){
        jQuery(function($){
                var initFilter="par nom et/ou par classe";
                var _tbFilterFocus=function(){
                        this.value="";
                        $(this).unbind("focus", _tbFilterFocus);
                };
                $("#tbFilter").focus(_tbFilterFocus).focusout(function(){
                        if (this.value==""){
                                this.value=initFilter;
                                $(this).focus(_tbFilterFocus);
                        }
                }).val(initFilter);
                var _table=$("#tExtraStudents");
                $("#lbAllStudents > option").click(function(){
                        var option = $(this);
                        option.attr("selected","selected");
                        var row=$("<tr>");
                        row.addClass("clearButton").click(function(){
                                row.detach();
                                option.removeAttr("selected");
                        });
                        $("<td>").text(this.text).appendTo(row);
                        _table.append(row);
                });
                function showOption(o, show){
                        if(show){
                                $(o).removeClass("hide");
                        }else{
                                $(o).addClass("hide");
                        }
                }
                var _lbAllStudents = $("#lbAllStudents").scrollTop(0);
                _lbAllStudents[0].selectedIndex=-1;
                $("#tbFilter").bind("change keyup", function(){
                        var text=this.value.trim();
                        if (text.length>0){
                                var re=new RegExp(text,"i");
                                _lbAllStudents.children().each(function(i,o){
                                        showOption(o, re.test(o.text));
                                }).scrollTop(0);
                        }else{
                                _lbAllStudents.children().each(function(i,o){
                                        showOption(o, true);
                                });
                        }
                });
        });
})();

