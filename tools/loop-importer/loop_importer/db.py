import sqlite3
from datetime import datetime, timezone
from typing import List
from contextlib import closing

from . import models

def load_habits_from_db(db_path: str) -> List[models.Habit]:
    habits = {}
    with closing(sqlite3.connect(db_path)) as conn:
        conn.row_factory = sqlite3.Row

        result = conn.execute('SELECT id, name, archived FROM Habits')
        for row in result.fetchall():
            habit_id = row['id']
            if not isinstance(habit_id, int):
                raise ValueError(f'invalid type for the habit ID: {habit_id}')

            name = row['name']
            if not isinstance(name, str):
                raise ValueError(f'invalid type for the habit name: {name}')

            archived = row['archived']
            if archived not in (0, 1):
                raise ValueError(f'invalid value for the archived flag: {archived}')

            habits[habit_id] = models.Habit(
                id=habit_id,
                name=name,
                is_archived=bool(archived),
                repetitions=[]
            )

        result = conn.execute('SELECT habit, timestamp, value FROM Repetitions')
        for row in result.fetchall():
            habit_id = row['habit']
            if habit_id not in habits:
                raise ValueError(f'repetition refers to an unknown habit ID: {habit_id}')

            timestamp = row['timestamp']
            if not isinstance(timestamp, int):
                raise ValueError(f'invalid type for the timestamp: {timestamp}')

            value = row['value']
            if value not in models.RepetitionValue._value2member_map_:
                raise ValueError(f'invalid repetition value: {value}')

            habits[habit_id].repetitions.append(models.Repetition(
                habit_id=habit_id,
                date=datetime.fromtimestamp(timestamp / 1000, timezone.utc).date(),
                value=models.RepetitionValue(value)
            ))

    return list(habits.values())
