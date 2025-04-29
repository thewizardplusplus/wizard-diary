import sys
import dataclasses
import re
from typing import List
from itertools import islice

import dataclasses_json
import termcolor

from . import logger
from . import cli
from . import output
from . import db
from . import models
from . import processing

_MAX_COUNT = 2

def _debug_log(prefix: str, debug_data: str):
    logger.get_logger().debug(prefix + termcolor.colored(debug_data, 'green'))

def _debug_log_items(
    prefix: str,
    item_cls: dataclasses_json.DataClassJsonMixin,
    items: List[dataclasses_json.DataClassJsonMixin],
):
    _debug_log(prefix, item_cls.schema().dumps(items, many=True))

def _debug_log_habit_repetitions_by_date(
    prefix: str,
    habit_repetitions_by_date: models.HabitRepetitionsByDate,
    max_count: int = _MAX_COUNT,
):
    item_cls = models.HabitRepetitionsByDateItem
    items = (
        item_cls(habit_repetitions=habit_repetitions[:max_count], date=date)
        for date, habit_repetitions in islice(habit_repetitions_by_date.items(), max_count)
    )
    _debug_log_items(prefix, item_cls, sorted(items, key=lambda item: item.date))

def main():
    try:
        options = cli.parse_options()
        logger.init_logger(options.verbose)

        habits = db.load_habits_from_db(options.db)
        _debug_log_items('some loaded habits (given in part): ', models.Habit, [
            dataclasses.replace(habit, repetitions=habit.repetitions[:_MAX_COUNT])
            for habit in habits[:_MAX_COUNT]
        ])

        habit_repetitions_by_date = processing.group_habit_repetitions_by_date(habits)
        _debug_log_habit_repetitions_by_date(
            'habit repetitions by date (given in part): ',
            habit_repetitions_by_date,
        )

        import_representation = processing.format_habit_repetitions_by_date_to_markdown(
            habit_repetitions_by_date,
        )
        _debug_log(
            'import representation (given in part):\n',
            '\n'.join(re.split(r'\n(?=#)', import_representation)[:_MAX_COUNT]),
        )

        output.copy_import_representation(import_representation)
        if options.output is not None:
            output.output_import_representation(options.output, import_representation)
    except Exception as exception:
        sys.exit('error: ' + str(exception))
