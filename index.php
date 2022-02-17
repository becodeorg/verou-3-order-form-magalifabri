<?php

// SETUP

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();


// VARIABLES

$products = [
    ['name' => 'Your favourite drink', 'price' => 2.5],
    ['name' => 'Your least-favourite drink', 'price' => 0.5],
    ['name' => 'Mediocre drink', 'price' => 1.5],
];

$totalValue = 0;

$validationErrors = [
    "email" => "",
    "street" => "",
    "streetnumber" => "",
    "city" => "",
    "zipcode" => "",
    "products" => "",
];

$orderConfirmationMsg = "";


// DEV FUNCTIONS

function pre_r(array $str)
{
    echo "<pre>";
    var_dump($str);
    echo "</pre>";
}

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


// FUNCTIONS

function getOrderList($products)
{
    // global $products;
    $orderedProductsStr = "";

    foreach ($_POST["products"] as $key => $value) {
        $orderedProductsStr .= "- " . $products[$key]["name"] . "<br>";
    }

    return $orderedProductsStr;
}

function getAddress()
{
    return "${_POST['street']} ${_POST['streetnumber']}, ${_POST['city']}";
}

function reportSuccess($products)
{
    global $orderConfirmationMsg;

    $orderConfirmationMsg = "Thank you for ordering! <br><br>"
        . "Your order: <br>"
        . getOrderList($products)
        . "<br> Delivery to " . getAddress() . " will follow shortly";
}


function reportErrors($invalidFields)
{
    global $validationErrors;

    foreach ($invalidFields as $field) {
        $validationErrors[$field[0]] = $field[1];
    }
}


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

    return $invalidFields;
}


function handleForm($products)
{
    $invalidFields = validate();
    if (!empty($invalidFields)) {
        reportErrors($invalidFields);
    } else {
        reportSuccess($products);
    }
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    handleForm($products);
}


require 'form-view.php';
