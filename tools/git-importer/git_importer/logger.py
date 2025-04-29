import logging

import termcolor

class Formatter(logging.Formatter):
    def format(self, record):
        if record.levelno == logging.INFO:
            record.msg = termcolor.colored(record.msg, attrs=['bold'])

        message = super().format(record)
        for level, color in _LEVELS_COLORS.items():
            message = message.replace(
                level,
                termcolor.colored('[{}]'.format(level), color),
            )

        return message

_LEVELS_COLORS = {
    'DEBUG': 'blue',
    'INFO': 'green',
}

def get_logger():
    return logging.getLogger(__package__)

def init_logger(verbose):
    handler = logging.StreamHandler()
    handler.setFormatter(Formatter(
        fmt=termcolor.colored('%(asctime)s', 'grey', attrs=['dark']) \
            + ' %(levelname)s %(message)s',
    ))

    get_logger().addHandler(handler)
    get_logger().setLevel(logging.DEBUG if verbose else logging.INFO)
