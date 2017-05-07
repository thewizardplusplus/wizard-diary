#!/usr/bin/env python3.5

import argparse
import datetime
import itertools
import collections
import re
import sys
import operator

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
SPECIAL_ISSUE = 'прочее'

def parse_timestamp(value):
    try:
        timestamp = datetime.datetime.strptime(value, '%Y-%m-%d')
    except Exception:
        try:
            timestamp = datetime.datetime.strptime(value, '%Y-%m-%dT%H:%M:%S')
        except Exception:
            timestamp, status = parsedatetime.Calendar().parseDT(value)
            if status == 0:
                raise argparse.ArgumentTypeError(
                    'timestamp {} is incorrect'.format(value),
                )

    return LOCAL_TIME_ZONE.localize(timestamp, is_dst=None)

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
    parser.add_argument('-p', '--project', required=True, help='project name')
    parser.add_argument('-o', '--output', help='output path')

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
        return {SPECIAL_ISSUE: [message[0].lower() + message[1:]]}

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
        date = commit.timestamp.date()
        for issue_mark, messages in process_commit_message(
            commit.message,
        ).items():
            data[date][issue_mark].extend(messages)

    return data

def get_dummy_generator(collection):
    return itertools.repeat(' ' * 4, len(collection) - 1)

def format_messages(project_indent, issue_mark, messages):
    return '\n'.join(
        project_indent + issue_indent + message
        for project_indent, issue_indent, message in zip(
            itertools.chain([project_indent], get_dummy_generator(
                messages,
            )),
            itertools.chain(['{}, '.format(issue_mark)], get_dummy_generator(
                messages,
            )),
            messages,
        )
    )

def get_issue_mark_key(pair):
    return int(pair[0][7:]) if pair[0] != SPECIAL_ISSUE else sys.maxsize

def format_issues_marks(project, issues_marks):
    return '\n\n'.join(
        format_messages(project_indent, issue_mark, messages)
        for project_indent, (issue_mark, messages) in zip(
            itertools.chain(['{}, '.format(project)], get_dummy_generator(
                issues_marks,
            )),
            sorted(issues_marks.items(), key=get_issue_mark_key),
        )
    )

def format_git_history(project, data):
    return '\n\n'.join(
        '## {}\n\n```\n{}\n```'.format(
            timestamp.strftime('%Y-%m-%d'),
            format_issues_marks(project, issues_marks),
        )
        for timestamp, issues_marks in sorted(
            data.items(),
            key=operator.itemgetter(0),
        )
    )

if __name__ == '__main__':
    options = parse_options()
    history = read_git_history(options.repo, options.revs, options.start)
    data = process_git_history(history)
    representation = format_git_history(options.project, data)
    print(representation)
