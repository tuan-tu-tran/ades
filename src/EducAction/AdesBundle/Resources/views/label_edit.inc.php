<?php
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

use EducAction\AdesBundle\View;
use EducAction\AdesBundle\Html;
?>

<div style="float:left;width:100%; margin-top:1em; margin-bottom:1em">
    <label>Etiquettes</label>
    <div style="float:left">
        <div style="display:none; margin-bottom:5px" id="divLabels">
        </div>
        <input class="nomargin" type="text" id="tbLabel"/><button class="nomargin" id="bLabelAdd" onclick="return false;">+</button>
    </div>
    <div id="labelView" style="display:none">
        <div style="display:inline-block;">
            <div style="display:none; margin-right:0.3em; border:solid 1px black; padding:3px">
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(function($){
        function LabeList(div){
            var _labels=[];
            var _labelView = $("#labelView").html();
            this.contains = function(label){
                return _labels.indexOf(label) > -1;
            }

            var __add;
            __add=function(label, no_animate){
                if (typeof(label)=="string"){
                    _labels.push(label);
                    var view=$(_labelView);
                    var inner=view.find("div").text(label);
                    view.appendTo(div.show());
                    if(no_animate){
                        inner.show();
                    } else {
                        inner.show("slide", {direction:"left"});
                    }
                }else {
                    $(label).each(function(i,l){
                        __add(l);
                    })
                }
            }
            this.add=__add;
        }

        var labelList = new LabeList($("#divLabels"));

        labelList.add(["test","foo"], true);
        $("#bLabelAdd").click(function(e){
            var textbox=$("#tbLabel");
            var label = textbox.val();
            if(!label){
                alert("Veuillez entrer un label.");
            } else if (labelList.contains(label)){
                alert("Ce label est déjà affecté à ce fait.");
            } else {
                labelList.add(label);
                textbox.val("");
            }
        });
    });
</script>
