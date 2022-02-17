<?php

// This file is your starting point (= since it's the index)
// It will contain most of the logic, to prevent making a messy mix in the html

// This line makes PHP behave in a more strict way
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// We are going to use session variables so we need to enable sessions
session_start();

function pre_r(array $str)
{
    echo "<pre>";
    var_dump($str);
    echo "</pre>";
}

// Use this function when you need to need an overview of these variables
function whatIsHappening()
{
    // echo '<h2>$_GET</h2>';
    // var_dump($_GET);
    // pre_r($_GET);
    echo '<h2>$_POST</h2>';
    // var_dump($_POST);
    pre_r($_POST);
    // echo '<h2>$_COOKIE</h2>';
    // var_dump($_COOKIE);
    // echo '<h2>$_SESSION</h2>';
    // var_dump($_SESSION);
}

// TODO: provide some products (you may overwrite the example)
$products = [
    ['name' => 'Your favourite drink', 'price' => 2.5],
    ['name' => 'Your least-favourite drink', 'price' => 0.5],
    ['name' => 'Mediocre drink', 'price' => 1.5],
];

$totalValue = 0;

function validate()
{
    $invalidFields = [];

    foreach ($_POST as $fieldKey => $fieldValue) {
        if (empty($fieldValue)) { // check for empty fields
            array_push($invalidFields, [$fieldKey, "field required"]);
        } else if ($fieldKey === "zipcode") {
            if (!is_numeric($fieldValue)) { // check if zipcode contains only* numbers
                array_push($invalidFields, [$fieldKey, "invalid zipcode"]);
            }
        } else if ($fieldKey === "email") { // check if email is valid
            if (!filter_var($fieldValue, FILTER_VALIDATE_EMAIL)) {
                array_push($invalidFields, [$fieldKey, "invalid email"]);
            }
        }
    }

    // check if min 1 product has been selected
    if (!isset($_POST["products"])) {
        array_push($invalidFields, ["products", "min. 1 selection required"]);
    }

    // pre_r($invalidFields);
    // This function will send a list of invalid fields back
    return $invalidFields;
}

$validationErrors = [
    "email" => "",
    "street" => "",
    "streetnumber" => "",
    "city" => "",
    "zipcode" => "",
    "products" => "",
];

// function sanitizeData()
// {
//     // trim whitespace from strings in $_POST
//     foreach ($_POST as &$data) {
//         if (gettype($data) === "string") {
//             $data = trim($data);
//             $data = htmlspecialchars($data);
//         }
//     }
//     unset($data);
// }

function reportErrors($invalidFields)
{
    global $validationErrors;

    foreach ($invalidFields as $field) {
        $validationErrors[$field[0]] = $field[1];
    }
}

function handleForm()
{
    // TODO: form related tasks (step 1)

    // sanitizeData();
    // Validation (step 2)
    $invalidFields = validate();
    if (!empty($invalidFields)) {
        // TODO: handle errors
        reportErrors($invalidFields);
    } else {
        // TODO: handle successful submission
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    pre_r($_POST);
    handleForm();
}

$email = $_POST["email"] ?? "";
$street = $_POST["street"] ?? "";
$streetnumber = $_POST["streetnumber"] ?? "";
$city = $_POST["city"] ?? "";
$zipcode = $_POST["zipcode"] ?? "";

require 'form-view.php';
