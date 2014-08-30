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

namespace EducAction\AdesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AccessController
{
    private $request;
    private $router;
    public function __construct(Request $request, $router)
    {
        $this->request = $request;
        $this->router = $router;
    }

    public function redirectLogin()
    {
        $this->request->getSession()->clear();
        $url=$this->router->generate("educ_action_ades_login");
        return new RedirectResponse($url);
    }

    public function unauthorized()
    {
        return new RedirectResponse("unauthorized.php");
    }
}


