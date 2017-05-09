import sys

from . import log
from . import cli
from . import input_
from . import process
from . import format_
from . import output

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
        output.copy_git_history(representation)
        if options.output is not None:
            output.output_git_history(options.output, representation)
    except Exception as exception:
        sys.exit('error: {}'.format(exception))
