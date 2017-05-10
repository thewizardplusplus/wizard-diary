import sys

from . import logger
from . import cli
from . import input_
from . import process
from . import format_
from . import output

def main():
    try:
        options = cli.parse_options()
        logger.init_logger(options.verbose)

        history = input_.input_git_history(
            options.repo,
            options.revs,
            options.start,
        )
        data = process.process_git_history(history)
        unique_data = process.unique_git_history(data)
        representation = format_.format_git_history(
            options.project,
            unique_data,
        )
        output.copy_git_history(representation)
        if options.output is not None:
            output.output_git_history(options.output, representation)
    except Exception as exception:
        sys.exit('error: ' + str(exception))
