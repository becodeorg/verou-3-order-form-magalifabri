<?php

// SETUP

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();


// VARIABLES

require "./products.php";

$totalValue = $_COOKIE["totalValue"] ?? 0;

$orderHistoryUl = createOrderHistoryUl($products);

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
    // pre_r($_GET);
    // echo '<h2>$_POST</h2>';
    // pre_r($_POST);
    // echo '<h2>$_COOKIE</h2>';
    // pre_r($_COOKIE);
    echo '<h2>$_SESSION</h2>';
    pre_r($_SESSION);
}


// FUNCTIONS

function createOrderHistoryUl($products)
{
    $orderHistoryUl = "<ul>";

    foreach ($products as $index => $productArray) {
        $productNameAsCookieKey = str_replace(" ", "-", $productArray["name"]);
        if (!empty($_COOKIE[$productNameAsCookieKey])) {
            $orderHistoryUl .= "<li>" . $productArray["name"] . " x " . $_COOKIE[$productNameAsCookieKey] . "</li>";
        }
    }

    $orderHistoryUl .= "</ul>";

    return $orderHistoryUl;
}


function storeOrderInfo($products, $totalValue)
{
    setcookie("totalValue", "$totalValue", time() + 60 * 60 * 24 * 30);

    foreach ($_POST["products"] as $index => $numberOrdered) {
        if ($numberOrdered) {
            $productName = str_replace(" ", "-", $products[$index]["name"]);
            if (!empty($_COOKIE[$productName])) {
                $currentNumOrdered = $_COOKIE[$productName];
                $newNumOrdered = $currentNumOrdered + $numberOrdered;
                setcookie($productName, "$newNumOrdered", time() + 60 * 60 * 24 * 30);
            } else {
                setcookie($productName, "$numberOrdered", time() + 60 * 60 * 24 * 30);
            }
        }
    }
}


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
        return "<br> Delivery to " . getAddress() . " within <b>2 hours</b>";
    } else {
        return "<br> Express delivery to " . getAddress() . " within <b>45 minutes</b>";
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

function reportSuccess($products, $totalOrderCost)
{
    global $orderConfirmationMsg;

    $orderConfirmationMsg = "Thanks for your order! <br><br>"
        . "Order overview: <br>"
        . getOrderList($products)
        // . "<hr>"
        . "Total: €" . $totalOrderCost . "<br>"
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


function handleForm(array $products): float
{
    $invalidFields = validate();
    if (!empty($invalidFields)) {
        reportErrors($invalidFields);
        return 0;
    } else {
        $totalOrderCost = getTotalCost($products);
        reportSuccess($products, $totalOrderCost);
        setSessionsVars();
        return $totalOrderCost;
    }
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $totalOrderCost = handleForm($products);

    if ($totalOrderCost) { // 0 means validation failed
        $totalValue += $totalOrderCost;
        storeOrderInfo($products, $totalValue);
        $orderHistoryUl = createOrderHistoryUl($products);
    }
}

require 'form-view.php';
