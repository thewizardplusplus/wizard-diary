import itertools
import sys
import operator
import logging

import xerox

from . import cli
from . import log
from . import input_
from . import process

def format_date(date):
    return date.strftime('%Y-%m-%d')

def get_dummy_generator(collection):
    return itertools.repeat(' ' * 4, len(collection) - 1)

def format_messages(project_indent, issue_mark, messages, verbose):
    if verbose:
        log.log(logging.DEBUG, 'format the git history for {}'.format(
            log.ansi('blue', issue_mark),
        ))

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
    return int(pair[0][7:]) if pair[0] != process.SPECIAL_ISSUE else sys.maxsize

def format_issues_marks(project, date, issues_marks, verbose):
    formatted_date = format_date(date)
    if verbose:
        log.log(logging.DEBUG, 'format the git history for {}'.format(
            log.ansi('magenta', formatted_date),
        ))

    return '## {}\n\n```\n{}\n```'.format(formatted_date, '\n\n'.join(
        format_messages(project_indent, issue_mark, messages, verbose)
        for project_indent, (issue_mark, messages) in zip(
            itertools.chain(['{}, '.format(project)], get_dummy_generator(
                issues_marks,
            )),
            sorted(issues_marks.items(), key=get_issue_mark_key),
        )
    ))

def format_git_history(project, data, verbose):
    log.log(logging.INFO, 'format the git history')

    return '# {}\n\n{}\n'.format(project, '\n\n'.join(
        format_issues_marks(project, date, issues_marks, verbose)
        for date, issues_marks in sorted(
            data.items(),
            key=operator.itemgetter(0),
        )
    ))

def copy_git_history(representation):
    log.log(logging.INFO, 'copy the git history')

    xerox.copy(representation)

def output_git_history(output_path, representation):
    log.log(logging.INFO, 'output the git history')

    with open(output_path + '.md', 'w') as output_file:
        output_file.write(representation)

def main():
    try:
        log.init_log()

        options = cli.parse_options()
        history = input_.input_git_history(
            options.repo,
            options.revs,
            options.start,
            options.verbose,
        )
        data = process.process_git_history(history, options.verbose)
        unique_data = process.unique_git_history(data, options.verbose)
        representation = format_git_history(
            options.project,
            unique_data,
            options.verbose,
        )
        copy_git_history(representation)
        if options.output is not None:
            output_git_history(options.output, representation)
    except Exception as exception:
        sys.exit('error: {}'.format(exception))
