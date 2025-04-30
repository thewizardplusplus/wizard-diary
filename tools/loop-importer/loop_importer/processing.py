from collections import defaultdict
from typing import List

from . import models

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

    for habit_repetitions_by_id in habit_repetitions_by_id_and_date.values():
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

    return {
        date: list(habit_repetitions_by_id.values())
        for date, habit_repetitions_by_id in habit_repetitions_by_id_and_date.items()
    }

def remove_habit_repetitions_before_they_start(
    habits: List[models.Habit],
    habit_repetitions_by_date: models.HabitRepetitionsByDate,
) -> models.HabitRepetitionsByDate:
    not_yet_started_habit_names = {habit.name for habit in habits}

    filtered_habit_repetitions_by_date = {}
    for date in sorted(habit_repetitions_by_date):
        filtered_habit_repetitions = []
        for habit_repetition in habit_repetitions_by_date[date]:
            if habit_repetition.value != models.RepetitionValue.YES \
                and habit_repetition.habit_name in not_yet_started_habit_names:
                continue

            if habit_repetition.value == models.RepetitionValue.YES:
                not_yet_started_habit_names.discard(habit_repetition.habit_name)

            filtered_habit_repetitions.append(habit_repetition)
        if not filtered_habit_repetitions:
            continue

        filtered_habit_repetitions_by_date[date] = filtered_habit_repetitions

    return filtered_habit_repetitions_by_date

def format_habit_repetitions_by_date_to_markdown(
    habit_repetitions_by_date: models.HabitRepetitionsByDate,
) -> str:
    lines = []
    for date in sorted(habit_repetitions_by_date):
        lines.append(f'## {date.isoformat()}')
        lines.append('')

        for habit_repetition in sorted(
            habit_repetitions_by_date[date],
            key=lambda habit_repetition: habit_repetition.habit_position,
        ):
            checkbox = '[ ]'
            if habit_repetition.value == models.RepetitionValue.YES:
                checkbox = '[x]'

            name = habit_repetition.habit_name
            if habit_repetition.value == models.RepetitionValue.SKIP:
                name = f'~~{name}~~'

            lines.append(f'- {checkbox} {name}')
        lines.append('')

    return '\n'.join(lines).strip() + '\n'
