from datetime import date

from . import base_test_case
from .. import models
from .. import processing

class TestGroupHabitRepetitionsByDate(base_test_case.BaseTestCase):
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
            date(2025, 1, 3): [
                self._create_habit_repetition(id=1, name='one', value=models.RepetitionValue.NO),
                self._create_habit_repetition(id=2, name='two', value=models.RepetitionValue.NO),
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
