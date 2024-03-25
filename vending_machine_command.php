<?php

require_once 'src/Balance/CoinInterface.php';
require_once 'src/Balance/Coin.php';
require_once 'src/Products/Product.php';
require_once 'src/Products/ProductManager.php';
require_once 'src/VendingMachine.php';


$productManager = new ProductManager();
$vendingMachine = new VendingMachine($productManager);

if ($argc < 2) {
    echo "Usage: php vending_machine_command.php [command]\n";
    exit(1);
}

$command = $argv[1];
switch ($command) {

    case 'list':
        $vendingMachine->listProductsFromFile('products.txt');
        $vendingMachine->commandRules();
        break;

    case 'buy':
        if ($argc < 3) {
            echo "Usage: php vending_machine_command.php buy [product_name]\n";
            exit(1);
        }
        $productName = $argv[2];
        $selectedProduct = $vendingMachine->selectProduct($productName);
        if ($selectedProduct !== null) {
            $vendingMachine->buyProduct($selectedProduct);
            $vendingMachine->returnChange();
            $vendingMachine->commandRules();
        } else {
            echo "Product named '{$productName}' not found\n";
        }
        break;

    case 'accept':
        if ($argc < 3) {
            echo "Usage: php vending_machine_command.php accept [coin_value]\n";
            exit(1);
        }
        $coinValue = (float)$argv[2];
        $coin = new Coin($coinValue);
        $vendingMachine->acceptCoin($coin);
        $vendingMachine->commandRules();
        break;

    case 'info':
        if ($argc < 3) {
            echo "Usage: php vending_machine_command.php info [product_name]\n";
            exit(1);
        }
        $productName = $argv[2];
        $selectedProduct = $vendingMachine->selectProduct($productName);
        if ($selectedProduct !== null) {
            echo "{$selectedProduct->getName()} коштує {$selectedProduct->getPrice()} \n";
            $vendingMachine->commandRules();
        } else {
            echo "Product named '{$productName}' not found\n";
        }
        break;

    case 'add_product':
        if ($argc < 4) {
            echo "Usage: php vending_machine_command.php add_product [product_name] [price]\n";
            exit(1);
        }
        $productName = $argv[2];
        $price = (float)$argv[3];
        $vendingMachine->addProduct($productName, $price);
        break;

    default:
        echo "Unknown command '{$command}'\n";
        exit(1);
}

