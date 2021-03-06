<?php
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

use EducAction\AdesBundle\View;

?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
		<title><?php View::Block("title") ?></title>
		<link media="screen" rel="stylesheet" href="config/screen.css" type="text/css">
		<link media="print" rel="stylesheet" href="config/print.css" type="text/css">
        <script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
        <script type="text/javascript" src="js/base.js"></script>
		<!--[if IE]>
		<link href="css/facelist_ie.css" rel="stylesheet" type="text/css" />
		<![endif]-->
		<?php View::Block("post_head");?>
	</head>
	<body>
		<?php View::Block("body");?>
	</body>
</html>

