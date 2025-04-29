import datetime
from enum import IntEnum
from dataclasses import dataclass, field
from typing import List

import dataclasses_json
import marshmallow.fields

def _date_field():
    return field(
        metadata=dataclasses_json.config(
            encoder=lambda date: date.isoformat(),
            mm_field=marshmallow.fields.Date(format='iso'),
        ),
    )

class RepetitionValue(IntEnum):
    NO = 0
    YES = 2
    SKIP = 3

@dataclass
class Repetition(dataclasses_json.DataClassJsonMixin):
    habit_id: int
    value: RepetitionValue
    date: datetime.date = _date_field()

@dataclass
class Habit(dataclasses_json.DataClassJsonMixin):
    id: int
    name: str
    position: int
    repetitions: List[Repetition]
    is_archived: bool
