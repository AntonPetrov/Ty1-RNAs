
fprintf('[');
for h=1:length(hl)
    fprintf('([');
    
    int = hl(h,1)+1:hl(h,2)-1;
    int = int - 1;
    for q = int
        if q ~= int(end)
            fprintf('%i,',q);
        else
            fprintf('%i',q);
        end        
    end
    
    fprintf(']),');
end
fprintf(']\n');