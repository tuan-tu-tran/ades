This folder contains assets to build the base image of the app container.
Mainly it's a Dockerfile that builds an apache server with the correct php dependencies and versions.

This base image can be used for development: at runtime, mount the source on `/ades`.
Alternatively, it can be used as base image to build the distributable image: create a new image based in this one where you add the source code.
See also the `dist` folder.
