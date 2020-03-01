<?php

include 'sales_taxes_probem.php';

if ($argc <=1) {
    echo "Missing arguments. Provide at least an input file.\n";
}
else {
    //load input
    $input_path = $argv[1];
    $input = file_get_contents($input_path);
    
    echo "\nLoaded input:\n\n";
    
    echo $input;
    echo "\n\n";
    
    $exempt_list = [];
    if ($argc == 3) {
        //load exempt dictionary
        $exempt_path = $argv[2];
        $exempt_string = file_get_contents($exempt_path);
        $exempt_string = str_replace(" ", "", $exempt_string); //remove empty spaces
        $exempt_list = array_filter(explode("\n", $exempt_string), "strlen"); //a word on each line, remove empty ones
    }
    
    $voices = parse_input($input);
    $receipt = make_receipt($voices, $exempt_list);
    
    echo "\nOutput:\n\n";
    
    echo $receipt;
    echo "\n\n";
}


