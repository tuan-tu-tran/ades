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
var register_detention_places, set_detention_list_id;
(function(){
        var _freePlacesByDentention={};
        register_detention_places=function(id, places){
                _freePlacesByDentention[id]=places;
        }

        var _detentionListId=null;
        var _editing=false;
        set_detention_list_id=function(id, editing){
                _detentionListId=id;
                _editing=editing;
        }

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
                }).val(initFilter).keypress(function(e){
                        if(e.which==13){
                                if(this.value!=""){
                                        var visible=_lbAllStudents.children(":visible");
                                        if(visible.length==1){
                                                visible.click();
                                                this.value="";
                                        }else if(visible.length > 1){
                                                alert("Veuillez raffiner votre filtre");
                                        }else{
                                                alert("Aucun élève sélectionné pour ce filtre");
                                        }
                                }
                                e.preventDefault();
                                e.stopPropagation();
                        }
                });
                var _selectedCount = 0;
                var _hasDefaultStudent = $("input[name=ideleve]").val()>0;
                /**
                 * Return the number of free places for the given detention
                 * taking into account the currently selected students
                 */
                function freePlaces(detentionId)
                {
                        var free=_freePlacesByDentention[detentionId];
                        //the "current" student takes a spot, unless we're editing and we're selecting the initial detention
                        if(_hasDefaultStudent && !(_editing && _initialDetentionId==detentionId)){
                                free-=1;
                        }
                        free-=_selectedCount;
                        return free;
                }
                var _detentionList;
                var _initialDetentionId;
                var _lastDetentionId;
                if(_detentionListId){
                        _detentionList=$("#"+_detentionListId);
                        _initialDetentionId=_detentionList.val();
                        _detentionList.click(function(){
                                _lastDetentionId=_detentionList.val();
                        })
                        _detentionList.change(function(){
                                //allow the change only if the room constraint can be met
                                var newDetentionId=_detentionList.val();
                                var free=freePlaces(newDetentionId);
                                if(free<0){
                                        //the change would lead to overflow
                                        alert("La retenue sélectionnée ne peut pas accueillir le nombre d'élèves sélectionnés!");
                                        _detentionList.val(_lastDetentionId);
                                }
                        });
                }
                var lNoExtraStudent=$("#lNoExtraStudent");
                var lExtraStudents=$("#lExtraStudents");
                var _table=$("#tExtraStudents");
                var _lbAllStudents = $("#lbAllStudents").scrollTop(0);
                _lbAllStudents[0].selectedIndex=-1;
                _lbAllStudents.children().click(function(e){
                        var canAdd;
                        if(_detentionList){
                                //handle the limit of places in detention
                                var detentionId = _detentionList.val();
                                var free=freePlaces(detentionId);
                                if(free>0){
                                        canAdd=true;
                                }else{
                                        alert("Il n'y a plus assez de places pour cette retenue!");
                                        canAdd=false;
                                }
                        } else {
                                canAdd=true;
                        }
                        if(canAdd){
                        var option = $(this);
                        option.attr("selected","selected");
                        var row=$("<tr>");
                        row.addClass("clearButton").click(function(){
                                row.remove();
                                option.removeAttr("selected");
                                _selectedCount-=1;
                                if(_selectedCount==0){
                                        lNoExtraStudent.show();
                                        lExtraStudents.hide();
                                }
                        });
                        $("<td>").text(this.text).append(
                                $("<input type='hidden' name='extraStudentIds[]'/>").val(this.value)
                        ).appendTo(row);
                        _table.append(row);
                        _selectedCount+=1;
                        lNoExtraStudent.hide();
                        lExtraStudents.show();
                        }
                });
                function showOption(o, show){
                        if(show){
                                $(o).removeClass("hide");
                        }else{
                                $(o).addClass("hide");
                        }
                }
                $("#tbFilter").bind("change keyup", function(){
                        var text=this.value.trim();
                        var filters=[];
                        $.each(text.split(/(\s+)/), function(i,f){
                                if(f.trim()!=""){
                                        filters.push(f);
                                }
                        });
                        if (filters.length>0){
                                filters=$(filters).map(function(i, t){
                                        return new RegExp(t,"i");
                                });
                                var match=function(t){
                                        var m=true;
                                        $.each(filters, function(i,f){
                                                m=f.test(t);
                                                return m;
                                        })
                                        return m;
                                }
                                _lbAllStudents.children().each(function(i,o){
                                        showOption(o, match(o.text));
                                }).scrollTop(0);
                        }else{
                                _lbAllStudents.children().each(function(i,o){
                                        showOption(o, true);
                                });
                        }
                });
        });
})();

