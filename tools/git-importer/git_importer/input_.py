import logging
import datetime

import git
import termcolor
import tzlocal

from . import log

class Commit:
    def __init__(self, hash_, timestamp, message):
        self.hash = hash_
        self.timestamp = timestamp
        self.message = message

def input_git_history(
    repository_path,
    revisions_specifier,
    start_timestamp,
    verbose,
):
    log.log(logging.INFO, 'input the git history')

    return [
        _input_commit(commit, verbose)
        for commit in git.Repo(repository_path).iter_commits(
            revisions_specifier,
            **({} if start_timestamp is None else {'after': start_timestamp}),
        )
    ]

def _input_commit(commit, verbose):
    commit_hash = str(commit)[:7]
    if verbose:
        log.log(logging.DEBUG, 'input the {} commit'.format(
            termcolor.colored(commit_hash, 'yellow'),
        ))

    return Commit(
        commit_hash,
        tzlocal.get_localzone().localize(
            datetime.datetime.fromtimestamp(commit.authored_date),
            is_dst=None,
        ),
        commit.message,
    )
