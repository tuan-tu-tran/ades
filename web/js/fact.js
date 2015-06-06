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
                var _tbFilterFocus=function(){
                        this.value="";
                        $(this).unbind("focus", _tbFilterFocus);
                };
                var _options = $("#lbAllStudents > option");
                var _lbAllStudents = $("#lbAllStudents");
                $("#tbFilter").focus(_tbFilterFocus).bind("change keyup", function(){
                        var text=this.value.trim();
                        _lbAllStudents.empty().scrollTop(0);
                        if (text.length>0){
                                var re=new RegExp(text,"i");
                                _options.each(function(i,o){
                                        var add = re.test(o.text);
                                        console.log("'"+o.text+"' matches '"+text+"' : "+add);
                                        if(add){
                                                _lbAllStudents.append(o);
                                        }
                                })
                        }else{
                                _options.each(function(i,o){
                                        _lbAllStudents.append(o);
                                })
                        }
                });
        });
})();

