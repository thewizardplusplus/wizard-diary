import re
import collections
import itertools

import termcolor

from . import logger

SPECIAL_ISSUE = 'прочее'

_ISSUE_MARK_PATTERN = re.compile(r'issue #\d+(?:, issue #\d+)*:', re.IGNORECASE)

def process_git_history(commits):
    logger.get_logger().info('process the git history')

    data = collections.defaultdict(lambda: collections.defaultdict(list))
    for commit in commits:
        date = commit.timestamp.date()
        for issue_mark, messages in _process_commit_message(
            commit.hash,
            commit.message,
        ).items():
            data[date][issue_mark].extend(messages)

    return data

def unique_git_history(data):
    logger.get_logger().info('unique the git history')

    unique_data = collections.defaultdict(dict)
    for date, issues_marks in data.items():
        logger.get_logger().debug(
            'unique the git history for ' \
                + termcolor.colored(date.strftime('%Y-%m-%d'), 'magenta'),
        )

        for issue_mark, messages in issues_marks.items():
            logger.get_logger().debug(
                'unique the git history for ' \
                    + termcolor.colored(issue_mark, 'blue'),
            )

            unique_data[date][issue_mark] = list(_unique_everseen(messages))

    return unique_data

def _process_commit_message(commit_hash, message):
    logger.get_logger().debug(
        'process the %s commit',
        termcolor.colored(commit_hash, 'yellow'),
    )

    message = message.lstrip().split('\n')[0].rstrip()
    if len(message) == 0 \
        or message.startswith('Merge branch') \
        or message.startswith('Merge the branch'):
        return {}

    issue_mark_match = _ISSUE_MARK_PATTERN.match(message)
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

# https://docs.python.org/3/library/itertools.html#itertools-recipes
def _unique_everseen(iterable):
    seen = set()
    for element in itertools.filterfalse(seen.__contains__, iterable):
        seen.add(element)
        yield element
