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
By default, create and deploy an archive via ftp.
A php extraction script is deployed along with the archive.
After deployment, if url is present in the config, wget is called
to launch the extraction script.
Use the -c option to create an empty config file.
Use the -a option to just create an archive (git required).
"""

usage="""
	%prog [options] CONFIG_INI_FILE [ ARCHIVE_FILE ]
	%prog [options] -c CONFIG_INI_FILE [ SECTION [ ... ] ]
	%prog [options] -a [ ARCHIVE_FILE ]
"""

config_template="""[%s]
host=
user=
password=
path=

; optional url of the website
url=
"""

import os
import ftplib
import optparse
import ConfigParser
import logging
import sys
logging.basicConfig(format="%(message)s", level=logging.INFO)
logger=logging.getLogger()

def main():
	parser=optparse.OptionParser(usage=usage.rstrip(), description=desc.strip())
	parser.add_option("-c","--create-config",
			help="Create an empty config file with the given sections (defaults to 'deploy' if none given). Cannot be used with -a.",
			action="store_true", dest="create")

	parser.add_option("-a","--archive-only",
			help="Just create the archive without trying to upload it. Requires git to be installed and fails if not at the project root dir.",
			dest="archive", action="store_true")

	parser.add_option("-f","--force",
			help="Force overwrite of the archive file (or the config file with -c).",
			action="store_true", dest="force")

	parser.add_option("-r","--ref", metavar="<tree-ish>",
			help="The tree-ish ref to create the archive from (passed to git archive). Default is HEAD (no effect with -c or if ARCHIVE_FILE is given without -a).",
			dest="ref", default="HEAD")

	parser.add_option("-s","--section", metavar="SECTION",
			help="The section to read the deploy config from. Default is 'deploy' (no effect with -a or -c).",
			dest="section", default="deploy")

	parser.add_option("-x","--extract-script", metavar="FILENAME",
			help="The path to the extract script. Default is scripts/extract.php (no effect wih -a or -c).",
			dest="extract", default=None)

	parser.add_option("-k","--keep",
			help="Keep the created archive after upload. Default behavior is to delete the archive after the upload if it was created (no effect with -a or -c)",
			dest="keep", action="store_true")

	option,args=parser.parse_args()

	if option.create and option.archive:
		parser.error("options -c and -a are mutually exclusive")

	if option.create:
		if len(args)==0:
			parser.error("-c requires a config filename")
		path=args[0]
		sections=args[1:]
		create_config(path, sections, option)
	elif option.archive:
		if len(args)>1:
			parser.error("-a takes only 1 optional archive filename")
		if len(args)==0:
			archive="archive.zip"
		else:
			archive=args[0]
		create_archive(archive, option)
		sys.exit(0)
	else:
		if len(args)==0:
			parser.error("missing CONFIG_INI_FILE argument")
		elif len(args)>2:
			parser.error("1 or 2 arguments exepected, got "+len(args))
		else:
			config_file=args[0]
			if len(args)==1:
				archive=None
			elif len(args)==2:
				archive=args[1]
			deploy(config_file, archive, option)

def create_config(path, sections, option):
	if len(sections) == 0:
		sections.append("deploy")
	if os.path.exists(path) and not option.force:
		logger.error("refusing to overwrite %s (use -f to force).", path)
		sys.exit(1)
	else:
		if os.path.exists(path):
			logger.info("overwriting config file %s with sections %s", path, ", ".join(sections))
		else:
			logger.info("creating config file %s with sections %s", path, ", ".join(sections))
		with open(path,"w") as fh:
			fh.write("\n".join([ config_template%s for s in sections ]))
		sys.exit(0)

def create_archive(archive, option):
	if os.path.exists(archive) and not option.force:
		logger.error("refusing to overwrite %s (use -f to force)", archive)
		sys.exit(1)
	if not os.path.isdir(".git"):
		logger.error("Cannot create archive: it seems you are not at the root of the git folder")
		sys.exit(1)
	if os.path.exists(archive):
		logger.info("%s already exists. Removing it...", archive)
		os.remove(archive)
	logger.info("creating %s from %s", archive, option.ref)
	os.system("git archive --format zip -o %s %s"%(archive, option.ref))
	if not os.path.exists(archive):
		logger.error("error: could not create %s", archive)
		sys.exit(1)

def deploy(config_file, archive, option):
	config=ConfigParser.ConfigParser()
	config.read([config_file])
	section=option.section
	if not os.path.exists(config_file):
		logger.error("config file %s does not exist (use -c to create one).",config_file)
		sys.exit(1)
	if not config.has_section(section):
		logger.error("no section '%s' in config (use -s to specify a section to read from).",section)
		sys.exit(1)
	logger.info("reading config section '%s' from file %s", section, config_file)
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
		logger.error("config error: "+str(e))
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

		if archive==None:
			archive_created=True
			archive="archive.zip"
			create_archive(archive, option)
		else:
			archive_created=False
			if not os.path.exists(archive):
				logger.error("archive file %s does not exist"%archive)
				sys.exit(1)

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

			logger.info("ftp: cd %s", path)
			ftp.cwd(path)

			logger.info("ftp: upload %s to archive.zip",archive)
			with open(archive,"rb") as fh:
				ftp.storbinary("STOR archive.zip", fh)

			logger.info("ftp: cd web")
			ftp.cwd("web")
			logger.info("ftp: upload %s to extract.php",extract)
			with open(extract,"r") as fh:
				ftp.storlines("STOR extract.php", fh)

			quiting=True
			ftp.quit()
		except:
			import traceback
			t,e,tb=sys.exc_info()
			logger.error("ftp error: %s", e)
			logger.debug(traceback.format_exc())
			if not quiting:
				sys.exit(1)

		if archive_created:
			if not option.keep:
				logger.info("removing created %s", archive)
				os.remove(archive)
			else:
				logger.info("created %s was kept, as requested", archive)

		if config.has_option(section, "url"):
			logger.info("")
			url=config.get(section, "url").rstrip("/")+"/extract.php"
			logger.info("wget %s", url)
			if os.system("wget %s -O-" %url)!=0:
				logger.error("error: it seems we could not call the extract script at %s", url)
				sys.exit(1)
		sys.exit(0)

main()
raise RuntimeError("main did not call sys.exit")

