import sys
import dataclasses
from typing import List

import dataclasses_json
import termcolor

from . import logger
from . import cli
from . import output
from . import db
from . import models

_MAX_COUNT = 2

def _debug_log(prefix: str, debug_data: str):
    logger.get_logger().debug(prefix + termcolor.colored(debug_data, 'green'))

def _debug_log_items(
    prefix: str,
    item_cls: dataclasses_json.DataClassJsonMixin,
    items: List[dataclasses_json.DataClassJsonMixin],
):
    _debug_log(prefix, item_cls.schema().dumps(items, many=True))

def main():
    try:
        options = cli.parse_options()
        logger.init_logger(options.verbose)

        habits = db.load_habits_from_db(options.db)
        _debug_log_items('some loaded habits (given in part): ', models.Habit, [
            dataclasses.replace(habit, repetitions=habit.repetitions[:_MAX_COUNT])
            for habit in habits[:_MAX_COUNT]
        ])

        import_representation = 'dummy'

        output.copy_import_representation(import_representation)
        if options.output is not None:
            output.output_import_representation(options.output, import_representation)
    except Exception as exception:
        sys.exit('error: ' + str(exception))
