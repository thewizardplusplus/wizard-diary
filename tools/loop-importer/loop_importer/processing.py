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
