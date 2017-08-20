<?php
require_once '../../config.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

$method = varStr('method');

$error = '';
$result = [];

switch ($method) {

	case 'orders.add': list($result, $error) = orders::add(
		varStr('title'),
		varStr('description'),
		varInt('reward')
	); break;
	case 'orders.get': list($result, $error) = orders::get(varStr('act')); break;
	case 'orders.do': list($result, $error) = orders::doOrder(varInt('orderID')); break;
	case 'orders.finish': list($result, $error) = orders::finishOrder(varInt('orderID')); break;

	case 'balance.refill': list($result, $error) = balance::refill(varInt('amount')); break;
	case 'balance.withdraw': list($result, $error) = balance::withdraw(varInt('amount')); break;

	case 'auth.checkHash': list($result, $error) = auth::checkHash(varStr('hash')); break;


	default:
		$error = 'Unknown method';
		break;
}

echo json_encode([
	'success' => $error === '',
	'error' => $error,
	'result' => $result
], 256);