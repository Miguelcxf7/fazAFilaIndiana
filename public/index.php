<?php
require_once dirname(__DIR__).'/config/bootstrap.php';
$route=$_GET['route']??'cart';
try {
 switch($route){
  case 'customers': (new CustomerController())->index(); break;
  case 'customer-form': (new CustomerController())->form(); break;
  case 'customer-save': if($_SERVER['REQUEST_METHOD']==='POST')(new CustomerController())->save(); break;
  case 'customer-delete': if($_SERVER['REQUEST_METHOD']==='POST')(new CustomerController())->delete(); break;
  case 'cart': (new CartController())->index(); break;
  case 'cart-add': if($_SERVER['REQUEST_METHOD']==='POST')(new CartController())->add(); break;
  case 'cart-update': if($_SERVER['REQUEST_METHOD']==='POST')(new CartController())->update(); break;
  case 'cart-remove': if($_SERVER['REQUEST_METHOD']==='POST')(new CartController())->remove(); break;
  case 'checkout': if($_SERVER['REQUEST_METHOD']==='POST')(new CartController())->checkout(); break;
  case 'reports': (new ReportController())->index(); break;
  case 'sale-detail': (new ReportController())->detail(); break;
  case 'sale-cancel': if($_SERVER['REQUEST_METHOD']==='POST')(new ReportController())->cancel(); break;
  default: http_response_code(404); echo 'Página não encontrada.';
 }
} catch(Throwable $e) { http_response_code(500); echo '<div style="font-family:Arial;padding:2rem"><h2>Erro ao carregar o sistema</h2><p>'.htmlspecialchars($e->getMessage()).'</p><p>Confira o arquivo .env e execute database/schema.sql.</p></div>'; }
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);