#!/usr/bin/env python3.5

import argparse

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

    return parser.parse_args()

if __name__ == '__main__':
    options = parse_options()
    print(options)
