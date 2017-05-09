import argparse
import datetime

import parsedatetime
import tzlocal

def parse_options():
    parser = argparse.ArgumentParser(
        formatter_class=argparse.ArgumentDefaultsHelpFormatter,
    )
    parser.add_argument(
        '-r',
        '--repo',
        default='.',
        help='path to the repository',
    )
    parser.add_argument(
        '-R',
        '--revs',
        default='HEAD',
        help='revisions specifier in the git-rev-parse command format',
    )
    parser.add_argument(
        '-s',
        '--start',
        type=_parse_timestamp,
        help='a start timestamp of the repository log ' \
            + 'in ISO 8601 or human-readable formats',
    )
    parser.add_argument('-p', '--project', required=True, help='project name')
    parser.add_argument('-o', '--output', help='output path')
    parser.add_argument(
        '-V',
        '--verbose',
        action='store_true',
        help='verbose logging',
    )

    return parser.parse_args()

def _parse_timestamp(value):
    try:
        timestamp = datetime.datetime.strptime(value, '%Y-%m-%d')
    except Exception:
        try:
            timestamp = datetime.datetime.strptime(value, '%Y-%m-%dT%H:%M:%S')
        except Exception:
            timestamp, status = parsedatetime.Calendar().parseDT(value)
            if status == 0:
                raise argparse.ArgumentTypeError(
                    'timestamp {} is incorrect'.format(value),
                )

    return tzlocal.get_localzone().localize(timestamp, is_dst=None)
