import xerox

from . import logger

def copy_import_representation(import_representation):
    logger.get_logger().info('copy the import representation')

    xerox.copy(import_representation)

def output_import_representation(output_path, import_representation):
    logger.get_logger().info('output the import representation')

    with open(output_path + '.md', 'w') as output_file:
        output_file.write(import_representation)
