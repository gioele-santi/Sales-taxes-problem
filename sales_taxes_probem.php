<?php

echo "Sales taxes problem\n";

$default_exempt_list = ["book", "chocolate", "pill", "bandage", "bandages"];

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
    $receipt_text = sprintf("%sSales Taxes: %.2f\nTotal: %.2f", $receipt_text, $taxes, $total);

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

// tests
function test_parse_input_line() {
    $success = true;
    $tests = array(
        array("in"=>"1 imported box of chocolates at 10.00",
            "out"=>array("name"=>"imported box of chocolates", "count"=>1, "price"=>10.0, "imported"=>true)
        ),
        array("in"=>"2 book at 12.49",
            "out"=>array("name"=>"book", "count"=>2, "price"=>12.49, "imported"=>false)
        ),
        array("in"=>"3 box of imported chocolates at 11.25",
            "out"=>array("name"=>"imported box of chocolates", "count"=>3, "price"=>11.25, "imported"=>true)
        )
    );

    foreach ($tests as $test) {
        $parse_test = parse_input_line($test["in"]);
        $diff_array = array_diff($parse_test, $test["out"]);
        
        $success = $success && (count($diff_array) == 0);
        if (! $success) {
            echo("Test input line parse: FAIL: \n");
            print_r($diff_array);
            return;
        }
    }
    
    echo "Test input line parse: SUCCESS\n";
}

function test_is_exempt($exempt_list = []) {
    $success = true;
    $tests = array(
        array("in"=>"imported box of chocolates", "out"=>true),
        array("in"=>"bar of chocolate", "out"=>true),
        array("in"=>"book", "out"=>true),
        array("in"=>"books", "out"=>true),
        
        array("in"=>"packet of headache pills", "out"=>true),
        array("in"=>"imported bottle of perfume", "out"=>false),
        array("in"=>"music CD", "out"=>false),
        );
    
    foreach ($tests as $test) {
        $exempt = is_exempt($test["in"], $exempt_list);
        $success = $success && ($exempt == $test["out"]);
        if (!$success) {
            echo "Test is exempt: FAIL\n";
            print_r($test);
            echo "return value was: " . ($exempt ? "TRUE": "FALSE");
            return;
        }
    }
    echo "Test is exempt: SUCCESS";
}

function test_make_receipt($exempt_list = []) {
    $tests = array(
        array("in"=> "2 book at 12.49
1 music CD at 14.99
1 chocolate bar at 0.85", 
"out"=> "2 book: 24.98
1 music CD: 16.49
1 chocolate bar: 0.85
Sales Taxes: 1.50
Total: 42.32"),
        array("in"=> "1 imported box of chocolates at 10.00
1 imported bottle of perfume at 47.50", 
"out"=> "1 imported box of chocolates: 10.50
1 imported bottle of perfume: 54.65
Sales Taxes: 7.65
Total: 65.15"),
        array("in"=> "1 imported bottle of perfume at 27.99
1 bottle of perfume at 18.99
1 packet of headache pills at 9.75
3 box of imported chocolates at 11.25", 
"out"=> "1 imported bottle of perfume: 32.19
1 bottle of perfume: 20.89
1 packet of headache pills: 9.75
3 imported box of chocolates: 35.55
Sales Taxes: 7.90
Total: 98.38"),
    );
    
    $success = true;
    
    foreach ($tests as $test) {
        $voices = parse_input($test["in"]);
        $receipt = make_receipt($voices, $exempt_list);
        
        if (strcmp($receipt, $test["out"]) != 0) {
            echo "Receipt test: FAIL\n";
            echo"Expected output: \n" .  $test["out"];
            echo"\n\nCurrent output: \n" .  $receipt;
            return;
        }
        
    }
    
    echo "Receipt test: SUCCESS";
}

function test_perc_round() {
    $tests = array(
        array("in"=>7.63, "out"=>7.65),
        array("in"=>0.00, "out"=>0.00),
        array("in"=>1.499, "out"=>1.50)
    );
    
    foreach ($tests as $test) {
        $round = perc_round($test["in"]);
        if($round != $test["out"]) {
            echo "\nTest rounding: FAIL";
            echo "\n\nError in rounding:";
            echo "\nExpected output: " . $test["out"];
            echo "\nCurrent output: " . $round;
            return;
        }
    }
    echo "\nTest rounding: SUCCESS";
}

//test_parse_input_line();
//test_is_exempt($default_exempt_list);
//test_perc_round();
//test_make_receipt($default_exempt_list);