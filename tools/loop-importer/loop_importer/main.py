import sys

from . import logger
from . import cli
from . import output

def main():
    try:
        options = cli.parse_options()
        logger.init_logger(options.verbose)

        import_representation = 'dummy'

        output.copy_import_representation(import_representation)
        if options.output is not None:
            output.output_import_representation(options.output, import_representation)
    except Exception as exception:
        sys.exit('error: ' + str(exception))
