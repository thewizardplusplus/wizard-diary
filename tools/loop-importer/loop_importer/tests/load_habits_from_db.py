import sqlite3
import unittest
import tempfile
import os
import random
from datetime import date, datetime, timezone

from .. import db
from .. import models

class TestLoadHabitsFromDB(unittest.TestCase):
    def tearDown(self):
        os.remove(self.temp_db_path)

    def test_successful_load(self):
        no = models.RepetitionValue.NO
        yes = models.RepetitionValue.YES
        skip = models.RepetitionValue.SKIP

        self.temp_db_path = self._create_temp_db(
            habit_rows=[(1, 'one', 101, 0), (2, 'two', 102, 1)],
            repetition_rows=[
                (1, self._create_timestamp(date(2025, 1, 1)), yes.value),
                (1, self._create_timestamp(date(2025, 1, 2)), skip.value),
                (1, self._create_timestamp(date(2025, 1, 3)), no.value),
                (2, self._create_timestamp(date(2025, 1, 4)), yes.value),
                (2, self._create_timestamp(date(2025, 1, 5)), skip.value),
                (2, self._create_timestamp(date(2025, 1, 6)), no.value),
            ],
        )

        habits = db.load_habits_from_db(self.temp_db_path)

        self.assertEqual(habits, [
            models.Habit(
                id=1,
                name='one',
                position=101,
                repetitions=[
                    models.Repetition(habit_id=1, date=date(2025, 1, 1), value=yes),
                    models.Repetition(habit_id=1, date=date(2025, 1, 2), value=skip),
                    models.Repetition(habit_id=1, date=date(2025, 1, 3), value=no),
                ],
                is_archived=False,
            ),
            models.Habit(
                id=2,
                name='two',
                position=102,
                repetitions=[
                    models.Repetition(habit_id=2, date=date(2025, 1, 4), value=yes),
                    models.Repetition(habit_id=2, date=date(2025, 1, 5), value=skip),
                    models.Repetition(habit_id=2, date=date(2025, 1, 6), value=no),
                ],
                is_archived=True,
            ),
        ])

    def test_invalid_habit_id_type(self):
        self.temp_db_path = self._create_temp_db(
            habit_rows=[('invalid', 'one', 101, 0)],
            repetition_rows=[],
        )

        with self.assertRaisesRegex(ValueError, 'invalid type for the habit ID: invalid'):
            db.load_habits_from_db(self.temp_db_path)

    def test_invalid_habit_name_type(self):
        self.temp_db_path = self._create_temp_db(
            habit_rows=[(1, 23, 101, 0)],
            repetition_rows=[],
        )

        with self.assertRaisesRegex(ValueError, 'invalid type for the habit name: 23'):
            db.load_habits_from_db(self.temp_db_path)

    def test_invalid_habit_position_type(self):
        self.temp_db_path = self._create_temp_db(
            habit_rows=[(1, 'one', 'invalid', 0)],
            repetition_rows=[],
        )

        with self.assertRaisesRegex(ValueError, 'invalid type for the habit position: invalid'):
            db.load_habits_from_db(self.temp_db_path)

    def test_invalid_habit_archived_flag(self):
        self.temp_db_path = self._create_temp_db(
            habit_rows=[(1, 'one', 0, 23)],
            repetition_rows=[],
        )

        with self.assertRaisesRegex(ValueError, 'invalid value for the habit archived flag: 23'):
            db.load_habits_from_db(self.temp_db_path)

    def test_unknown_habit_id_in_repetition(self):
        self.temp_db_path = self._create_temp_db(
            habit_rows=[],
            repetition_rows=[
                (23, self._create_timestamp(date(2025, 1, 1)), models.RepetitionValue.YES.value),
            ],
        )

        with self.assertRaisesRegex(ValueError, 'repetition refers to an unknown habit ID: 23'):
            db.load_habits_from_db(self.temp_db_path)

    def test_invalid_repetition_timestamp_type(self):
        self.temp_db_path = self._create_temp_db(
            habit_rows=[(1, 'one', 101, 0)],
            repetition_rows=[(1, 'invalid', models.RepetitionValue.YES.value)],
        )

        with self.assertRaisesRegex(ValueError, 'invalid type for the repetition timestamp: invalid'):
            db.load_habits_from_db(self.temp_db_path)

    def test_invalid_repetition_value(self):
        self.temp_db_path = self._create_temp_db(
            habit_rows=[(1, 'one', 101, 0)],
            repetition_rows=[(1, self._create_timestamp(date(2025, 1, 1)), 23)],
        )

        with self.assertRaisesRegex(ValueError, 'invalid repetition value: 23'):
            db.load_habits_from_db(self.temp_db_path)

    def _create_temp_db(self, habit_rows, repetition_rows) -> str:
        temp_file = tempfile.NamedTemporaryFile(delete=False)
        connection = sqlite3.connect(temp_file.name)
        try:
            with connection:
                connection.execute('''
                    CREATE TABLE Habits (
                        id BLOB PRIMARY KEY,
                        name BLOB,
                        position BLOB,
                        archived BLOB
                    );
                ''')
                connection.execute('''
                    CREATE TABLE Repetitions (
                        habit BLOB,
                        timestamp BLOB,
                        value BLOB
                    );
                ''')
                connection.executemany(
                    'INSERT INTO Habits (id, name, position, archived) VALUES (?, ?, ?, ?);',
                    habit_rows,
                )
                connection.executemany(
                    'INSERT INTO Repetitions (habit, timestamp, value) VALUES (?, ?, ?);',
                    repetition_rows,
                )
        finally:
            connection.close()

        return temp_file.name

    def _create_timestamp(self, date: date) -> int:
        date_and_time = datetime(
            year=date.year,
            month=date.month,
            day=date.day,
            hour=random.randint(0, 23),
            minute=random.randint(0, 59),
            second=random.randint(0, 59),
            tzinfo=timezone.utc,
        )
        return int(date_and_time.timestamp() * 1000)
