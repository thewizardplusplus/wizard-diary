import datetime

import git
import termcolor
import tzlocal

from . import logger

class Commit:
    def __init__(self, hash_, timestamp, message):
        self.hash = hash_
        self.timestamp = timestamp
        self.message = message

def input_git_history(repository_path, revisions_specifier, start_timestamp):
    logger.get_logger().info('input the git history')

    return [
        _input_commit(commit)
        for commit in git.Repo(repository_path).iter_commits(
            revisions_specifier,
            **({} if start_timestamp is None else {'after': start_timestamp}),
        )
    ]

def _input_commit(commit):
    logger.get_logger().debug(
        'input the %s commit',
        termcolor.colored(commit.hexsha, 'yellow'),
    )

    return Commit(
        commit.hexsha,
        tzlocal.get_localzone().localize(
            datetime.datetime.fromtimestamp(commit.authored_date),
            is_dst=None,
        ),
        commit.message,
    )
