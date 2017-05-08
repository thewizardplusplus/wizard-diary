import argparse
import datetime
import itertools
import collections
import re
import sys
import operator
import logging

import parsedatetime
import tzlocal
import git
import xerox

class Commit:
    def __init__(self, hash_, timestamp, message):
        self.hash = hash_
        self.timestamp = timestamp
        self.message = message

    def __repr__(self):
        return str(self.__dict__)

LOCAL_TIME_ZONE = tzlocal.get_localzone()
ISSUE_MARK_PATTERN = re.compile(r'issue #\d+(?:, issue #\d+)*:', re.IGNORECASE)
SPECIAL_ISSUE = 'прочее'
ANSI_CODES = {
    'bold': '1',
    'black': '30',
    'green': '32',
}

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
        help='a start timestamp of the repository log ' \
            + 'in ISO 8601 or human-readable formats',
    )
    parser.add_argument('-p', '--project', required=True, help='project name')
    parser.add_argument('-o', '--output', help='output path')
    parser.add_argument(
        '-V',
        '--verbose',
        action='store_true',
        help='verbose logging',
    )

    return parser.parse_args()

def read_git_history(repository_path, revisions_specifier, start_timestamp):
    logging.info('read git history')

    return [
        Commit(
            str(commit)[:7],
            LOCAL_TIME_ZONE.localize(
                datetime.datetime.fromtimestamp(commit.authored_date),
                is_dst=None,
            ),
            commit.message,
        )
        for commit in git.Repo(repository_path).iter_commits(
            revisions_specifier,
            **({} if start_timestamp is None else {'after': start_timestamp}),
        )
    ]

def process_commit_message(message):
    message = message.lstrip().split('\n')[0].rstrip()
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
    logging.info('process git history')

    data = collections.defaultdict(lambda: collections.defaultdict(list))
    for commit in commits:
        date = commit.timestamp.date()
        for issue_mark, messages in process_commit_message(
            commit.message,
        ).items():
            data[date][issue_mark].extend(messages)

    return data

# https://docs.python.org/3/library/itertools.html#itertools-recipes
def unique_everseen(iterable):
    seen = set()
    for element in itertools.filterfalse(seen.__contains__, iterable):
        seen.add(element)
        yield element

def unique_git_history(data):
    logging.info('unique git history')

    unique_data = collections.defaultdict(dict)
    for date, issues_marks in data.items():
        for issue_mark, messages in issues_marks.items():
            unique_data[date][issue_mark] = list(unique_everseen(messages))

    return unique_data

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
    logging.info('format git history')

    return '# {}\n\n'.format(project) + '\n\n'.join(
        '## {}\n\n```\n{}\n```'.format(
            date.strftime('%Y-%m-%d'),
            format_issues_marks(project, issues_marks),
        )
        for date, issues_marks in sorted(
            data.items(),
            key=operator.itemgetter(0),
        )
    )

def copy_git_history(representation):
    logging.info('copy git history')

    xerox.copy(representation)

def output_git_history(output_path, representation):
    logging.info('output git history')

    with open(output_path + '.md', 'w') as output_file:
        output_file.write(representation + '\n')

def ansi(code, text):
    return '\x1b[{}m{}\x1b[m'.format(ANSI_CODES.get(code, code), text)

def main():
    try:
        logging.basicConfig(
            format=' '.join([
                ansi('black', '%(asctime)s'),
                ansi('green', '[%(levelname)s]'),
                ansi('bold', '%(message)s'),
            ]),
            level=logging.INFO,
        )

        options = parse_options()
        history = read_git_history(options.repo, options.revs, options.start)
        data = process_git_history(history)
        unique_data = unique_git_history(data)
        representation = format_git_history(options.project, unique_data)
        copy_git_history(representation)
        if options.output is not None:
            output_git_history(options.output, representation)
    except Exception as exception:
        sys.exit('error: {}'.format(exception))
