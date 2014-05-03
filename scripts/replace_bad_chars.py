#!/usr/bin/env python
# -*- encoding: utf8 -*-
"""
Copyright (c) 2014 Educ-Action

This file is part of ADES.

ADES is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

ADES is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with ADES.  If not, see <http://www.gnu.org/licenses/>.
"""

import sys
for fname in sys.argv[1:]:
	with open(fname) as fh:
		s=fh.read()
	to_replace="\xef\xbf\xbd"
	if s.count(to_replace) > 0:
		print fname,":",s.count(to_replace),"occurences"
		with open(fname,"w") as fh:
			fh.write(s.replace(to_replace,"§"))
