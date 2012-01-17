"""

"""

import random, datetime, math, sys, pdb, csv, os, shutil, collections

from BeautifulSoup import BeautifulSoup          # For processing HTML

from sqlalchemy import *
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker

import sqlalchemy.exc

engine  = create_engine('mysql://root:bioinfo@localhost/ty1')

Session = sessionmaker(bind=engine)
session = Session()

Base = declarative_base()

class LoopLocations(Base):
    """
    """
    __tablename__ = 'loop_locations'
    job_id = Column(String(20), primary_key=True)
    ss_id  = Column(String(20), primary_key=True)
    location = Column(String(25), primary_key=True)
    loop_type = Column(String(2))
    seq   = Column(Text)
    count = Column(Integer)

class Jar3dResults(Base):
    """
    """
    __tablename__ = 'jar3d_results'
    job_id = Column(String(20), primary_key=True)
    ss_id  = Column(String(20), primary_key=True)
    location = Column(String(25), primary_key=True)
    result_id = Column(Integer, primary_key=True)
    group = Column(Text)
    mean_log_probability = Column(Float)
    median_log_probability = Column(Float)
    mean_percentile = Column(Float)
    median_percentile = Column(Float)
    mean_min_edit_distance = Column(Float)
    median_min_edit_distance = Column(Float)
    signature = Column(Text)

Base.metadata.create_all(engine)


def import_loop_distances():
    # "16s1","il","18_20_898_902","CAU*GCAAG","1"
    ifn = 'loops.csv'
    reader = csv.reader(open(ifn, 'rb'), delimiter=',', quotechar='"')
    for i, row in enumerate(reader):
        session.add(LoopLocations(job_id='16s',
                                  ss_id=row[0],
                                  loop_type=row[1],
                                  location=row[2],
                                  seq=row[3],
                                  count=row[4]))
    session.commit()


def select_unique_loops():
    """
    """
    return session.query(LoopLocations). \
                   filter(LoopLocations.job_id=='16s'). \
                   group_by(LoopLocations.location).all()

def generate_fasta_files(locations):
    """
    """
    RUN_DIR    = '/Users/anton/Dropbox/BGSURNA/Motifs/Sequences';

    for loc in locations:
        for loop in session.query(LoopLocations). \
                            filter(LoopLocations.job_id==loc.job_id).\
                            filter(LoopLocations.ss_id==loc.ss_id). \
                            filter(LoopLocations.location==loc.location).all():
            ofn = open(os.path.join(RUN_DIR, '%s.fasta' % loop.location), 'w')
            for i in xrange(loop.count):
                ofn.write('>%i times\n' % loop.count)
                ofn.write(loop.seq)
            ofn.close()

def run_jar3d(locations):
    """
    """
    WEBJAR3D   = '/Users/anton/Dropbox/BGSURNA/Motifs';
    for loc in locations:
        command = 'cd %s; java -jar webJAR3D_server.jar "%s" "%s.fasta"' \
                  % (WEBJAR3D, WEBJAR3D, loc.location)
        print command
        os.system(command)

def import_jar3d_results(locations):
    """
    """
    RESULTS = '/Users/anton/Dropbox/BGSURNA/Motifs'
    FASTALOC = '/Users/anton/Dropbox/BGSURNA/Motifs/Sequences'
    for loc in locations:
        ifn = os.path.join(RESULTS, '%s.html' % loc.location)
        f = open(ifn)
        html = f.read()
        soup = BeautifulSoup(html)
        table = soup.find('table')
        rows = table.findAll('tr')
        for i, tr in enumerate(rows):
            cols = tr.findAll('td')
            fields = []
            for td in cols:
                fields.append(''.join(td.find(text=True)))
            session.merge(Jar3dResults(
                job_id = '16s',
                ss_id  = loc.ss_id,
                location = loc.location,
                result_id = i+1,
                group = fields[0],
                mean_log_probability = fields[1],
                median_log_probability = fields[2],
                mean_percentile = fields[3],
                median_percentile = fields[4],
                mean_min_edit_distance = fields[5],
                median_min_edit_distance = fields[6],
                signature = fields[7]
            ))
        session.commit()
        f.close()
        os.remove(ifn)
        fasta = os.path.join(FASTALOC, '%s.fasta' % loc.location)
        os.remove(fasta)


def main(argv):
    """
    """
    import_loop_distances()
    locations = select_unique_loops()
    generate_fasta_files(locations)
    run_jar3d(locations)
    import_jar3d_results(locations)

if __name__ == "__main__":
    main(sys.argv[1:])