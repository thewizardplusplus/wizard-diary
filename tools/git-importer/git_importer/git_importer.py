import sys
import logging

import xerox

from . import cli
from . import log
from . import input_
from . import process
from . import format_

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
        representation = format_.format_git_history(
            options.project,
            unique_data,
            options.verbose,
        )
        copy_git_history(representation)
        if options.output is not None:
            output_git_history(options.output, representation)
    except Exception as exception:
        sys.exit('error: {}'.format(exception))
