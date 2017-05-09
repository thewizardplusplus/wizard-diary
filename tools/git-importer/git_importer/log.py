import logging

import termcolor

def init_log():
    logging.basicConfig(
        format=termcolor.colored('%(asctime)s', 'grey') + ' %(message)s',
        level=logging.INFO,
    )

def log(level, message):
    if level == logging.INFO:
        level = termcolor.colored('[INFO]', 'green')
        message = termcolor.colored(message, attrs=['bold'])
    elif level == logging.DEBUG:
        level = termcolor.colored('[DEBUG]', 'blue')

    logging.info(level + ' ' + message)
