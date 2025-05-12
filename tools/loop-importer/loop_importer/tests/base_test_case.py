import unittest
from datetime import date
from typing import Optional, List

from .. import models

_POSITION_OFFSET = 100

class BaseTestCase(unittest.TestCase):
    def _create_habit(
        self,
        id: int,
        name: str,
        repetition_dates: Optional[List[date]] = None,
        is_archived: Optional[bool] = None,
    ) -> models.Habit:
        repetitions = []
        if repetition_dates is not None:
            repetitions = [
                models.Repetition(habit_id=id, date=date, value=models.RepetitionValue.YES)
                for date in repetition_dates
            ]

        return models.Habit(
            id=id,
            name=name,
            position=id + _POSITION_OFFSET,
            repetitions=repetitions,
            is_archived=is_archived if is_archived is not None else id % 2 == 0,
        )

    def _create_habit_repetition(
        self,
        id: int,
        name: str,
        value: models.RepetitionValue,
        position: Optional[int] = None,
    ) -> models.HabitRepetition:
        return models.HabitRepetition(
            habit_id=id,
            habit_name=name,
            habit_position=position if position is not None else id + _POSITION_OFFSET,
            is_habit_archived=id % 2 == 0,
            value=value,
        )
