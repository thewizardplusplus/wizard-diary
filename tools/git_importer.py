#!/usr/bin/env python3.5

import argparse
import datetime

import parsedatetime
import tzlocal
import git

class Commit:
    def __init__(self, timestamp, message):
        self.timestamp = timestamp
        self.message = message

    def __repr__(self):
        return str(self.__dict__)

LOCAL_TIME_ZONE = tzlocal.get_localzone()

def parse_timestamp(value):
    try:
        timestamp = datetime.datetime.strptime(value, '%Y-%m-%dT%H:%M:%S%z')
    except Exception:
        timestamp, status = parsedatetime.Calendar().parseDT(value)
        if status == 0:
            raise argparse.ArgumentTypeError(
                'timestamp {} is incorrect'.format(value),
            )

        timestamp = LOCAL_TIME_ZONE.localize(timestamp, is_dst=None)

    return timestamp

def parse_options():
    parser = argparse.ArgumentParser(
        formatter_class=argparse.ArgumentDefaultsHelpFormatter,
    )
    parser.add_argument(
        '-r',
        '--repo',
        default='.',
        help='path to the repository',
    )
    parser.add_argument(
        '-R',
        '--revs',
        default='HEAD',
        help='revisions specifier in the git-rev-parse command format',
    )
    parser.add_argument(
        '-s',
        '--start',
        type=parse_timestamp,
        required=True,
        help='a start timestamp of the repository log ' \
            + 'in ISO 8601 or human-readable formats',
    )

    return parser.parse_args()

def read_git_history(repository_path, revisions_specifier, start_timestamp):
    return [
        Commit(
            LOCAL_TIME_ZONE.localize(
                datetime.datetime.fromtimestamp(commit.authored_date),
                is_dst=None,
            ),
            commit.message,
        )
        for commit in git.Repo(repository_path).iter_commits(
            revisions_specifier,
            after=start_timestamp,
        )
    ]

if __name__ == '__main__':
    options = parse_options()
    history = read_git_history(options.repo, options.revs, options.start)
    print(history)
