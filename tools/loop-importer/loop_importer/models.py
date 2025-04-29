import datetime
from enum import IntEnum
from dataclasses import dataclass, field
from typing import List, Dict, Iterable

import dataclasses_json
import marshmallow.fields

class RepetitionValue(IntEnum):
    NO = 0
    YES = 2
    SKIP = 3

@dataclass
class Repetition(dataclasses_json.DataClassJsonMixin):
    habit_id: int
    value: RepetitionValue
    date: datetime.date = field(
        metadata=dataclasses_json.config(
            encoder=lambda date: date.isoformat(),
            mm_field=marshmallow.fields.DateTime(format="iso"),
        ),
    )

@dataclass
class Habit(dataclasses_json.DataClassJsonMixin):
    id: int
    name: str
    repetitions: List[Repetition]
    is_archived: bool

HabitsByDate = Dict[datetime.date, List[Habit]]

@dataclass
class HabitsByDateItem(dataclasses_json.DataClassJsonMixin):
    habits: List[Habit]
    date: datetime.date = field(
        metadata=dataclasses_json.config(
            encoder=lambda date: date.isoformat(),
            mm_field=marshmallow.fields.DateTime(format="iso"),
        ),
    )

def iterate_over_habits_by_date(habits_by_date: HabitsByDate) -> Iterable[HabitsByDateItem]:
    return sorted(
        (
            HabitsByDateItem(habits=habits, date=date)
            for date, habits in habits_by_date.items()
        ),
        key=lambda item: item.date,
    )
