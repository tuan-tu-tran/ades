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
import sys
import optparse
import re

parser=optparse.OptionParser(description="check that the given files on the command line (or from stdin separated by newlines if none given on the command line) contain a copyright notice")
parser.add_option("-x","--exclude",metavar="FILE",default="scripts/no_copyright.txt",help="the file that defines files that must not contain copyright notice", dest="exclude")
options,args=parser.parse_args()
retval=0
if len(args)==0:
	file_list=sys.stdin.readlines()
else:
	file_list=args
with open(options.exclude) as fh:
	regexes=[ l.strip() for l in fh if not l.startswith("#") and l.strip()!="" ]

for f in file_list:
	f=f.strip()
	keep_going=True
	for rx in regexes:
		if re.search(rx,f):
			print "no copyright required for",f
			keep_going=False
			break
	if not keep_going:
		continue
	with open(f) as fh:
		content=fh.read()
	if copyright not in content:
		print "no copyright in",f
		retval=1
sys.exit(retval)
