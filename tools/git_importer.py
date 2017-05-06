#!/usr/bin/env python3.5

import argparse
import datetime
import itertools
import collections
import re

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
ISSUE_MARK_PATTERN = re.compile(r'issue #\d+(?:, issue #\d+)*:', re.IGNORECASE)

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

def process_commit_message(message):
    message = message.strip()
    if len(message) == 0 \
        or message.startswith('Merge branch') \
        or message.startswith('Merge the branch'):
        return {}

    issue_mark_match = ISSUE_MARK_PATTERN.match(message)
    if issue_mark_match is None:
        return {'прочее': [message[0].lower() + message[1:]]}

    data = collections.defaultdict(list)
    message = message[len(issue_mark_match.group()):].strip()
    for issue_mark in (
        issue_mark.strip()
        for issue_mark in issue_mark_match.group()[:-1].lower().split(',')
    ):
        data[issue_mark].append(message)

    return data

def process_git_history(commits):
    data = collections.defaultdict(lambda: collections.defaultdict(list))
    for commit in commits:
        for issue_mark, messages in process_commit_message(
            commit.message,
        ).items():
            data[commit.timestamp][issue_mark].extend(messages)

    return data

if __name__ == '__main__':
    options = parse_options()
    history = read_git_history(options.repo, options.revs, options.start)
    data = process_git_history(history)
    print(data)
