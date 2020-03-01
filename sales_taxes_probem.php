<?php


// input line pattern: [unit_count] [imported (opt)] [name] at [unit_price]
// output: array with tagged elements
function parse_input_line($input_line) {
    
    $imported = (strpos($input_line, "imported") !== false); //position is not fixed
    $words = explode(" ", str_replace("imported ", "", $input_line)); //remove if present
    
    $unit_price = floatval(array_pop($words));
    $unit_count = intval(array_shift($words));
    
    array_pop($words); //remove "at"
    $name = implode(" ", $words);

    if ($imported) { //add imported at start of name
        $name = "imported " . $name;
    }
    
    return array("name"=>$name, "count"=>$unit_count, "price"=>$unit_price, "imported"=>$imported);
}

//split lines of input and apply parse_input_line function
//output: array containing details for each receipt line
function parse_input($input) {
    $lines = explode(PHP_EOL, $input);
    $result = array();
    
    foreach ($lines as $line) {
        array_push($result, parse_input_line($line));
    }
    
    return $result;
}

//input: a product name, a list of exempted products
//output: a bool, true if the name contains at least one of the provided words 
function is_exempt($product_name, $exempt_list = []) {
    foreach ($exempt_list as $word) {
        if(strpos($product_name, $word) !== false) {
            return true;
        } 
    }
    return false;
}

//input: arrays with parsed product details
//output: string with receipt text
function make_receipt($voices, $exempt_list = []) {
    
    $receipt_text = "";
    $taxes = 0.0;
    $total = 0.0;
    
    foreach ($voices as $voice) {
        $line_total = $voice["count"] * $voice["price"];
        //check import
        $import_tax = 0.00;
        if ($voice["imported"]) {
            $import_tax =  $voice["count"] * perc_round($voice["price"] * 0.05);
        }

        //check exemption
        $sale_tax = 0.00;
        if (! is_exempt($voice["name"], $exempt_list)) {
            $sale_tax = perc_round($line_total * 0.1); // 10%
        }
        
        $line_total += $sale_tax + $import_tax;
        $taxes += $sale_tax + $import_tax;
        
        $total += $line_total;
        $receipt_text = $receipt_text . sprintf("%d %s: %.2f\n", $voice["count"], $voice["name"], $line_total);
    }
    
    //add taxes and total
    $receipt_text = sprintf("%sSales Taxes: %.2f\nTotal: %.2f\n", $receipt_text, $taxes, $total);

    return $receipt_text;
}

//input: decimal number
//output: number rounded up to the nearest 0.05
function perc_round($value) {
    //be sure to keep inly 2 decimal
    $two_dec = round($value ,2,PHP_ROUND_HALF_UP);
    $integer = $two_dec * 100;
    $rem = $integer % 5;
    if ($rem == 0) {
        return $two_dec; // no need to round
    }
    $integer = $integer + 5 - $rem;
    return $integer/100;
}