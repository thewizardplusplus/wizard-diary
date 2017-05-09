import logging

_ANSI_CODES = {
    'bold': '1',
    'black': '30',
    'green': '32',
    'yellow': '33',
    'blue': '34',
    'magenta': '35',
}

def init_log():
    logging.basicConfig(
        format=ansi('black', '%(asctime)s') + ' %(message)s',
        level=logging.INFO,
    )

def log(level, message):
    if level == logging.INFO:
        level = ansi('green', '[INFO]')
        message = ansi('bold', message)
    elif level == logging.DEBUG:
        level = ansi('blue', '[DEBUG]')

    logging.info(level + ' ' + message)

def ansi(code, text):
    return '\x1b[{}m{}\x1b[m'.format(_ANSI_CODES[code], text)
