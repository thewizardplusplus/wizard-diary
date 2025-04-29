import datetime
from enum import IntEnum
from dataclasses import dataclass, field
from typing import List

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
