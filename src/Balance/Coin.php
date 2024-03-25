<?php

class Coin implements CoinInterface {
private float $value;

public function __construct(float $value) {
$this->value = $value;
}

public function getValue(): float {
return $this->value;
}
}