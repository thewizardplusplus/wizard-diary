import xerox

from . import logger

def copy_git_history(representation):
    logger.get_logger().info('copy the git history')

    xerox.copy(representation)

def output_git_history(output_path, representation):
    logger.get_logger().info('output the git history')

    with open(output_path + '.md', 'w') as output_file:
        output_file.write(representation)
