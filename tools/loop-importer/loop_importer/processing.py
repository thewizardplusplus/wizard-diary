import datetime
from collections import defaultdict
from typing import Iterable, Set, List, Optional

from . import models

_SEPARATOR = '- [ ] -'

def _filter_habit_repetitions(
    habit_repetitions_by_date: models.HabitRepetitionsByDate,
    dates_to_analyze: Iterable[datetime.date],
    habit_names_to_analyze: Set[str],
) -> models.HabitRepetitionsByDate:
    copied_habit_names_to_analyze = habit_names_to_analyze.copy()

    filtered_habit_repetitions_by_date = {}
    for date in dates_to_analyze:
        filtered_habit_repetitions = []
        for habit_repetition in habit_repetitions_by_date[date]:
            if habit_repetition.value != models.RepetitionValue.YES \
                and habit_repetition.habit_name in copied_habit_names_to_analyze:
                continue

            if habit_repetition.value == models.RepetitionValue.YES:
                copied_habit_names_to_analyze.discard(habit_repetition.habit_name)

            filtered_habit_repetitions.append(habit_repetition)
        if not filtered_habit_repetitions:
            continue

        filtered_habit_repetitions_by_date[date] = filtered_habit_repetitions

    return filtered_habit_repetitions_by_date

def group_habit_repetitions_by_date(habits: List[models.Habit]) -> models.HabitRepetitionsByDate:
    habit_repetitions_by_id_and_date = defaultdict(dict)
    for habit in habits:
        for repetition in habit.repetitions:
            habit_repetitions_by_id_and_date[repetition.date][habit.id] = models.HabitRepetition(
                habit_id=habit.id,
                habit_name=habit.name,
                habit_position=habit.position,
                is_habit_archived=habit.is_archived,
                value=repetition.value,
            )

    min_date = min(habit_repetitions_by_id_and_date)
    max_date = max(habit_repetitions_by_id_and_date)

    date = min_date
    while date <= max_date:
        habit_repetitions_by_id = habit_repetitions_by_id_and_date[date]
        for habit in habits:
            if habit.id in habit_repetitions_by_id:
                continue

            habit_repetitions_by_id[habit.id] = models.HabitRepetition(
                habit_id=habit.id,
                habit_name=habit.name,
                habit_position=habit.position,
                is_habit_archived=habit.is_archived,
                value=models.RepetitionValue.NO,
            )

        date += datetime.timedelta(days=1)

    return {
        date: list(habit_repetitions_by_id.values())
        for date, habit_repetitions_by_id in habit_repetitions_by_id_and_date.items()
    }

def remove_habit_repetitions_before_they_start(
    habits: List[models.Habit],
    habit_repetitions_by_date: models.HabitRepetitionsByDate,
) -> models.HabitRepetitionsByDate:
    return _filter_habit_repetitions(
        habit_repetitions_by_date=habit_repetitions_by_date,
        dates_to_analyze=sorted(habit_repetitions_by_date),
        habit_names_to_analyze={habit.name for habit in habits},
    )

def remove_archived_habit_repetitions(
    habits: List[models.Habit],
    habit_repetitions_by_date: models.HabitRepetitionsByDate,
) -> models.HabitRepetitionsByDate:
    return _filter_habit_repetitions(
        habit_repetitions_by_date=habit_repetitions_by_date,
        dates_to_analyze=reversed(sorted(habit_repetitions_by_date)),
        habit_names_to_analyze={habit.name for habit in habits if habit.is_archived},
    )

def format_habit_repetitions_by_date_to_markdown(
    habit_repetitions_by_date: models.HabitRepetitionsByDate,
    separator_predecessor_ids: Optional[List[int]] = None,
) -> str:
    lines = []
    for date in sorted(habit_repetitions_by_date):
        lines.append(f'## {date.isoformat()}')
        lines.append('')

        previous_prefix = None
        has_trailing_separator = False
        for index, habit_repetition in enumerate(sorted(
            habit_repetitions_by_date[date],
            key=lambda habit_repetition: habit_repetition.habit_position,
        )):
            checkbox = '[ ]'
            if habit_repetition.value == models.RepetitionValue.YES:
                checkbox = '[x]'

            name = habit_repetition.habit_name
            if habit_repetition.value == models.RepetitionValue.SKIP:
                name = f'~~{name}~~'

            prefix, separator, _ = habit_repetition.habit_name.partition(',')
            if separator:
                if prefix.strip() != previous_prefix \
                    and index != 0 \
                    and not has_trailing_separator:
                    lines.append(_SEPARATOR)
                previous_prefix = prefix.strip()

            lines.append(f'- {checkbox} {name}')
            has_trailing_separator = False

            if separator_predecessor_ids is not None \
                and habit_repetition.habit_id in separator_predecessor_ids \
                and index != len(habit_repetitions_by_date[date])-1:
                lines.append(_SEPARATOR)
                has_trailing_separator = True
        lines.append('')

    return '\n'.join(lines).strip() + '\n'
