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

namespace EducAction\AdesBundle;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * This class wraps a FlashBagInterface in a way that
 * each key stores only 1 values
 */
class FlashBagWrapper
{
    private $bag;

    public function __construct(FlashBagInterface $bag)
    {
        $this->bag=$bag;
    }

    public function peek($key, $default=NULL)
    {
        return $this->unserialize($this->bag->peek($key), $default);
    }

    public function get($key, $default=NULL)
    {
        return $this->unserialize($this->bag->get($key), $default);
    }

    public function set($key, $value)
    {
        return $this->bag->set($key, serialize($value));
    }

    public function clear()
    {
        return $this->bag->clear();
    }

    private function unserialize($value, $default)
    {
        if($value) {
            return unserialize($value[0]);
        } else {
            return $default;
        }
    }

}

