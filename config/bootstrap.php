<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Controller.php';
require_once __DIR__ . '/../app/Models/Customer.php';
require_once __DIR__ . '/../app/Models/Product.php';
require_once __DIR__ . '/../app/Models/Sale.php';
require_once __DIR__ . '/../app/Repositories/CustomerRepository.php';
require_once __DIR__ . '/../app/Repositories/ProductRepository.php';
require_once __DIR__ . '/../app/Repositories/SaleRepository.php';
require_once __DIR__ . '/../app/Services/SaleService.php';
require_once __DIR__ . '/../app/Controllers/CustomerController.php';
require_once __DIR__ . '/../app/Controllers/CartController.php';
require_once __DIR__ . '/../app/Controllers/ReportController.php';
