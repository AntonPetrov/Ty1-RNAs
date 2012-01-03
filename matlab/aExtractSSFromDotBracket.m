function [il_positions,hl_positions] = aExtractSSFromDotBracket(seq)
    
    il_positions = [];
    hl_positions = [];
    L = length(seq);
    
    dots = length(strfind(seq,'.'));
    brackets = length(strfind(seq,'(')) + length(strfind(seq,')'));    
    if dots + brackets ~= L
%         seq = strrep(seq,'[','.');
%         seq = strrep(seq,']','.');        
%         disp('Pseudoknot detected. Replaced all [ and ] with .');
        disp('Pseudoknot detected. Exiting');
        return;
    end
    
    matrix = rnaconvert(seq); % seq = dot-bracket notation
    matrix = matrix + triu(matrix)'; % make symmetric
    
    conn = zeros(1,L); % find what is connected to what
    for i = 1:L
        pair = find(matrix(i,:),1);
        if ~isempty(pair)
            conn(i) = pair;
        end
    end

    i = find(conn,1); % start at the first stem
    N = find(conn,1,'last'); % stop at the end of the last stem
    
    while i < N-1
        
        j = i;
        
        while conn(j+1) == 0 && j < N-1 % find stretches of consequtive zeros
            j = j+1;
        end
        
        if j ~= i  
            flankss1 = seq(i:j+1);
            ind = sort([conn(i) conn(j+1)]);
            flankss2 = seq(ind(1):ind(2));
                        
            if strcmp(seq(i),'(') && strcmp(seq(j+1),')') %HL
                hl_positions = [hl_positions; i j+1];
%                 fprintf('Potential HL: %s\n',flankss1);
            else
                dots1 = length(strfind(flankss1,'.'));
                dots2 = length(strfind(flankss2,'.'));
                if (dots1>0) && (dots2>0) && (dots1 == length(flankss1)-2) && ...
                        (dots2 == length(flankss2)-2) % only dots in the middle
                    il_positions = [il_positions; sort([i j+1 ind(1) ind(2)])];
%                 else

                end
            end
            
%             fprintf('Stretch %i to %i\n',i,j);
        end        
        
        i = j+1;        
    end
    
    il_positions = unique(il_positions,'rows');
    hl_positions = unique(hl_positions,'rows');    
    
%     keyboard;







end