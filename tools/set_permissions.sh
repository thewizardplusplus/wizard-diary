#!/bin/bash

cd `dirname "$0"`/..

find . -type f -exec chmod 0444 {} \;
find . -type d -exec chmod 0555 {} \;
