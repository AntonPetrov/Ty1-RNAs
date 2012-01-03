function appletLoaded (){
    $('.exemplar:first').trigger('click');
    $('.exemplar:first').attr('checked','checked');
}

function show_first_instance_in_jmol(id,loopType) {
    jmolScript('zap;');
    if (loopType == 'IL') {
        jmolScript('load "http://rna.bgsu.edu/research/anton/share/iljun6/PDBDatabase/'+id+'/'+id+'_1.pdb";');
    } else {
        jmolScript('load "http://rna.bgsu.edu/research/anton/share/hljun2/PDBDatabase/'+id+'/'+id+'_1.pdb";');
    }
    apply_jmol_styling();
}

function apply_jmol_styling() {
        jmolScript('spacefill off;');
        jmolScript('select [U];color navy;');
        jmolScript('select [G]; color chartreuse;');
        jmolScript('select [C]; color gold;');
        jmolScript('select [A]; color red;');
        jmolScript('select 1.2; color grey; color translucent 0.8;');
        jmolScript('select protein; color purple; color translucent 0.8;');
        jmolScript('select 1.0;spacefill off;center 1.1;');
        jmolScript('frame *;display displayed and not 1.2;');
        jmolScript('select hetero;color pink;');
}

function position_jmol_applet() {
    $('#jmol').css('position','fixed');
    var offset_left = $('#left_content').offset().left + 500; // 530 = span9 width
    var offset_top  = $('#left_content').offset().top;
    $('#jmol').css('left',offset_left);
    $('#jmol').css('top', offset_top);
}