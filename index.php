<?php

require 'vendor/autoload.php';

$token = '2709b58674fdf39af2cb0d2b4f02157fbb7c9dd5183afc859f6c974e2f9f3dbc97a85b43ce72745c97089bb01feca665bf7d6dc295489c5a8769cfaa550edfdd';

$copernica = new \TomKriek\CopernicaAPI\CopernicaAPI($token);

$test = new \TomKriek\CopernicaAPI\CopernicaAPI($token);

$data = [
    'name' => 'Test123',
    'description' => 'Description of test'
];

$b = $test->database(1)->get();

echo '<pre>';
print_r($b);
echo '</pre>';

//echo "<pre>";
//print_r($x);
//echo "</pre>";

