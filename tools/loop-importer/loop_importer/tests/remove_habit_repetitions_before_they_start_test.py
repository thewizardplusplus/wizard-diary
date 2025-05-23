from datetime import date

from . import base_test_case
from .. import models
from .. import processing

class TestRemoveHabitRepetitionsBeforeTheyStart(base_test_case.BaseTestCase):
    def setUp(self) -> None:
        self.habits = [
            self._create_habit(id=1, name='one'),
            self._create_habit(id=2, name='two'),
            self._create_habit(id=3, name='three'),
            self._create_habit(id=4, name='four'),
            self._create_habit(id=5, name='five'),
        ]

    def test_without_skipping_repetitions(self) -> None:
        yes = models.RepetitionValue.YES

        habit_repetitions_by_date = processing.remove_habit_repetitions_before_they_start(
            self.habits,
            {
                date(2025, 1, 1): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=yes),
                    self._create_habit_repetition(id=3, name='three', value=yes),
                    self._create_habit_repetition(id=4, name='four', value=yes),
                    self._create_habit_repetition(id=5, name='five', value=yes),
                ],
                date(2025, 1, 2): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=yes),
                    self._create_habit_repetition(id=3, name='three', value=yes),
                    self._create_habit_repetition(id=4, name='four', value=yes),
                    self._create_habit_repetition(id=5, name='five', value=yes),
                ],
                date(2025, 1, 3): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=yes),
                    self._create_habit_repetition(id=3, name='three', value=yes),
                    self._create_habit_repetition(id=4, name='four', value=yes),
                    self._create_habit_repetition(id=5, name='five', value=yes),
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
            ],
            date(2025, 1, 2): [
                self._create_habit_repetition(id=1, name='one', value=yes),
                self._create_habit_repetition(id=2, name='two', value=yes),
                self._create_habit_repetition(id=3, name='three', value=yes),
                self._create_habit_repetition(id=4, name='four', value=yes),
                self._create_habit_repetition(id=5, name='five', value=yes),
            ],
            date(2025, 1, 3): [
                self._create_habit_repetition(id=1, name='one', value=yes),
                self._create_habit_repetition(id=2, name='two', value=yes),
                self._create_habit_repetition(id=3, name='three', value=yes),
                self._create_habit_repetition(id=4, name='four', value=yes),
                self._create_habit_repetition(id=5, name='five', value=yes),
            ],
        })

    def test_with_skipping_repetitions_at_beginning(self) -> None:
        no = models.RepetitionValue.NO
        yes = models.RepetitionValue.YES
        skip = models.RepetitionValue.SKIP

        habit_repetitions_by_date = processing.remove_habit_repetitions_before_they_start(
            self.habits,
            {
                date(2025, 1, 1): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=no),
                    self._create_habit_repetition(id=3, name='three', value=no),
                    self._create_habit_repetition(id=4, name='four', value=skip),
                    self._create_habit_repetition(id=5, name='five', value=skip),
                ],
                date(2025, 1, 2): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=no),
                    self._create_habit_repetition(id=3, name='three', value=yes),
                    self._create_habit_repetition(id=4, name='four', value=skip),
                    self._create_habit_repetition(id=5, name='five', value=yes),
                ],
                date(2025, 1, 3): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=yes),
                    self._create_habit_repetition(id=3, name='three', value=yes),
                    self._create_habit_repetition(id=4, name='four', value=yes),
                    self._create_habit_repetition(id=5, name='five', value=yes),
                ],
            },
        )

        self.assertEqual(habit_repetitions_by_date, {
            date(2025, 1, 1): [
                self._create_habit_repetition(id=1, name='one', value=yes),
            ],
            date(2025, 1, 2): [
                self._create_habit_repetition(id=1, name='one', value=yes),
                self._create_habit_repetition(id=3, name='three', value=yes),
                self._create_habit_repetition(id=5, name='five', value=yes),
            ],
            date(2025, 1, 3): [
                self._create_habit_repetition(id=1, name='one', value=yes),
                self._create_habit_repetition(id=2, name='two', value=yes),
                self._create_habit_repetition(id=3, name='three', value=yes),
                self._create_habit_repetition(id=4, name='four', value=yes),
                self._create_habit_repetition(id=5, name='five', value=yes),
            ],
        })

    def test_with_skipping_repetitions_in_middle(self) -> None:
        no = models.RepetitionValue.NO
        yes = models.RepetitionValue.YES
        skip = models.RepetitionValue.SKIP

        habit_repetitions_by_date = processing.remove_habit_repetitions_before_they_start(
            self.habits,
            {
                date(2025, 1, 1): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=yes),
                    self._create_habit_repetition(id=3, name='three', value=yes),
                    self._create_habit_repetition(id=4, name='four', value=yes),
                    self._create_habit_repetition(id=5, name='five', value=yes),
                ],
                date(2025, 1, 2): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=no),
                    self._create_habit_repetition(id=3, name='three', value=no),
                    self._create_habit_repetition(id=4, name='four', value=skip),
                    self._create_habit_repetition(id=5, name='five', value=skip),
                ],
                date(2025, 1, 3): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=yes),
                    self._create_habit_repetition(id=3, name='three', value=yes),
                    self._create_habit_repetition(id=4, name='four', value=yes),
                    self._create_habit_repetition(id=5, name='five', value=yes),
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
            ],
            date(2025, 1, 2): [
                self._create_habit_repetition(id=1, name='one', value=yes),
                self._create_habit_repetition(id=2, name='two', value=no),
                self._create_habit_repetition(id=3, name='three', value=no),
                self._create_habit_repetition(id=4, name='four', value=skip),
                self._create_habit_repetition(id=5, name='five', value=skip),
            ],
            date(2025, 1, 3): [
                self._create_habit_repetition(id=1, name='one', value=yes),
                self._create_habit_repetition(id=2, name='two', value=yes),
                self._create_habit_repetition(id=3, name='three', value=yes),
                self._create_habit_repetition(id=4, name='four', value=yes),
                self._create_habit_repetition(id=5, name='five', value=yes),
            ],
        })

    def test_with_skipping_repetitions_at_end(self) -> None:
        no = models.RepetitionValue.NO
        yes = models.RepetitionValue.YES
        skip = models.RepetitionValue.SKIP

        habit_repetitions_by_date = processing.remove_habit_repetitions_before_they_start(
            self.habits,
            {
                date(2025, 1, 1): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=yes),
                    self._create_habit_repetition(id=3, name='three', value=yes),
                    self._create_habit_repetition(id=4, name='four', value=yes),
                    self._create_habit_repetition(id=5, name='five', value=yes),
                ],
                date(2025, 1, 2): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=no),
                    self._create_habit_repetition(id=3, name='three', value=yes),
                    self._create_habit_repetition(id=4, name='four', value=skip),
                    self._create_habit_repetition(id=5, name='five', value=yes),
                ],
                date(2025, 1, 3): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=no),
                    self._create_habit_repetition(id=3, name='three', value=no),
                    self._create_habit_repetition(id=4, name='four', value=skip),
                    self._create_habit_repetition(id=5, name='five', value=skip),
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
            ],
            date(2025, 1, 2): [
                self._create_habit_repetition(id=1, name='one', value=yes),
                self._create_habit_repetition(id=2, name='two', value=no),
                self._create_habit_repetition(id=3, name='three', value=yes),
                self._create_habit_repetition(id=4, name='four', value=skip),
                self._create_habit_repetition(id=5, name='five', value=yes),
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

        habit_repetitions_by_date = processing.remove_habit_repetitions_before_they_start(
            self.habits,
            {
                date(2025, 1, 1): [
                    self._create_habit_repetition(id=2, name='two', value=no),
                    self._create_habit_repetition(id=3, name='three', value=no),
                    self._create_habit_repetition(id=4, name='four', value=skip),
                    self._create_habit_repetition(id=5, name='five', value=skip),
                ],
                date(2025, 1, 2): [
                    self._create_habit_repetition(id=2, name='two', value=no),
                    self._create_habit_repetition(id=3, name='three', value=yes),
                    self._create_habit_repetition(id=4, name='four', value=skip),
                    self._create_habit_repetition(id=5, name='five', value=yes),
                ],
                date(2025, 1, 3): [
                    self._create_habit_repetition(id=2, name='two', value=yes),
                    self._create_habit_repetition(id=3, name='three', value=yes),
                    self._create_habit_repetition(id=4, name='four', value=yes),
                    self._create_habit_repetition(id=5, name='five', value=yes),
                ],
            },
        )

        self.assertEqual(habit_repetitions_by_date, {
            date(2025, 1, 2): [
                self._create_habit_repetition(id=3, name='three', value=yes),
                self._create_habit_repetition(id=5, name='five', value=yes),
            ],
            date(2025, 1, 3): [
                self._create_habit_repetition(id=2, name='two', value=yes),
                self._create_habit_repetition(id=3, name='three', value=yes),
                self._create_habit_repetition(id=4, name='four', value=yes),
                self._create_habit_repetition(id=5, name='five', value=yes),
            ],
        })
