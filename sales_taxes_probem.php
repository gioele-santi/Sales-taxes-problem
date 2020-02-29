<?php

echo "Sales taxes problem\n";

$default_exempt_list = ["book", "chocolate", "pill"];

// input line pattern: [unit_count] [imported (opt)] [name] at [unit_price]
// output: array with tagged elements
function parse_input_line($input_line) {
    
    $words = explode(" ", $input_line);
    
    $unit_price = floatval(array_pop($words));
    $unit_count = intval(array_shift($words));
    $imported = in_array("imported", $words);
    
    /*    //Removing not necessary 
    if ($imported) {
        array_shift($words); //remove "imported"
    }*/
    
    array_pop($words); //remove "at"
    $name = implode(" ", $words);
    
    return array("name"=>$name, "count"=>$unit_count, "price"=>$unit_price, "imported"=>$imported);
    
}

function parse_input($input) {
    $lines = explode(PHP_EOL, $input);
    $result = array();
    
    foreach ($lines as $line) {
        array_push($result, parse_input_line($line));
    }
    
    return $result;
}

// [mock] check if name contains exempted products
function is_exempt($product_name, $exempt_list = []) {
    return false;
}

function make_receipt($voices) {
    
    $receipt_text = "";
    $taxes = 0.0;
    $total = 0.0;
    
    foreach ($voices as $voice) {
        $line_total = $voice["count"] * $voice["price"];
        if ($voice["imported"]) {
            $line_total += $line_total * 0.05; //5%
        }
        
        if (! is_exempt($voice["name"])) {
            $line_tax = $line_total * 0.1; // 10%
            $line_total += $line_tax;
            $taxes += $line_tax;
        }
        
        $total += $line_total;
        $receipt_text = sprintf("%s\n%d %s: %.2f", $receipt_text, $voice["count"], $voice["name"], $line_total);
    }
    
    //add taxes and total
    $receipt_text = sprintf("%s\nSales Taxes: %.2f\nTotal: %.2f", $receipt_text, $taxes, $total);

    return $receipt_text;
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

//test_parse_input_line();

$input = "2 book at 12.49
1 music CD at 14.99
1 chocolate bar at 0.85";

$output = "2 book: 24.98
1 music CD: 16.49
1 chocolate bar: 0.85
Sales Taxes: 1.50
Total: 42.32";

$voices = parse_input($input);
print_r($voices);
$receipt = make_receipt($voices);
echo($receipt);