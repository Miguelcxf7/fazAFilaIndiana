<?php
final class Customer {
    public function __construct(public ?int $id, public string $name, public ?string $email, public ?string $phone, public ?string $document) {}
}
