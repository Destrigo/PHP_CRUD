<?php
echo "<h1>Welcome to PHP CRUD Learning!</h1>";

//Declarations
$name = "Alice";     // string
$age = 25;           // integer
$price = 19.99;      // float
$isAdmin = true;     // boolean

echo "$name is $age years old and costs $price USD. Admin? $isAdmin";

//Arrays
$fruits = ["Apple", "Banana", "Cherry"];
echo $fruits[0];  // Apple

$person = [
    "name" => "Alice",
    "age" => 25
];
echo $person["name"];  // Alice

//Conditional Statements
$age = 20;

if ($age >= 18) {
    echo "You are an adult.";
} else {
    echo "You are a minor.";
}

//Loops
$numbers = [1, 2, 3, 4, 5];

foreach($numbers as $num){
    echo "Number: $num <br>";
}
?>