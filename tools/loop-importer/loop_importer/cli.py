import argparse

def parse_options():
    parser = argparse.ArgumentParser(
        prog=__package__.replace('_', '-'),
        formatter_class=argparse.ArgumentDefaultsHelpFormatter,
    )
    parser.add_argument('-d', '--db', required=True, help='DB path')
    parser.add_argument('-o', '--output', help='output path')
    parser.add_argument('-V', '--verbose', action='store_true', help='verbose logging')

    return parser.parse_args()
