from datetime import date

from . import base_test_case
from .. import models
from .. import processing

class TestFormatHabitRepetitionsByDateToMarkdown(base_test_case.BaseTestCase):
    def test_regular(self) -> None:
        import_representation = processing.format_habit_repetitions_by_date_to_markdown({
            date(2025, 1, 1): [
                self._create_habit_repetition(id=1, name='one', value=models.RepetitionValue.YES),
                self._create_habit_repetition(id=2, name='two', value=models.RepetitionValue.SKIP),
                self._create_habit_repetition(id=3, name='three', value=models.RepetitionValue.NO),
            ],
            date(2025, 1, 2): [
                self._create_habit_repetition(id=1, name='one', value=models.RepetitionValue.YES),
                self._create_habit_repetition(id=2, name='two', value=models.RepetitionValue.SKIP),
                self._create_habit_repetition(id=3, name='three', value=models.RepetitionValue.NO),
            ],
        })

        self.assertEqual(
            import_representation,
            '## 2025-01-01\n'
                + '\n'
                + '- [x] one\n'
                + '- [ ] ~~two~~\n'
                + '- [ ] three\n'
                + '\n'
                + '## 2025-01-02\n'
                + '\n'
                + '- [x] one\n'
                + '- [ ] ~~two~~\n'
                + '- [ ] three\n',
        )

    def test_reverse_order(self) -> None:
        no = models.RepetitionValue.NO
        yes = models.RepetitionValue.YES
        skip = models.RepetitionValue.SKIP

        import_representation = processing.format_habit_repetitions_by_date_to_markdown({
            date(2025, 1, 1): [
                self._create_habit_repetition(id=1, name='one', value=yes, position=103),
                self._create_habit_repetition(id=2, name='two', value=skip, position=102),
                self._create_habit_repetition(id=3, name='three', value=no, position=101),
            ],
            date(2025, 1, 2): [
                self._create_habit_repetition(id=1, name='one', value=yes, position=103),
                self._create_habit_repetition(id=2, name='two', value=skip, position=102),
                self._create_habit_repetition(id=3, name='three', value=no, position=101),
            ],
        })

        self.assertEqual(
            import_representation,
            '## 2025-01-01\n'
                + '\n'
                + '- [ ] three\n'
                + '- [ ] ~~two~~\n'
                + '- [x] one\n'
                + '\n'
                + '## 2025-01-02\n'
                + '\n'
                + '- [ ] three\n'
                + '- [ ] ~~two~~\n'
                + '- [x] one\n',
        )

    def test_automatic_separators(self) -> None:
        no = models.RepetitionValue.NO
        yes = models.RepetitionValue.YES
        skip = models.RepetitionValue.SKIP

        import_representation = processing.format_habit_repetitions_by_date_to_markdown({
            date(2025, 1, 1): [
                self._create_habit_repetition(id=1, name='prefix #1, one', value=yes),
                self._create_habit_repetition(id=2, name='prefix #1, two', value=skip),
                self._create_habit_repetition(id=3, name='prefix #1, three', value=no),
                self._create_habit_repetition(id=4, name='prefix #2, one', value=yes),
                self._create_habit_repetition(id=5, name='prefix #2, two', value=skip),
                self._create_habit_repetition(id=6, name='prefix #2, three', value=no),
                self._create_habit_repetition(id=7, name='prefix #3, one', value=yes),
                self._create_habit_repetition(id=8, name='prefix #3, two', value=skip),
                self._create_habit_repetition(id=9, name='prefix #3, three', value=no),
            ],
            date(2025, 1, 2): [
                self._create_habit_repetition(id=1, name='prefix #1, one', value=yes),
                self._create_habit_repetition(id=2, name='prefix #1, two', value=skip),
                self._create_habit_repetition(id=3, name='prefix #1, three', value=no),
                self._create_habit_repetition(id=4, name='prefix #2, one', value=yes),
                self._create_habit_repetition(id=5, name='prefix #2, two', value=skip),
                self._create_habit_repetition(id=6, name='prefix #2, three', value=no),
                self._create_habit_repetition(id=7, name='prefix #3, one', value=yes),
                self._create_habit_repetition(id=8, name='prefix #3, two', value=skip),
                self._create_habit_repetition(id=9, name='prefix #3, three', value=no),
            ],
        })

        self.assertEqual(
            import_representation,
            '## 2025-01-01\n'
                + '\n'
                + '- [x] prefix #1, one\n'
                + '- [ ] ~~prefix #1, two~~\n'
                + '- [ ] prefix #1, three\n'
                + '- [ ] -\n'
                + '- [x] prefix #2, one\n'
                + '- [ ] ~~prefix #2, two~~\n'
                + '- [ ] prefix #2, three\n'
                + '- [ ] -\n'
                + '- [x] prefix #3, one\n'
                + '- [ ] ~~prefix #3, two~~\n'
                + '- [ ] prefix #3, three\n'
                + '\n'
                + '## 2025-01-02\n'
                + '\n'
                + '- [x] prefix #1, one\n'
                + '- [ ] ~~prefix #1, two~~\n'
                + '- [ ] prefix #1, three\n'
                + '- [ ] -\n'
                + '- [x] prefix #2, one\n'
                + '- [ ] ~~prefix #2, two~~\n'
                + '- [ ] prefix #2, three\n'
                + '- [ ] -\n'
                + '- [x] prefix #3, one\n'
                + '- [ ] ~~prefix #3, two~~\n'
                + '- [ ] prefix #3, three\n',
        )

    def test_custom_separators(self) -> None:
        no = models.RepetitionValue.NO
        yes = models.RepetitionValue.YES
        skip = models.RepetitionValue.SKIP

        import_representation = processing.format_habit_repetitions_by_date_to_markdown(
            {
                date(2025, 1, 1): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=skip),
                    self._create_habit_repetition(id=3, name='three', value=no),
                    self._create_habit_repetition(id=4, name='four', value=yes),
                    self._create_habit_repetition(id=5, name='five', value=skip),
                    self._create_habit_repetition(id=6, name='six', value=no),
                    self._create_habit_repetition(id=7, name='seven', value=yes),
                    self._create_habit_repetition(id=8, name='eight', value=skip),
                ],
                date(2025, 1, 2): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=skip),
                    self._create_habit_repetition(id=3, name='three', value=no),
                    self._create_habit_repetition(id=4, name='four', value=yes),
                    self._create_habit_repetition(id=5, name='five', value=skip),
                    self._create_habit_repetition(id=6, name='six', value=no),
                    self._create_habit_repetition(id=7, name='seven', value=yes),
                    self._create_habit_repetition(id=8, name='eight', value=skip),
                ],
            },
            [2, 4, 6],
        )

        self.assertEqual(
            import_representation,
            '## 2025-01-01\n'
                + '\n'
                + '- [x] one\n'
                + '- [ ] ~~two~~\n'
                + '- [ ] -\n'
                + '- [ ] three\n'
                + '- [x] four\n'
                + '- [ ] -\n'
                + '- [ ] ~~five~~\n'
                + '- [ ] six\n'
                + '- [ ] -\n'
                + '- [x] seven\n'
                + '- [ ] ~~eight~~\n'
                + '\n'
                + '## 2025-01-02\n'
                + '\n'
                + '- [x] one\n'
                + '- [ ] ~~two~~\n'
                + '- [ ] -\n'
                + '- [ ] three\n'
                + '- [x] four\n'
                + '- [ ] -\n'
                + '- [ ] ~~five~~\n'
                + '- [ ] six\n'
                + '- [ ] -\n'
                + '- [x] seven\n'
                + '- [ ] ~~eight~~\n',
        )

    def test_conflict_of_automatic_and_custom_separators(self) -> None:
        no = models.RepetitionValue.NO
        yes = models.RepetitionValue.YES
        skip = models.RepetitionValue.SKIP

        import_representation = processing.format_habit_repetitions_by_date_to_markdown(
            {
                date(2025, 1, 1): [
                    self._create_habit_repetition(id=1, name='prefix #1, one', value=yes),
                    self._create_habit_repetition(id=2, name='prefix #1, two', value=skip),
                    self._create_habit_repetition(id=3, name='prefix #1, three', value=skip),
                    self._create_habit_repetition(id=4, name='prefix #1, four', value=no),
                    self._create_habit_repetition(id=5, name='prefix #2, one', value=yes),
                    self._create_habit_repetition(id=6, name='prefix #2, two', value=skip),
                    self._create_habit_repetition(id=7, name='prefix #2, three', value=skip),
                    self._create_habit_repetition(id=8, name='prefix #2, four', value=no),
                    self._create_habit_repetition(id=9, name='prefix #3, one', value=yes),
                    self._create_habit_repetition(id=10, name='prefix #3, two', value=skip),
                    self._create_habit_repetition(id=11, name='prefix #3, three', value=skip),
                    self._create_habit_repetition(id=12, name='prefix #3, four', value=no),
                ],
                date(2025, 1, 2): [
                    self._create_habit_repetition(id=1, name='prefix #1, one', value=yes),
                    self._create_habit_repetition(id=2, name='prefix #1, two', value=skip),
                    self._create_habit_repetition(id=3, name='prefix #1, three', value=skip),
                    self._create_habit_repetition(id=4, name='prefix #1, four', value=no),
                    self._create_habit_repetition(id=5, name='prefix #2, one', value=yes),
                    self._create_habit_repetition(id=6, name='prefix #2, two', value=skip),
                    self._create_habit_repetition(id=7, name='prefix #2, three', value=skip),
                    self._create_habit_repetition(id=8, name='prefix #2, four', value=no),
                    self._create_habit_repetition(id=9, name='prefix #3, one', value=yes),
                    self._create_habit_repetition(id=10, name='prefix #3, two', value=skip),
                    self._create_habit_repetition(id=11, name='prefix #3, three', value=skip),
                    self._create_habit_repetition(id=12, name='prefix #3, four', value=no),
                ],
            },
            [2, 4, 6, 8, 10],
        )

        self.assertEqual(
            import_representation,
            '## 2025-01-01\n'
                + '\n'
                + '- [x] prefix #1, one\n'
                + '- [ ] ~~prefix #1, two~~\n'
                + '- [ ] -\n'
                + '- [ ] ~~prefix #1, three~~\n'
                + '- [ ] prefix #1, four\n'
                + '- [ ] -\n'
                + '- [x] prefix #2, one\n'
                + '- [ ] ~~prefix #2, two~~\n'
                + '- [ ] -\n'
                + '- [ ] ~~prefix #2, three~~\n'
                + '- [ ] prefix #2, four\n'
                + '- [ ] -\n'
                + '- [x] prefix #3, one\n'
                + '- [ ] ~~prefix #3, two~~\n'
                + '- [ ] -\n'
                + '- [ ] ~~prefix #3, three~~\n'
                + '- [ ] prefix #3, four\n'
                + '\n'
                + '## 2025-01-02\n'
                + '\n'
                + '- [x] prefix #1, one\n'
                + '- [ ] ~~prefix #1, two~~\n'
                + '- [ ] -\n'
                + '- [ ] ~~prefix #1, three~~\n'
                + '- [ ] prefix #1, four\n'
                + '- [ ] -\n'
                + '- [x] prefix #2, one\n'
                + '- [ ] ~~prefix #2, two~~\n'
                + '- [ ] -\n'
                + '- [ ] ~~prefix #2, three~~\n'
                + '- [ ] prefix #2, four\n'
                + '- [ ] -\n'
                + '- [x] prefix #3, one\n'
                + '- [ ] ~~prefix #3, two~~\n'
                + '- [ ] -\n'
                + '- [ ] ~~prefix #3, three~~\n'
                + '- [ ] prefix #3, four\n',
        )

    def test_trailing_separators(self) -> None:
        no = models.RepetitionValue.NO
        yes = models.RepetitionValue.YES
        skip = models.RepetitionValue.SKIP

        import_representation = processing.format_habit_repetitions_by_date_to_markdown(
            {
                date(2025, 1, 1): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=skip),
                    self._create_habit_repetition(id=3, name='three', value=no),
                    self._create_habit_repetition(id=4, name='four', value=yes),
                    self._create_habit_repetition(id=5, name='five', value=skip),
                    self._create_habit_repetition(id=6, name='six', value=no),
                    self._create_habit_repetition(id=7, name='seven', value=yes),
                    self._create_habit_repetition(id=8, name='eight', value=skip),
                ],
                date(2025, 1, 2): [
                    self._create_habit_repetition(id=1, name='one', value=yes),
                    self._create_habit_repetition(id=2, name='two', value=skip),
                    self._create_habit_repetition(id=3, name='three', value=no),
                    self._create_habit_repetition(id=4, name='four', value=yes),
                    self._create_habit_repetition(id=5, name='five', value=skip),
                    self._create_habit_repetition(id=6, name='six', value=no),
                    self._create_habit_repetition(id=7, name='seven', value=yes),
                    self._create_habit_repetition(id=8, name='eight', value=skip),
                ],
            },
            [2, 4, 6, 8],
        )

        self.assertEqual(
            import_representation,
            '## 2025-01-01\n'
                + '\n'
                + '- [x] one\n'
                + '- [ ] ~~two~~\n'
                + '- [ ] -\n'
                + '- [ ] three\n'
                + '- [x] four\n'
                + '- [ ] -\n'
                + '- [ ] ~~five~~\n'
                + '- [ ] six\n'
                + '- [ ] -\n'
                + '- [x] seven\n'
                + '- [ ] ~~eight~~\n'
                + '\n'
                + '## 2025-01-02\n'
                + '\n'
                + '- [x] one\n'
                + '- [ ] ~~two~~\n'
                + '- [ ] -\n'
                + '- [ ] three\n'
                + '- [x] four\n'
                + '- [ ] -\n'
                + '- [ ] ~~five~~\n'
                + '- [ ] six\n'
                + '- [ ] -\n'
                + '- [x] seven\n'
                + '- [ ] ~~eight~~\n',
        )

    def test_no_separator_between_identical_separators(self) -> None:
        no = models.RepetitionValue.NO
        yes = models.RepetitionValue.YES
        skip = models.RepetitionValue.SKIP

        import_representation = processing.format_habit_repetitions_by_date_to_markdown({
            date(2025, 1, 1): [
                self._create_habit_repetition(id=1, name='prefix, one', value=yes),
                self._create_habit_repetition(id=2, name='two', value=skip),
                self._create_habit_repetition(id=3, name='three', value=skip),
                self._create_habit_repetition(id=4, name='prefix, four', value=no),
            ],
            date(2025, 1, 2): [
                self._create_habit_repetition(id=1, name='prefix, one', value=yes),
                self._create_habit_repetition(id=2, name='two', value=skip),
                self._create_habit_repetition(id=3, name='three', value=skip),
                self._create_habit_repetition(id=4, name='prefix, four', value=no),
            ],
        })

        self.assertEqual(
            import_representation,
            '## 2025-01-01\n'
                + '\n'
                + '- [x] prefix, one\n'
                + '- [ ] ~~two~~\n'
                + '- [ ] ~~three~~\n'
                + '- [ ] prefix, four\n'
                + '\n'
                + '## 2025-01-02\n'
                + '\n'
                + '- [x] prefix, one\n'
                + '- [ ] ~~two~~\n'
                + '- [ ] ~~three~~\n'
                + '- [ ] prefix, four\n',
        )

    def test_no_separator_between_different_separators(self) -> None:
        no = models.RepetitionValue.NO
        yes = models.RepetitionValue.YES
        skip = models.RepetitionValue.SKIP

        import_representation = processing.format_habit_repetitions_by_date_to_markdown({
            date(2025, 1, 1): [
                self._create_habit_repetition(id=1, name='prefix #1, one', value=yes),
                self._create_habit_repetition(id=2, name='two', value=skip),
                self._create_habit_repetition(id=3, name='three', value=skip),
                self._create_habit_repetition(id=4, name='prefix #2, four', value=no),
            ],
            date(2025, 1, 2): [
                self._create_habit_repetition(id=1, name='prefix #1, one', value=yes),
                self._create_habit_repetition(id=2, name='two', value=skip),
                self._create_habit_repetition(id=3, name='three', value=skip),
                self._create_habit_repetition(id=4, name='prefix #2, four', value=no),
            ],
        })

        self.assertEqual(
            import_representation,
            '## 2025-01-01\n'
                + '\n'
                + '- [x] prefix #1, one\n'
                + '- [ ] ~~two~~\n'
                + '- [ ] ~~three~~\n'
                + '- [ ] -\n'
                + '- [ ] prefix #2, four\n'
                + '\n'
                + '## 2025-01-02\n'
                + '\n'
                + '- [x] prefix #1, one\n'
                + '- [ ] ~~two~~\n'
                + '- [ ] ~~three~~\n'
                + '- [ ] -\n'
                + '- [ ] prefix #2, four\n',
        )
