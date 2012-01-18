% this program creates a bash script which has to be run in terminal like so:
% bash matlab/webjar3d_bash_script.sh
% it runs webjar3d on all fasta files
% the resulting .html files are in ~/Dropbox/BGSURNA/Motifs
% fasta files are in ~/Dropbox/BGSURNA/Motifs/Sequences

% useful commands once everything is done:
% mv ~/Dropbox/BGSURNA/Motifs/str*html /Servers/rna.bgsu.edu/img/ty1/data_new/
% mv ~/Dropbox/BGSURNA/Motifs/Sequences/str*fasta /Servers/rna.bgsu.edu/img/ty1/data_new/

function [] = correspondenceParseExtract(input_type)

    global WEBJAR3D RUN_DIR;

    if input_type == 1
        % input for the original 7 alternative structures
        dotbracket  = '/Users/anton/Dropbox/BGSU_shared/Data_from_Alain/newRNAstruct644.bracket';
        prefix = 'str';
    elseif input_type == 2
        % input for the pseudoknot structures processed by the K2N webserver
        dotbracket  = '/Users/anton/Dropbox/BGSU_shared/Data_from_Alain/analysis/pseudoknot/pseudoknot_removed_dot_bracket.txt';    
        prefix = 'pseudoknot';
    elseif input_type == 3
        % input for the pseudoknot structures processed by RNAStructure
        dotbracket  = '/Users/anton/Dropbox/BGSU_shared/Data_from_Alain/analysis/95_pseudoknots_removed.txt';
        prefix = 'pseudo';       
    elseif input_type == 4
        % input for the rnaalifold predicted ty1, no shape
        dotbracket  = '/Users/anton/Dropbox/BGSU_shared/Data_from_Alain/analysis/rnaalifold_ty1_prediction.txt';
        prefix = 'ty1rnaalifold';        
    else
        error('Specify input_type');
    end
    
    clustalfile = '/Users/anton/Dropbox/BGSU_shared/Data_from_Alain/analysis/original_sequences/all_sequences_aligned.txt';


    WEBJAR3D   = '/Users/anton/Dropbox/BGSURNA/Motifs';
    RUN_DIR    = '/Users/anton/Dropbox/BGSURNA/Motifs/Sequences';
    
    ofn = sprintf('loops_%s.csv', prefix);
    fid = fopen(ofn, 'w');

    % get sequences and headers
    [S, H] = read_clustal_alignment_file(clustalfile);
    
    % get annotations, sequences and secondary structures 
    [a,s,ss] = read_dot_bracket_file(dotbracket);
    
    % map the sequence from dot-bracket file to the same sequence 
    % in the alignment, which is hardcoded at the moment.
    c = establish_correspondence(s{1},S(14,:));
    
    asterisks(1:length(S(:,1)),1) = '*';
    
    % loop over secondary structures
    for i = 1:length(ss)
    
        % get il and hl positions
        [il,hl] = aExtractSSFromDotBracket(ss{i});
        
        % process ils
        for j = 1:length(il(:,1))
            leftStrand  = S(:,c(il(j,1)):c(il(j,2)));
            rightStrand = S(:,c(il(j,3)):c(il(j,4)));
            loop = [leftStrand asterisks rightStrand];

            il_variants = get_sequence_variants(loop);
            
            % location, e.g. 15_20_50_55
            loc = sprintf('%i_%i_%i_%i',il(j,1),il(j,2),il(j,3),il(j,4));
            % id like str1_15_20_50_55
            id  = sprintf('%s%i_%s',prefix,i,loc);
            
            output_csv(il_variants, 'il');
        end
        
        % process hls
        for j = 1:length(hl(:,1))
            hairpin = S(:,c(hl(j,1)):c(hl(j,2)));
            hl_variants = get_sequence_variants(hairpin);

            loc = sprintf('%i_%i', hl(j,1),hl(j,2));
            id = sprintf('%s%i_%s',prefix,i,loc);

            output_csv(hl_variants, 'hl');
        end           
    end
    
    fclose(fid);
    fprintf('Done\n');
    

    function [] = output_csv(variants, loop_type)
        for k = 1:length(variants(:,1))
            fprintf(fid, '"%s%i","%s","%s","%s","%i"\n', prefix, ... 
                         i, loop_type, loc, variants{k,1}, variants{k,2});
        end                
    end

end

function [result] = get_sequence_variants(S)

    N = length(S(:,1));
    v = cell(1,N);

    for i = 1:N
        v{i} = S(i,:);        
    end

    [a,b,c] = unique(v);
    
    % x contains counts of each loop from a
    [x,y] = histc(c,1:length(a));
    
    result = cell(length(a),2);
    
    for i = 1:length(a)
        result{i,1} = strrep(a{i},'T','U');
        result{i,1} = strrep(result{i,1},'-','');        
        result{i,2} = x(i);
    end
    
end

function [c] = establish_correspondence(ss_seq, al_seq)

    c = 1:length(al_seq);
    c(strfind(al_seq,'-')) = [];

    if ~isequal(al_seq(c), ss_seq)
        error('Problem');
    end

end

function [T,H] = read_clustal_alignment_file(filename)

    fid = fopen(filename);    

    S{1} = [];
    H = {};
    
    tline = fgetl(fid);
    block = 1;
    while ischar(tline)
        if isempty(strfind(tline,'*')) && isempty(strfind(tline,'CLUSTAL')) && length(tline) > 1
            parts = regexp(tline,'\s+','split');
            S{block}(end+1,1:length(parts{2})) = parts{2};
            H{end+1} = parts{1};
        end
        if ~isempty(strfind(tline,'*'))
            block = block + 1;
            S{block} = '';
        end
        tline = fgetl(fid);
    end

    fclose(fid);    
    
    H = H(1:length(S{1}(:,1)));
    S = S(1:end-1); % discard last ''
    
    T = [];
    for i = 1:length(S)
        T = [T S{i}]; 
    end


end

function [a,s,ss] = read_dot_bracket_file(filename)

    fid = fopen(filename);
    ss  = {}; % secondary structure
    s   = {}; % primary sequence
    a   = {}; % annotation
    
    tline = fgetl(fid);
    while ischar(tline)
        if strfind(tline,'(')
            ss{end+1} = tline;
        elseif strfind(tline, '>')
            a{end+1} = tline;
        elseif length(tline) > 1
            s{end+1} = tline;
        end
            
        tline = fgetl(fid);
    end

    fclose(fid);    

end
