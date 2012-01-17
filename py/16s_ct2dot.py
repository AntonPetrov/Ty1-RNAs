"""
This program converts 100 16S E.coli structures from ct to dot bracket format.
"""

import os, pdb

ctfile = '/Users/anton/Dropbox/BGSU_shared/Data_from_Alain/16S_with_SHAPE/ec_100.ct'
ct2dot = '/Applications/RNAsoft/RNAstructure/exe/ct2dot'
output_dir = '16S_bracket'

if not os.path.exists(output_dir):
    os.mkdir(output_dir)

for i in xrange(1,101):
    os.system('%s %s %i %s/16s_%i.txt' % (ct2dot, ctfile, i, output_dir, i))
