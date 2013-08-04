#!/bin/bash

if [ -z $1 ]; then
	echo "Usage: $0 <tag_name>"
	exit 1
fi

TAG=$1
echo "Creating Git tag: $TAG"
git tag -af "$TAG"

DIRNAME=$(basename $(pwd))
FILENAME="$DIRNAME""_""$TAG"".tgz"
echo "Creating release file: $FILENAME"
tar -czf $FILENAME \
  --exclude-vcs \
  --exclude='.gitmodules' \
  --exclude='_test' \
  --exclude='*.sh' \
  --exclude='*.tar' \
  --exclude='*.tgz' \
  ../$DIRNAME
