from collections import defaultdict
from typing import List
from dataclasses import replace

from . import models

def group_habits_by_date(habits: List[models.Habit]) -> models.HabitsByDate:
    habits_by_date = defaultdict(list)
    for habit in habits:
        for repetition in habit.repetitions:
            cleaned_habit = replace(habit, repetitions=[])
            habits_by_date[repetition.date].append(cleaned_habit)

    return dict(habits_by_date)
