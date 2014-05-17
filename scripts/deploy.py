#!/usr/bin/env python
"""
Copyright (c) 2014 Tuan-Tu TRAN

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

desc="""
Create and deploy an archive via ftp.
A php extraction script is deployed along with the archive.
After deployment, if url is present in the config, wget is called
to launch the extraction script, unless the option -n is given.
Use the -c option to create an empty config file.
"""

import os
import ftplib
import optparse
import ConfigParser
import logging
import sys
logging.basicConfig(format="%(message)s", level=logging.INFO)
logger=logging.getLogger()

parser=optparse.OptionParser(usage="%prog [options] [CONFIG_INI_FILE]", description=desc.strip())
parser.add_option("-c","--create", help="create an empty config file", action="store_true", dest="create")
parser.add_option("-f","--force", help="force overwrite of the archive file (or the config file with -c)", action="store_true", dest="force")
parser.add_option("-r","--ref", help="the tree-ish ref to deploy (passed to git archive). Default is HEAD",metavar="<tree-ish>", dest="ref", default="HEAD")
parser.add_option("-s","--section", help="the section to read the deploy config from (default is deploy)",metavar="SECTION", dest="section", default="deploy")
parser.add_option("-x","--extract-script", help="the path to the extract script. (default is scripts/extract.php)", metavar="FILENAME", dest="extract", default=None)
parser.add_option("-k","--keep", help="don't delete the archive after upload", dest="keep", action="store_true")
parser.add_option("-a","--archive-only", help="just create the archive, don't try to upload it", dest="archive", action="store_true")
option,args=parser.parse_args()
if len(args)!=1 and ( option.create or not option.archive ):
	parser.error("wrong number of args. Use -h for help")

if option.create:
	path=args[0]
	if os.path.exists(path) and not option.force:
		logger.error("refusing to overwrite %s. use -f to force", path)
	else:
		if os.path.exists(path):
			logger.info("overwriting %s",path)
		with open(args[0],"w") as fh:
			fh.write("""[deploy]
host=
user=
password=
path=

; optional url of the website to launch wget after deploy
url=
""")

def create_archive():
	archive="archive.zip"
	if os.path.exists(archive) and not option.force:
		logger.error("refusing to overwrite %s. use -f to force", tarball)
		sys.exit(1)
	if os.path.exists(archive):
		logger.info("removing %s", archive)
		os.remove(archive)
	logger.info("creating %s from %s", archive, option.ref)
	os.system("git archive --format zip -o %s %s"%(archive, option.ref))
	if not os.path.exists(archive):
		logger.error("error: could not create %s", archive)
		sys.exit(1)
	return archive

if option.archive:
	create_archive()

if not option.create and not option.archive:
	if not os.path.isdir(".git"):
		logger.error("It seems you are not at the root of the git folder")
		sys.exit(1)

	config=ConfigParser.ConfigParser()
	config.read(args)
	section=option.section
	logger.info("reading config section '%s' from file %s", section, args[0])
	def getopt(value):
		if config.has_option(section, value):
			return config.get(section, value)

	try:
		host=config.get(section, "host")
		user=getopt("user")
		password=getopt("password")
		path=config.get(section,"path")
		url=getopt("url")
	except ConfigParser.NoSectionError, e:
		parser.error("config error: "+str(e))
	else:
		if not host:
			logger.error("error: no host provided. use -c to create template config")
			sys.exit(1)
		if not path:
			logger.error("error: no remote path provided. use -c to create template config")
			sys.exit(1)

		extract=option.extract
		if not extract:
			extract=os.path.join("scripts","extract.php")
		if not os.path.exists(extract):
			logger.error("could not find extract script %s. use -x to specify path", option.extract)
			sys.exit(1)

		
		archive=create_archive()

		ftpargs=[host]
		if user:
			ftpargs.append(user)
			if password:
				ftpargs.append(password)
				using="yes"
			else:
				using="no"
			logger.info("ftp: %s@%s using password? %s", user, host, using)
		else:
			logger.info("ftp: anonymous login at %s", host)
		try:
			quiting=False
			ftp=ftplib.FTP(*ftpargs, timeout=15)
			if not user:
				ftp.login()

			logger.info("change pwd to %s", path)
			ftp.cwd(path)

			logger.info("upload file to %s",archive)
			with open(tarball,"rb") as fh:
				ftp.storbinary("STOR %s"%archive, fh)

			logger.info("change pwd to web")
			ftp.cwd("web")
			logger.info("upload extract script %s to extract.php",extract)
			with open(extract,"r") as fh:
				ftp.storlines("STOR extract.php", fh)

			quiting=True
			ftp.quit()
		except:
			import traceback
			t,e,tb=sys.exc_info()
			logger.error("error: %s", e)
			logger.debug(traceback.format_exc())
			if not quiting:
				sys.exit(1)

		if not option.keep:
			os.remove(tarball)
