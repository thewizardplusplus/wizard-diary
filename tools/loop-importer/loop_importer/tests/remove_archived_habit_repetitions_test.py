import unittest
from datetime import date

from .. import models
from .. import processing

_POSITION_OFFSET = 100

class TestRemoveArchivedHabitRepetitions(unittest.TestCase):
    def setUp(self) -> None:
        self.habits = [
            self._create_habit(id=1, name='one', is_archived=False),
            self._create_habit(id=2, name='two', is_archived=False),
            self._create_habit(id=3, name='three', is_archived=False),
            self._create_habit(id=4, name='four', is_archived=False),
            self._create_habit(id=5, name='five', is_archived=False),
            self._create_habit(id=6, name='six', is_archived=True),
            self._create_habit(id=7, name='seven', is_archived=True),
            self._create_habit(id=8, name='eight', is_archived=True),
            self._create_habit(id=9, name='nine', is_archived=True),
        ]

    def test_without_skipping_repetitions(self) -> None:
        yes = models.RepetitionValue.YES

        habit_repetitions_by_date = processing.remove_archived_habit_repetitions(
            self.habits,
            {
                date(2025, 1, 1): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=yes),
                    self._create_habit_repetition(id=3, name='three', value=yes),
                    self._create_habit_repetition(id=4, name='four', value=yes),
                    self._create_habit_repetition(id=5, name='five', value=yes),
                    self._create_habit_repetition(id=6, name='six', value=yes),
                    self._create_habit_repetition(id=7, name='seven', value=yes),
                    self._create_habit_repetition(id=8, name='eight', value=yes),
                    self._create_habit_repetition(id=9, name='nine', value=yes),
                ],
                date(2025, 1, 2): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=yes),
                    self._create_habit_repetition(id=3, name='three', value=yes),
                    self._create_habit_repetition(id=4, name='four', value=yes),
                    self._create_habit_repetition(id=5, name='five', value=yes),
                    self._create_habit_repetition(id=6, name='six', value=yes),
                    self._create_habit_repetition(id=7, name='seven', value=yes),
                    self._create_habit_repetition(id=8, name='eight', value=yes),
                    self._create_habit_repetition(id=9, name='nine', value=yes),
                ],
                date(2025, 1, 3): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=yes),
                    self._create_habit_repetition(id=3, name='three', value=yes),
                    self._create_habit_repetition(id=4, name='four', value=yes),
                    self._create_habit_repetition(id=5, name='five', value=yes),
                    self._create_habit_repetition(id=6, name='six', value=yes),
                    self._create_habit_repetition(id=7, name='seven', value=yes),
                    self._create_habit_repetition(id=8, name='eight', value=yes),
                    self._create_habit_repetition(id=9, name='nine', value=yes),
                ],
            },
        )

        self.assertEqual(habit_repetitions_by_date, {
            date(2025, 1, 1): [
                self._create_habit_repetition(id=1, name='one', value=yes),
                self._create_habit_repetition(id=2, name='two', value=yes),
                self._create_habit_repetition(id=3, name='three', value=yes),
                self._create_habit_repetition(id=4, name='four', value=yes),
                self._create_habit_repetition(id=5, name='five', value=yes),
                self._create_habit_repetition(id=6, name='six', value=yes),
                self._create_habit_repetition(id=7, name='seven', value=yes),
                self._create_habit_repetition(id=8, name='eight', value=yes),
                self._create_habit_repetition(id=9, name='nine', value=yes),
            ],
            date(2025, 1, 2): [
                self._create_habit_repetition(id=1, name='one', value=yes),
                self._create_habit_repetition(id=2, name='two', value=yes),
                self._create_habit_repetition(id=3, name='three', value=yes),
                self._create_habit_repetition(id=4, name='four', value=yes),
                self._create_habit_repetition(id=5, name='five', value=yes),
                self._create_habit_repetition(id=6, name='six', value=yes),
                self._create_habit_repetition(id=7, name='seven', value=yes),
                self._create_habit_repetition(id=8, name='eight', value=yes),
                self._create_habit_repetition(id=9, name='nine', value=yes),
            ],
            date(2025, 1, 3): [
                self._create_habit_repetition(id=1, name='one', value=yes),
                self._create_habit_repetition(id=2, name='two', value=yes),
                self._create_habit_repetition(id=3, name='three', value=yes),
                self._create_habit_repetition(id=4, name='four', value=yes),
                self._create_habit_repetition(id=5, name='five', value=yes),
                self._create_habit_repetition(id=6, name='six', value=yes),
                self._create_habit_repetition(id=7, name='seven', value=yes),
                self._create_habit_repetition(id=8, name='eight', value=yes),
                self._create_habit_repetition(id=9, name='nine', value=yes),
            ],
        })

    def test_with_skipping_repetitions_at_beginning(self) -> None:
        no = models.RepetitionValue.NO
        yes = models.RepetitionValue.YES
        skip = models.RepetitionValue.SKIP

        habit_repetitions_by_date = processing.remove_archived_habit_repetitions(
            self.habits,
            {
                date(2025, 1, 1): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=no),
                    self._create_habit_repetition(id=3, name='three', value=no),
                    self._create_habit_repetition(id=4, name='four', value=skip),
                    self._create_habit_repetition(id=5, name='five', value=skip),
                    self._create_habit_repetition(id=6, name='six', value=no),
                    self._create_habit_repetition(id=7, name='seven', value=no),
                    self._create_habit_repetition(id=8, name='eight', value=skip),
                    self._create_habit_repetition(id=9, name='nine', value=skip),
                ],
                date(2025, 1, 2): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=no),
                    self._create_habit_repetition(id=3, name='three', value=yes),
                    self._create_habit_repetition(id=4, name='four', value=skip),
                    self._create_habit_repetition(id=5, name='five', value=yes),
                    self._create_habit_repetition(id=6, name='six', value=no),
                    self._create_habit_repetition(id=7, name='seven', value=yes),
                    self._create_habit_repetition(id=8, name='eight', value=skip),
                    self._create_habit_repetition(id=9, name='nine', value=yes),
                ],
                date(2025, 1, 3): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=yes),
                    self._create_habit_repetition(id=3, name='three', value=yes),
                    self._create_habit_repetition(id=4, name='four', value=yes),
                    self._create_habit_repetition(id=5, name='five', value=yes),
                    self._create_habit_repetition(id=6, name='six', value=yes),
                    self._create_habit_repetition(id=7, name='seven', value=yes),
                    self._create_habit_repetition(id=8, name='eight', value=yes),
                    self._create_habit_repetition(id=9, name='nine', value=yes),
                ],
            },
        )

        self.assertEqual(habit_repetitions_by_date, {
            date(2025, 1, 1): [
                self._create_habit_repetition(id=1, name='one', value=yes),
                self._create_habit_repetition(id=2, name='two', value=no),
                self._create_habit_repetition(id=3, name='three', value=no),
                self._create_habit_repetition(id=4, name='four', value=skip),
                self._create_habit_repetition(id=5, name='five', value=skip),
                self._create_habit_repetition(id=6, name='six', value=no),
                self._create_habit_repetition(id=7, name='seven', value=no),
                self._create_habit_repetition(id=8, name='eight', value=skip),
                self._create_habit_repetition(id=9, name='nine', value=skip),
            ],
            date(2025, 1, 2): [
                self._create_habit_repetition(id=1, name='one', value=yes),
                self._create_habit_repetition(id=2, name='two', value=no),
                self._create_habit_repetition(id=3, name='three', value=yes),
                self._create_habit_repetition(id=4, name='four', value=skip),
                self._create_habit_repetition(id=5, name='five', value=yes),
                self._create_habit_repetition(id=6, name='six', value=no),
                self._create_habit_repetition(id=7, name='seven', value=yes),
                self._create_habit_repetition(id=8, name='eight', value=skip),
                self._create_habit_repetition(id=9, name='nine', value=yes),
            ],
            date(2025, 1, 3): [
                self._create_habit_repetition(id=1, name='one', value=yes),
                self._create_habit_repetition(id=2, name='two', value=yes),
                self._create_habit_repetition(id=3, name='three', value=yes),
                self._create_habit_repetition(id=4, name='four', value=yes),
                self._create_habit_repetition(id=5, name='five', value=yes),
                self._create_habit_repetition(id=6, name='six', value=yes),
                self._create_habit_repetition(id=7, name='seven', value=yes),
                self._create_habit_repetition(id=8, name='eight', value=yes),
                self._create_habit_repetition(id=9, name='nine', value=yes),
            ],
        })

    def test_with_skipping_repetitions_in_middle(self) -> None:
        no = models.RepetitionValue.NO
        yes = models.RepetitionValue.YES
        skip = models.RepetitionValue.SKIP

        habit_repetitions_by_date = processing.remove_archived_habit_repetitions(
            self.habits,
            {
                date(2025, 1, 1): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=yes),
                    self._create_habit_repetition(id=3, name='three', value=yes),
                    self._create_habit_repetition(id=4, name='four', value=yes),
                    self._create_habit_repetition(id=5, name='five', value=yes),
                    self._create_habit_repetition(id=6, name='six', value=yes),
                    self._create_habit_repetition(id=7, name='seven', value=yes),
                    self._create_habit_repetition(id=8, name='eight', value=yes),
                    self._create_habit_repetition(id=9, name='nine', value=yes),
                ],
                date(2025, 1, 2): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=no),
                    self._create_habit_repetition(id=3, name='three', value=no),
                    self._create_habit_repetition(id=4, name='four', value=skip),
                    self._create_habit_repetition(id=5, name='five', value=skip),
                    self._create_habit_repetition(id=6, name='six', value=no),
                    self._create_habit_repetition(id=7, name='seven', value=no),
                    self._create_habit_repetition(id=8, name='eight', value=skip),
                    self._create_habit_repetition(id=9, name='nine', value=skip),
                ],
                date(2025, 1, 3): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=yes),
                    self._create_habit_repetition(id=3, name='three', value=yes),
                    self._create_habit_repetition(id=4, name='four', value=yes),
                    self._create_habit_repetition(id=5, name='five', value=yes),
                    self._create_habit_repetition(id=6, name='six', value=yes),
                    self._create_habit_repetition(id=7, name='seven', value=yes),
                    self._create_habit_repetition(id=8, name='eight', value=yes),
                    self._create_habit_repetition(id=9, name='nine', value=yes),
                ],
            },
        )

        self.assertEqual(habit_repetitions_by_date, {
            date(2025, 1, 1): [
                self._create_habit_repetition(id=1, name='one', value=yes),
                self._create_habit_repetition(id=2, name='two', value=yes),
                self._create_habit_repetition(id=3, name='three', value=yes),
                self._create_habit_repetition(id=4, name='four', value=yes),
                self._create_habit_repetition(id=5, name='five', value=yes),
                self._create_habit_repetition(id=6, name='six', value=yes),
                self._create_habit_repetition(id=7, name='seven', value=yes),
                self._create_habit_repetition(id=8, name='eight', value=yes),
                self._create_habit_repetition(id=9, name='nine', value=yes),
            ],
            date(2025, 1, 2): [
                self._create_habit_repetition(id=1, name='one', value=yes),
                self._create_habit_repetition(id=2, name='two', value=no),
                self._create_habit_repetition(id=3, name='three', value=no),
                self._create_habit_repetition(id=4, name='four', value=skip),
                self._create_habit_repetition(id=5, name='five', value=skip),
                self._create_habit_repetition(id=6, name='six', value=no),
                self._create_habit_repetition(id=7, name='seven', value=no),
                self._create_habit_repetition(id=8, name='eight', value=skip),
                self._create_habit_repetition(id=9, name='nine', value=skip),
            ],
            date(2025, 1, 3): [
                self._create_habit_repetition(id=1, name='one', value=yes),
                self._create_habit_repetition(id=2, name='two', value=yes),
                self._create_habit_repetition(id=3, name='three', value=yes),
                self._create_habit_repetition(id=4, name='four', value=yes),
                self._create_habit_repetition(id=5, name='five', value=yes),
                self._create_habit_repetition(id=6, name='six', value=yes),
                self._create_habit_repetition(id=7, name='seven', value=yes),
                self._create_habit_repetition(id=8, name='eight', value=yes),
                self._create_habit_repetition(id=9, name='nine', value=yes),
            ],
        })

    def test_with_skipping_repetitions_at_end(self) -> None:
        no = models.RepetitionValue.NO
        yes = models.RepetitionValue.YES
        skip = models.RepetitionValue.SKIP

        habit_repetitions_by_date = processing.remove_archived_habit_repetitions(
            self.habits,
            {
                date(2025, 1, 1): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=yes),
                    self._create_habit_repetition(id=3, name='three', value=yes),
                    self._create_habit_repetition(id=4, name='four', value=yes),
                    self._create_habit_repetition(id=5, name='five', value=yes),
                    self._create_habit_repetition(id=6, name='six', value=yes),
                    self._create_habit_repetition(id=7, name='seven', value=yes),
                    self._create_habit_repetition(id=8, name='eight', value=yes),
                    self._create_habit_repetition(id=9, name='nine', value=yes),
                ],
                date(2025, 1, 2): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=no),
                    self._create_habit_repetition(id=3, name='three', value=yes),
                    self._create_habit_repetition(id=4, name='four', value=skip),
                    self._create_habit_repetition(id=5, name='five', value=yes),
                    self._create_habit_repetition(id=6, name='six', value=no),
                    self._create_habit_repetition(id=7, name='seven', value=yes),
                    self._create_habit_repetition(id=8, name='eight', value=skip),
                    self._create_habit_repetition(id=9, name='nine', value=yes),
                ],
                date(2025, 1, 3): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=no),
                    self._create_habit_repetition(id=3, name='three', value=no),
                    self._create_habit_repetition(id=4, name='four', value=skip),
                    self._create_habit_repetition(id=5, name='five', value=skip),
                    self._create_habit_repetition(id=6, name='six', value=no),
                    self._create_habit_repetition(id=7, name='seven', value=no),
                    self._create_habit_repetition(id=8, name='eight', value=skip),
                    self._create_habit_repetition(id=9, name='nine', value=skip),
                ],
            },
        )

        self.assertEqual(habit_repetitions_by_date, {
            date(2025, 1, 1): [
                self._create_habit_repetition(id=1, name='one', value=yes),
                self._create_habit_repetition(id=2, name='two', value=yes),
                self._create_habit_repetition(id=3, name='three', value=yes),
                self._create_habit_repetition(id=4, name='four', value=yes),
                self._create_habit_repetition(id=5, name='five', value=yes),
                self._create_habit_repetition(id=6, name='six', value=yes),
                self._create_habit_repetition(id=7, name='seven', value=yes),
                self._create_habit_repetition(id=8, name='eight', value=yes),
                self._create_habit_repetition(id=9, name='nine', value=yes),
            ],
            date(2025, 1, 2): [
                self._create_habit_repetition(id=1, name='one', value=yes),
                self._create_habit_repetition(id=2, name='two', value=no),
                self._create_habit_repetition(id=3, name='three', value=yes),
                self._create_habit_repetition(id=4, name='four', value=skip),
                self._create_habit_repetition(id=5, name='five', value=yes),
                self._create_habit_repetition(id=7, name='seven', value=yes),
                self._create_habit_repetition(id=9, name='nine', value=yes),
            ],
            date(2025, 1, 3): [
                self._create_habit_repetition(id=1, name='one', value=yes),
                self._create_habit_repetition(id=2, name='two', value=no),
                self._create_habit_repetition(id=3, name='three', value=no),
                self._create_habit_repetition(id=4, name='four', value=skip),
                self._create_habit_repetition(id=5, name='five', value=skip),
            ],
        })

    def test_with_skipping_whole_day(self) -> None:
        no = models.RepetitionValue.NO
        yes = models.RepetitionValue.YES
        skip = models.RepetitionValue.SKIP

        habit_repetitions_by_date = processing.remove_archived_habit_repetitions(
            self.habits,
            {
                date(2025, 1, 1): [
                    self._create_habit_repetition(id=6, name='six', value=yes),
                    self._create_habit_repetition(id=7, name='seven', value=yes),
                    self._create_habit_repetition(id=8, name='eight', value=yes),
                    self._create_habit_repetition(id=9, name='nine', value=yes),
                ],
                date(2025, 1, 2): [
                    self._create_habit_repetition(id=6, name='six', value=no),
                    self._create_habit_repetition(id=7, name='seven', value=yes),
                    self._create_habit_repetition(id=8, name='eight', value=skip),
                    self._create_habit_repetition(id=9, name='nine', value=yes),
                ],
                date(2025, 1, 3): [
                    self._create_habit_repetition(id=6, name='six', value=no),
                    self._create_habit_repetition(id=7, name='seven', value=no),
                    self._create_habit_repetition(id=8, name='eight', value=skip),
                    self._create_habit_repetition(id=9, name='nine', value=skip),
                ],
            },
        )

        self.assertEqual(habit_repetitions_by_date, {
            date(2025, 1, 1): [
                self._create_habit_repetition(id=6, name='six', value=yes),
                self._create_habit_repetition(id=7, name='seven', value=yes),
                self._create_habit_repetition(id=8, name='eight', value=yes),
                self._create_habit_repetition(id=9, name='nine', value=yes),
            ],
            date(2025, 1, 2): [
                self._create_habit_repetition(id=7, name='seven', value=yes),
                self._create_habit_repetition(id=9, name='nine', value=yes),
            ],
        })

    def _create_habit(self, id: int, name: str, is_archived: bool) -> models.Habit:
        return models.Habit(
            id=id,
            name=name,
            position=id + _POSITION_OFFSET,
            is_archived=is_archived,
            repetitions=[]
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
