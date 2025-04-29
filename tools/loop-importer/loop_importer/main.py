import sys

from . import logger
from . import cli
from . import output
from . import db
from . import models
from . import processing

def main():
    try:
        options = cli.parse_options()
        logger.init_logger(options.verbose)

        habits = db.load_habits_from_db(options.db)
        logger.get_logger().debug(models.Habit.schema().dumps(habits, many=True))

        habit_repetitions_by_date = processing.group_habit_repetitions_by_date(habits)
        logger.get_logger().debug(models.HabitRepetitionsByDateItem.schema().dumps(
            models.iterate_over_habit_repetitions_by_date(habit_repetitions_by_date),
            many=True,
        ))

        import_representation = \
            processing.format_habit_repetitions_by_date_to_markdown(habit_repetitions_by_date)
        logger.get_logger().debug(import_representation)

        output.copy_import_representation(import_representation)
        if options.output is not None:
            output.output_import_representation(options.output, import_representation)
    except Exception as exception:
        sys.exit('error: ' + str(exception))
