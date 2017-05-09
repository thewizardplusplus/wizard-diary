import logging
import operator
import itertools
import sys

from . import log
from . import process

def format_git_history(project, data, verbose):
    log.log(logging.INFO, 'format the git history')

    return '# {}\n\n{}\n'.format(project, '\n\n'.join(
        _format_issues_marks(project, date, issues_marks, verbose)
        for date, issues_marks in sorted(
            data.items(),
            key=operator.itemgetter(0),
        )
    ))

def _format_issues_marks(project, date, issues_marks, verbose):
    formatted_date = date.strftime('%Y-%m-%d')
    if verbose:
        log.log(logging.DEBUG, 'format the git history for {}'.format(
            log.ansi('magenta', formatted_date),
        ))

    return '## {}\n\n```\n{}\n```'.format(formatted_date, '\n\n'.join(
        _format_messages(project_indent, issue_mark, messages, verbose)
        for project_indent, (issue_mark, messages) in zip(
            itertools.chain(['{}, '.format(project)], _get_dummy_generator(
                issues_marks,
            )),
            sorted(issues_marks.items(), key=_get_issue_mark_key),
        )
    ))

def _format_messages(project_indent, issue_mark, messages, verbose):
    if verbose:
        log.log(logging.DEBUG, 'format the git history for {}'.format(
            log.ansi('blue', issue_mark),
        ))

    return '\n'.join(
        project_indent + issue_indent + message
        for project_indent, issue_indent, message in zip(
            itertools.chain([project_indent], _get_dummy_generator(
                messages,
            )),
            itertools.chain(['{}, '.format(issue_mark)], _get_dummy_generator(
                messages,
            )),
            messages,
        )
    )

def _get_dummy_generator(collection):
    return itertools.repeat(' ' * 4, len(collection) - 1)

def _get_issue_mark_key(pair):
    return int(pair[0][7:]) if pair[0] != process.SPECIAL_ISSUE else sys.maxsize
