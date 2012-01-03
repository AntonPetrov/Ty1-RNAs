

import os, pdb

# ctfile = '/Users/anton/Dropbox/BGSU_shared/Data_from_Alain/644.ct'
#
# f = open(ctfile, 'r')
# lines = f.readlines()
#
# ofn = open('pseudoknot1', 'w')
# ind = 1
# for i in xrange(95*645):
#     if i % 645 == 0:
#         ofn.close()
#         ofn = open('pseudoknot'+str(ind),'w')
#         ind +=1
#     ofn.write(lines[i])
#
# removepseudoknot = '/Applications/RNAsoft/RNAstructure/exe/RemovePseudoknots'
# for i in xrange(1,96):
#     os.system('%s pseudoknot%i pseudoknot_removed%i.txt' % (removepseudoknot, i, i))


ct2dot = '/Applications/RNAsoft/RNAstructure/exe/ct2dot'
for i in xrange(1,96):
    os.system('%s pseudoknot_removed%i.txt 1 pseudoknot%i.bracket' % (ct2dot, i, i))

os.system('cat *.bracket > 95_pseudoknots_removed.txt')