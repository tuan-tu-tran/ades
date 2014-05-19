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

<ul class="menu_facts <?php echo $class?>" id="<?php echo $id?>">
<?php foreach($tree as $label=>$value):?><li>
        <?php if (is_array($value)):?>
            <div class="top_label with_children"><?php echo Html::Encode($label)?></div>
            <div class="filler"></div>
            <ul>
                <?php foreach($value as $sublabel => $url):?>
                    <li>
                        <?php echo Html::Encode($sublabel)?>
                        <a href="<?php echo Html::Encode($url)?>"></a>
                    </li>
                <?php endforeach?>
            </ul>
        <?php elseif (is_string($value)):?>
            <div class="item top_label">
                <?php echo Html::Encode($label)?>
                <a href="<?php echo Html::Encode($value)?>"></a>
            </div>
        <?php else:?>
            <?php throw new Exception("value of label '$label' must be string or array. Got '".gettype($value)."' instead (".var_export($value, true).")");?>
        <?php endif?>
    </li><?php endforeach?>
</ul>
