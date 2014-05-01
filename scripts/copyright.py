#!/usr/bin/env python
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

copyright="Copyright (c) 2014 Educ-Action"

full_copyright="""
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


import re
def php(content):
	m=re.match("^<\?php *\n", content)
	if m!=None:
		content=content[len(m.group(0)):]
		new_content=m.group(0)
		close_php=False
	else:
		new_content="<?php\n"
		close_php=True
	new_content+="""/**
 * """+copyright+"""
"""+"".join([" * "+l+"\n" for l in full_copyright.splitlines()])+"""*/
"""
	if close_php:
		new_content+="?>"
	new_content+=content
	return new_content


import sys

copyrighter_by_ext={
	"php":php,
}
for fname in sys.argv[1:]:
	try:
		ext=fname.split(".")[-1]
		if ext not in copyrighter_by_ext:
			print "could not determine file type of",fname
			continue
		with open(fname) as fh:
			content=fh.read()
		if copyright not in content:
			copyright_content=copyrighter_by_ext[ext](content)
			with open(fname,"w") as fh:
				fh.write(copyright_content)
			print "added copyright to",fname
	except:
		print "error while processing file",fname
		raise
