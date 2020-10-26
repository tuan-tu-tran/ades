This folder contains assets used to build a distributable Docker image of the application.

The main script is the [build.sh](build.sh) script, which:
- builds and tags the base run time image from the [parent folder](..)
- then builds the distributable image described by the [Dockerfile](Dockerfile)

That image uses multi-stage build to :
- first checkout the code from source `.git` folder
- then copies the checked out source code to the base runtime image.

It also sets up some file permissions: the application needs to write to:
  - `/ades/app/cache` : Symfony cache 
  - `/ades/app/logs` : Symfony logs
  - `/ades/local` : the application persistant files

These folders need to be writeable for `www-data` the user running the application, as defined in the base image.

Also, since the `/ades/local` folder will usually be mounted, it will take the host's permissions and so we reset the permissions in the entrypoint.

For installation instructions of the app, see the [Installation wiki](/../../wiki/Installation).
