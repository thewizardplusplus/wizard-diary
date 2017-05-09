import logging

import xerox

from . import log

def copy_git_history(representation):
    log.log(logging.INFO, 'copy the git history')

    xerox.copy(representation)

def output_git_history(output_path, representation):
    log.log(logging.INFO, 'output the git history')

    with open(output_path + '.md', 'w') as output_file:
        output_file.write(representation)
