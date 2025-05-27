<?php

$employees = [
    ['name' => 'John', 'city' => 'Dallas'],
    ['name' => 'Jane', 'city' => 'Austin'],
    ['name' => 'Jake', 'city' => 'Dallas'],
    ['name' => 'Jill', 'city' => 'Dallas'],
];

$offices = [
    ['office' => 'Dallas HQ', 'city' => 'Dallas'],
    ['office' => 'Dallas South', 'city' => 'Dallas'],
    ['office' => 'Austin Branch', 'city' => 'Austin'],
];

$output = [
    "Dallas" => [
        "Dallas HQ" => ["John", "Jake", "Jill"],
        "Dallas South" => ["John", "Jake", "Jill"],
    ],
    "Austin" => [
        "Austin Branch" => ["Jane"],
    ],
];

// write elegant code using collections to generate the $output array. 
//your code goes here..

// ============================ SOLUTION ===============================

/*
    I tested the code and it workss as expected 
*/

function getOutput($employees, $offices)
{
    $byCity = [];

    // Group employees by city
    foreach ($employees as $e) {
        $byCity[$e['city']][] = $e['name'];
    }

    $output = [];

    
    foreach ($offices as $office) {
        $city = $office['city'];
        $officeName = $office['office'];
        
        // Assign the lisst of employees for the city
        $output[$city][$officeName] = $byCity[$city] ?? [];
    }

    return $output;
}


$output = getOutput($employees, $offices);

print_r($output);