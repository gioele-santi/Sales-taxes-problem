<?php

echo "Sales taxes problem\n";

$default_exempt_list = ["book", "chocolate", "pill"];

// input pattern: [unit_count] [imported (opt)] [name] at [unit_price]
function parse_input_line($input_line) {
    
    $words = explode(" ", $input_line);
    
    $unit_price = floatval(array_pop($words));
    $unit_count = intval(array_shift($words));
    $imported = in_array("imported", $words);
    
    if ($imported) {
        array_shift($words); //remove "imported"
        echo("\nImported: true");
    }
    
    array_pop($words); //remove "at"
    $name = implode(" ", $words);
    
    echo("\nUnit count: " . $unit_count );
    echo("\nUnit price: " . $unit_price );
    echo("\nName : " . $name );
    
}

function is_exempt($product_name, $exempt_list = []) {
    //mock, check if name contains exempted products
    return false;
}

$test1 = "1 imported box of chocolates at 10.00";

parse_input_line($test1);