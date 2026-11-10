<?php
final class Sale {
    public function __construct(public int $id, public ?int $customerId, public float $total, public string $status, public string $createdAt) {}
}
