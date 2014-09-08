#!/bin/bash

cd `dirname "$0"`/..

find . -type f -exec chmod 0444 {} \;
find . -type d -exec chmod 0555 {} \;

chmod 0777 assets
chmod 0777 protected/runtime
chmod 0777 backups
