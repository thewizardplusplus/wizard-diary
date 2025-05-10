import unittest
from datetime import date
from typing import List

from .. import models
from .. import processing

_POSITION_OFFSET = 100

class TestGroupHabitRepetitionsByDate(unittest.TestCase):
    def test_without_skipping_repetitions(self) -> None:
        habit_repetitions_by_date = processing.group_habit_repetitions_by_date([
            self._create_habit(id=1, name='one', repetition_dates=[
                date(2025, 1, 1),
                date(2025, 1, 2),
                date(2025, 1, 3),
            ]),
            self._create_habit(id=2, name='two', repetition_dates=[
                date(2025, 1, 1),
                date(2025, 1, 2),
                date(2025, 1, 3),
            ]),
        ])

        self.assertEqual(self._sort_habit_repetitions(habit_repetitions_by_date), {
            date(2025, 1, 1): [
                self._create_habit_repetition(id=1, name='one', value=models.RepetitionValue.YES),
                self._create_habit_repetition(id=2, name='two', value=models.RepetitionValue.YES),
            ],
            date(2025, 1, 2): [
                self._create_habit_repetition(id=1, name='one', value=models.RepetitionValue.YES),
                self._create_habit_repetition(id=2, name='two', value=models.RepetitionValue.YES),
            ],
            date(2025, 1, 3): [
                self._create_habit_repetition(id=1, name='one', value=models.RepetitionValue.YES),
                self._create_habit_repetition(id=2, name='two', value=models.RepetitionValue.YES),
            ],
        })

    def test_with_skipping_repetitions(self) -> None:
        habit_repetitions_by_date = processing.group_habit_repetitions_by_date([
            self._create_habit(id=1, name='one', repetition_dates=[
                date(2025, 1, 1),
                date(2025, 1, 2),
                date(2025, 1, 3),
            ]),
            self._create_habit(id=2, name='two', repetition_dates=[
                date(2025, 1, 2),
                date(2025, 1, 3),
                date(2025, 1, 4),
            ]),
        ])

        self.assertEqual(self._sort_habit_repetitions(habit_repetitions_by_date), {
            date(2025, 1, 1): [
                self._create_habit_repetition(id=1, name='one', value=models.RepetitionValue.YES),
                self._create_habit_repetition(id=2, name='two', value=models.RepetitionValue.NO),
            ],
            date(2025, 1, 2): [
                self._create_habit_repetition(id=1, name='one', value=models.RepetitionValue.YES),
                self._create_habit_repetition(id=2, name='two', value=models.RepetitionValue.YES),
            ],
            date(2025, 1, 3): [
                self._create_habit_repetition(id=1, name='one', value=models.RepetitionValue.YES),
                self._create_habit_repetition(id=2, name='two', value=models.RepetitionValue.YES),
            ],
            date(2025, 1, 4): [
                self._create_habit_repetition(id=1, name='one', value=models.RepetitionValue.NO),
                self._create_habit_repetition(id=2, name='two', value=models.RepetitionValue.YES),
            ],
        })

    def test_with_skipping_whole_day(self) -> None:
        habit_repetitions_by_date = processing.group_habit_repetitions_by_date([
            self._create_habit(id=1, name='one', repetition_dates=[
                date(2025, 1, 1),
                date(2025, 1, 2),
                date(2025, 1, 4),
            ]),
            self._create_habit(id=2, name='two', repetition_dates=[
                date(2025, 1, 2),
                date(2025, 1, 4),
                date(2025, 1, 5),
            ]),
        ])

        self.assertEqual(self._sort_habit_repetitions(habit_repetitions_by_date), {
            date(2025, 1, 1): [
                self._create_habit_repetition(id=1, name='one', value=models.RepetitionValue.YES),
                self._create_habit_repetition(id=2, name='two', value=models.RepetitionValue.NO),
            ],
            date(2025, 1, 2): [
                self._create_habit_repetition(id=1, name='one', value=models.RepetitionValue.YES),
                self._create_habit_repetition(id=2, name='two', value=models.RepetitionValue.YES),
            ],
            date(2025, 1, 4): [
                self._create_habit_repetition(id=1, name='one', value=models.RepetitionValue.YES),
                self._create_habit_repetition(id=2, name='two', value=models.RepetitionValue.YES),
            ],
            date(2025, 1, 5): [
                self._create_habit_repetition(id=1, name='one', value=models.RepetitionValue.NO),
                self._create_habit_repetition(id=2, name='two', value=models.RepetitionValue.YES),
            ],
        })

    def _create_habit(self, id: int, name: str, repetition_dates: List[date]) -> models.Habit:
        return models.Habit(
            id=id,
            name=name,
            position=id + _POSITION_OFFSET,
            is_archived=id % 2 == 0,
            repetitions=[
                models.Repetition(habit_id=id, date=date, value=models.RepetitionValue.YES)
                for date in repetition_dates
            ]
        )

    def _create_habit_repetition(
        self,
        id: int,
        name: str,
        value: models.RepetitionValue,
    ) -> models.HabitRepetition:
        return models.HabitRepetition(
            habit_id=id,
            habit_name=name,
            habit_position=id + _POSITION_OFFSET,
            is_habit_archived=id % 2 == 0,
            value=value,
        )

    def _sort_habit_repetitions(
        self,
        habit_repetitions_by_date: models.HabitRepetitionsByDate,
    ) -> models.HabitRepetitionsByDate:
        return {
            date: list(sorted(
                habit_repetitions,
                key=lambda habit_repetition: habit_repetition.habit_position,
            ))
            for date, habit_repetitions in habit_repetitions_by_date.items()
        }
