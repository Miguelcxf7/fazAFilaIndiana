<?php
final class Product {
    public function __construct(public int $id, public string $name, public int $stock, public float $price) {}
}
