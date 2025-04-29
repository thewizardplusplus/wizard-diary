from collections import defaultdict
from typing import List

from . import models

def group_habit_repetitions_by_date(habits: List[models.Habit]) -> models.HabitRepetitionsByDate:
    habit_repetitions_by_date = defaultdict(list)
    for habit in habits:
        for repetition in habit.repetitions:
            habit_repetitions_by_date[repetition.date].append(models.HabitRepetition(
                habit_id=habit.id,
                habit_name=habit.name,
                habit_position=habit.position,
                is_habit_archived=habit.is_archived,
                value=repetition.value,
            ))

    return dict(habit_repetitions_by_date)

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
