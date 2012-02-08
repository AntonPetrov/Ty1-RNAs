from sys import argv as args
import re
import string


def parse_coords(coords):
    coords = coords.split(':')[1].split(',')
    parsed = map(lambda s: s.strip().translate(translate), coords)
    parsed = '_'.join(parsed)
    return parsed

args = args[1:]

filename = args[0]
strand = open(filename, 'r')

translate = string.maketrans('-,', '__')
loops = []
patterns = ['***internal', '***hairpin']
line = strand.readline()
count = 0
while line:
    match = False
    for pattern in patterns:
        if line.find(pattern) >= 0:
            match = True

    if match:
        # print(line)
        line = strand.readline()
        parsed = parse_coords(line)
        count += 1
        print(parsed)
    else:
        line = strand.readline()
