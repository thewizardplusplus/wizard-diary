#!/usr/bin/env python3.5

import argparse
import datetime

import parsedatetime
import tzlocal

def parse_timestamp(value):
    try:
        timestamp = datetime.datetime.strptime(value, '%Y-%m-%dT%H:%M:%S%z')
    except Exception:
        timestamp, status = parsedatetime.Calendar().parseDT(value)
        if status == 0:
            raise argparse.ArgumentTypeError(
                'timestamp {} is incorrect'.format(value),
            )

        timestamp = tzlocal.get_localzone().localize(timestamp, is_dst=None)

    return timestamp

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
        type=parse_timestamp,
        required=True,
        help='a start timestamp of the repository log ' \
            + 'in ISO 8601 or human-readable formats',
    )

    return parser.parse_args()

if __name__ == '__main__':
    options = parse_options()
    print(options)
