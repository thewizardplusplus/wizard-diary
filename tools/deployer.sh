#!/usr/bin/env bash

set -o errexit
set -o pipefail
set -o nounset

declare -r DEFAULT_REMOTE="origin"
declare -r DEFAULT_BRANCH="master"

echo "info: updating system packages..." >&2

export DEBIAN_FRONTEND=noninteractive
if apt-get update; then
	apt-get \
		--assume-yes \
		--option Dpkg::Options::="--force-confdef" \
		--option Dpkg::Options::="--force-confold" \
		upgrade
	apt-get --assume-yes --purge autoremove
	apt-get --assume-yes clean
else
	echo "warning: apt-get update failed (network or lock issue)" >&2
fi

echo "info: current commit: $(git rev-parse --short HEAD)" >&2
echo -e "info: current status:\n$(git status | sed 's/^/  /')" >&2

git stash push --include-untracked --message "Fixes for the production"
git fetch "${DEFAULT_REMOTE}" --prune
git reset --hard "${DEFAULT_REMOTE}/${DEFAULT_BRANCH}"

echo "info: current commit: $(git rev-parse --short HEAD)" >&2

if ! git stash pop --index; then
	echo -e "error: unable to apply the stashed changes:\n$(git status | sed 's/^/  /')" >&2
	exit 1
fi

docker compose pull
docker compose up --build --detach

echo -e "info: current container list:\n$(docker container list | sed 's/^/  /')" >&2
