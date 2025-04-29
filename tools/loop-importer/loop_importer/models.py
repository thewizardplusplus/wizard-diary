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

@dataclass
class HabitRepetition(dataclasses_json.DataClassJsonMixin):
    habit_id: int
    habit_name: str
    is_habit_archived: bool
    value: RepetitionValue

HabitRepetitionsByDate = Dict[datetime.date, List[HabitRepetition]]

@dataclass
class HabitRepetitionsByDateItem(dataclasses_json.DataClassJsonMixin):
    habit_repetitions: List[HabitRepetition]
    date: datetime.date = field(
        metadata=dataclasses_json.config(
            encoder=lambda date: date.isoformat(),
            mm_field=marshmallow.fields.DateTime(format="iso"),
        ),
    )

def iterate_over_habit_repetitions_by_date(
    habit_repetitions_by_date: HabitRepetitionsByDate,
) -> Iterable[HabitRepetitionsByDateItem]:
    return sorted(
        (
            HabitRepetitionsByDateItem(habit_repetitions=habit_repetitions, date=date)
            for date, habit_repetitions in habit_repetitions_by_date.items()
        ),
        key=lambda item: item.date,
    )
