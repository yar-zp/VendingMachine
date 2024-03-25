<?php

class VendingMachine
{
    private ProductManager $productManager;
    private const ACCEPTED_COINS = [0.01, 0.05, 0.10, 0.25, 0.50, 1.00];


    public function __construct(ProductManager $productManager)
    {
        $this->productManager = $productManager;
    }

    public function listProductsFromFile(string $filename): void
    {
        echo "======================Vending Machine======================\n";
        echo "\e[1mList of available products:\e[0m\n";

        $productsData = file_get_contents($filename);
        if ($productsData === false) {
            echo "Error reading product file.\n";
            return;
        }

        $lines = explode("\n", $productsData);
        foreach ($lines as $line) {
            $productInfo = explode(',', $line);
            if (count($productInfo) === 2) {
                $productName = $productInfo[0];
                $productPrice = (float)$productInfo[1];
                echo "$productName - $productPrice$\n";
            }
        }
    }

    public function selectProduct(string $productName): ?Product
    {
        $products = $this->getProductsFromFile('products.txt');
        foreach ($products as $product) {
            if ($product->getName() === $productName) {
                return $product;
            }
        }
        return null;
    }

    public function acceptCoin(CoinInterface $coin): void
    {
        if (in_array($coin->getValue(), self::ACCEPTED_COINS)) {
            $currentBalance = $this->getCurrentBalance();
            $currentBalance += $coin->getValue();
            $this->saveCurrentBalance($currentBalance);
            echo "======================Vending Machine======================\n";
            echo "Accepted {$coin->getValue()} coin\n";
            echo "Current balance: " . $this->getCurrentBalance() . "$" . "\n";
        } else {
            echo "======================Vending Machine======================\n";
            echo "We do not accept this coin: {$coin->getValue()}\n";
        }
    }

    public function buyProduct(Product $product): void
    {
        $currentBalance = $this->getCurrentBalance();
        if ($currentBalance >= $product->getPrice()) {
            $currentBalance -= $product->getPrice();
            $this->saveCurrentBalance($currentBalance);
            echo "======================Vending Machine======================\n";
            echo "You bought  {$product->getName()} for {$product->getPrice()}$ \n";
        } else {
            echo "======================Vending Machine======================\n";
            echo "Not enough money to buy {$product->getName()} \n";
        }
    }

    public function returnChange(): void
    {
        $currentBalance = $this->getCurrentBalance();
        if ($currentBalance > 0) {
            $this->saveCurrentBalance(0);
            echo "We return the rest: {$currentBalance}$ \n";
        }
    }

    public function addProduct(string $productName, float $price): void
    {
        $product = new Product($productName, $price);
        $this->productManager->addProduct($product);
        $this->saveProductsToFile();
        echo "Product '{$productName}' has been added to the assortment.\n";
    }

    function getCurrentBalance(): float
    {
        $balanceFile = 'balance.txt';
        if (file_exists($balanceFile)) {
            return (float)file_get_contents($balanceFile);
        } else {
            return 0.0;
        }
    }

    function saveCurrentBalance(float $balance): void
    {
        $balanceFile = 'balance.txt';
        file_put_contents($balanceFile, $balance);
    }

    private function saveProductsToFile(): void
    {
        $productsData = '';
        foreach ($this->productManager->getProducts() as $product) {
            $productsData .= $product->getName() . ',' . $product->getPrice() . "\n";
        }
        file_put_contents('products.txt', $productsData, FILE_APPEND);
    }

    public function getProductsFromFile(string $filename): array
    {
        $products = [];
        $productsData = file_get_contents($filename);
        if ($productsData === false) {
            echo "Error reading product file.\n";
            return $products;
        }
        $lines = explode("\n", $productsData);
        foreach ($lines as $line) {
            $productInfo = explode(',', $line);
            if (count($productInfo) === 2) {
                $productName = $productInfo[0];
                $productPrice = (float)$productInfo[1];
                $products[] = new Product($productName, $productPrice);
            }
        }

        return $products;
    }

    function commandRules(): void
    {
        echo "\e[1m-------------------------------------------------------------\e[0m\n";
        echo "               I                   list - List of products\n";
        echo "               N                   buy [назва] - Buy a product\n";
        echo "               F                   accept [значення] - Accept coin\n";
        echo "               O                   info [продукт] - Product info\n";
    }

}