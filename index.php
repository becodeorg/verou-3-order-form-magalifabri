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

if ($_GET["food"] === "1") {
    $products = [
        ['name' => 'Your favourite food', 'price' => 4.5],
        ['name' => 'Your least-favourite food', 'price' => 2.5],
        ['name' => 'Mediocre food', 'price' => 3.5],
    ];
}

$totalValue = 0;

$validationErrors = [
    "email" => "",
    "street" => "",
    "streetnumber" => "",
    "city" => "",
    "zipcode" => "",
    "products" => "",
    "delivery" => "",
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
    // echo '<h2>$_POST</h2>';
    // pre_r($_POST);
    // echo '<h2>$_COOKIE</h2>';
    // var_dump($_COOKIE);
    echo '<h2>$_SESSION</h2>';
    pre_r($_SESSION);
}


// FUNCTIONS

function setSessionsVars()
{
    $_SESSION["street"] = $_POST["street"];
    $_SESSION["streetnumber"] = $_POST["streetnumber"];
    $_SESSION["city"] = $_POST["city"];
    $_SESSION["zipcode"] = $_POST["zipcode"];
}


function getAddress()
{
    return "${_POST['street']} ${_POST['streetnumber']}, ${_POST['city']}";
}

function getDeliveryText()
{
    if ($_POST["delivery"] === "normal") {
        return "<br> Delivery to " . getAddress() . " within 2 hours";
    } else {
        return "<br> Express delivery to " . getAddress() . " within 45 minutes";
    }
}

function getTotalCost($products)
{
    $total = 0;

    foreach ($_POST["products"] as $key => $value) {
        if ($value) {
            $total += $value *  $products[$key]["price"];
        }
    }

    if ($_POST["delivery"] === "express") {
        $total += 5;
    }

    return $total;
}

function getOrderList($products)
{
    $orderedProductsStr = "";

    foreach ($_POST["products"] as $key => $value) {
        if ($value) {
            $orderedProductsStr .= "- " . $value . " x " . $products[$key]["name"] . "<br>";
        }
    }

    return $orderedProductsStr;
}

function reportSuccess($products)
{
    global $orderConfirmationMsg;

    $orderConfirmationMsg = "Thank you for ordering! <br><br>"
        . "Your order: <br>"
        . getOrderList($products) . "<br>"
        . "Total: €" . getTotalCost($products) . "<br>"
        . getDeliveryText();
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
    $productOrdered = false;

    foreach ($_POST as $fieldKey => $fieldValue) {
        if (empty($fieldValue)) { // check for empty fields
            array_push($invalidFields, [$fieldKey, "field required"]);
        } else if ($fieldKey === "zipcode") {
            if (!filter_var($fieldValue, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0, "max_range" => 999999]])) {
                array_push($invalidFields, [$fieldKey, "invalid zipcode"]);
            }
        } else if ($fieldKey === "email") { // check if email is valid
            if (!filter_var($fieldValue, FILTER_VALIDATE_EMAIL)) {
                array_push($invalidFields, [$fieldKey, "invalid email"]);
            }
        }
    }

    // check delivery option selection
    if (empty($_POST["delivery"])) {
        array_push($invalidFields, ["delivery", "select a delivery option"]);
    }

    // check product selection
    foreach ($_POST["products"] as $index => $numberOrdered) {
        if ($numberOrdered) {
            if (
                !is_numeric($numberOrdered)
                || $numberOrdered < 0
            ) {
                array_push($invalidFields, ["products", "invalid input given"]);
                return $invalidFields;
            }
            $productOrdered = true;
        }
    }

    if (!$productOrdered) {
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
        setSessionsVars();
    }
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // pre_r($_POST);
    handleForm($products);
}

require 'form-view.php';
