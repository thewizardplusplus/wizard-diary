import re
import logging
import collections
import itertools

from . import log

SPECIAL_ISSUE = 'прочее'

_ISSUE_MARK_PATTERN = re.compile(r'issue #\d+(?:, issue #\d+)*:', re.IGNORECASE)

def process_git_history(commits, verbose):
    log.log(logging.INFO, 'process the git history')

    data = collections.defaultdict(lambda: collections.defaultdict(list))
    for commit in commits:
        date = commit.timestamp.date()
        for issue_mark, messages in _process_commit_message(
            commit.hash,
            commit.message,
            verbose,
        ).items():
            data[date][issue_mark].extend(messages)

    return data

def unique_git_history(data, verbose):
    log.log(logging.INFO, 'unique the git history')

    unique_data = collections.defaultdict(dict)
    for date, issues_marks in data.items():
        if verbose:
            log.log(logging.DEBUG, 'unique the git history for {}'.format(
                log.ansi('magenta', date.strftime('%Y-%m-%d')),
            ))

        for issue_mark, messages in issues_marks.items():
            if verbose:
                log.log(logging.DEBUG, 'unique the git history for {}'.format(
                    log.ansi('blue', issue_mark),
                ))

            unique_data[date][issue_mark] = list(_unique_everseen(messages))

    return unique_data

def _process_commit_message(commit_hash, message, verbose):
    if verbose:
        log.log(logging.DEBUG, 'process the {} commit'.format(
            log.ansi('yellow', commit_hash),
        ))

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
