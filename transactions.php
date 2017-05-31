<?php
session_start();
date_default_timezone_set("UTC"); //use UTC because it's equivalent to EVE time
$start = microtime(true); //record the time at which the page begins creation

require "vendor/autoload.php"; //load in phealng
require "functions.php"; //load in my custom functions
require "config.php";

Use Pheal\Pheal; //utilize library for the EVE API
Use Pheal\Core\Config;

try {
 Config::getInstance()->cache = new \Pheal\Cache\FileStorage($_SERVER['DOCUMENT_ROOT'].'/tmp/phealcache/'); //use file caching so we don't get banned from the EVE API


 Config::getInstance()->access = new \Pheal\Access\StaticCheck();
 } catch (PhealException $e) {
         echo get_class($e) . "<br/>";
         echo $e->getMessage() . "<br/>";
	die();
}

$evedb = new PDO('mysql:host=localhost;dbname=eve_dump', 'evedb', 'eve'); //connect to db
$evedb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //throw exceptions when something goes wrong (not like we're bothering to catch them yet, but whatever)
$evedb->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); //don't emulate preparing statements


$ply = new Pheal($keyid,$vcode,'account'); //create a new pheal object to get all characters on the account

$ply_chars = $ply->Characters();
$result = $ply_chars->toArray();

$charArray = $result['result']['characters']; //simplify things by being able to look in $charArray[0] instead of a triple nested array

if(isset($_GET['char'])) {
	$charNum = htmlspecialchars($_GET['char']); 
	if(!is_numeric($charNum)) {
		unset($charNum); //protect against people deliberately trying to break the website
	}
}

echo "<ul class=\"nav nav-tabs\">"; //start list of tabs for characters
foreach($charArray as $i=>$x) {
	if(isset($charNum) && $i==$charNum) {
		echo "<li role=\"presentation\"class=\"active\"><a href=\"#\">" . $x['name'] . "</a></li>";
	} elseif($i==0 && !isset($charNum)) {
		echo "<li role=\"presentation\"class=\"active\"><a href=\"#\">" . $x['name'] . "</a></li>";
	} else {
		echo "<li role=\"presentation\"><a href=\"overview.php?char=". $i . "\">" . $x['name'] . "</a></li>";
	}
}
echo "</ul>";

$charid = 0;
$charname = 0;

if(isset($charNum)) {
	$charid = $result["result"]["characters"][$charNum]["characterID"];
	$charname = $result["result"]["characters"][$charNum]["name"];
} else {
	$charid = $result["result"]["characters"][0]["characterID"];
	$charname = $result["result"]["characters"][0]["name"];
}



$char = new Pheal($keyid,$vcode,'char'); //new pheal object with 'char' scope to retrieve orders for a single character

$orders = $char->MarketOrders(array("characterID" => $charid));
$orders = $orders->toArray();

$acc_balance = $char->AccountBalance(array("characterID" => $charid));
$acc_balance = $acc_balance->toArray();

$acc_balance = $acc_balance["result"]["accounts"][0]["balance"]; //use the character's personal wallet, not a corp wallet

startDiv("row");
startDiv("col-md-8");
h3("Balance: " . number_format($acc_balance, 2) . " ISK");

$escrow = 0;

foreach($orders['result']['orders'] as $x) {
	if($x['orderState'] == 0 && $x['bid']) {
		$escrow += $x['price'] * $x['volEntered'];
	}
}

h4("ISK in escrow: " . number_format($escrow, 2));
endDiv();

startDiv("col-md-4");
h3("Character: " . $charname);
h4("EVE Time: " . date("m-d H:i"));
endDiv();
endDiv();
startDiv("col-md-6");
tablestart("Buy Orders, cached until: " . $orders['cachedUntil'] . " EVE", "order");

foreach ($orders['result']['orders'] as $x) {
	if($x['orderState'] == 0 && $x['bid']) {
		$typequery = doQuery($evedb, "SELECT typeName FROM invTypes WHERE typeID = ?", array($x['typeID']));
		rowstart("buy");
		tabledata($typequery[0]);
		tabledata(number_format($x['price'], 2));
		tabledata(number_format($x['volRemaining']) . "/" . number_format($x['volEntered']));
		rowend();
	}
}
tableend();
endDiv();

startDiv("col-md-6");

tablestart("Sell Orders, cached until: " . $orders['cachedUntil'] . " EVE", "order");

foreach ($orders['result']['orders'] as $x) {
	if($x['orderState'] == 0 && !$x['bid']) {
		$typequery = doQuery($evedb, "SELECT typeName FROM invTypes WHERE typeID = ?", array($x['typeID']));
		rowstart("sell");
		tabledata($typequery[0]);
		tabledata(number_format($x['price'], 2));
		tabledata(number_format($x['volRemaining']) . "/" . number_format($x['volEntered']));
		rowend();
	}
	
}

tableend();

endDiv();

echo "<br/>";

$transactions = $char->WalletTransactions(array("characterID" => $charid));
$transactions = $transactions->toArray();

tablestart("Latest 25 Transactions, cached until: " . $transactions['cachedUntil'] . " EVE", "transaction");
foreach($transactions['result']['transactions'] as $i=>$x) {
	//$x = $transactions['result']['transactions'][$i];
	if($i<=24) {
		//$typequery = doQuery($evedb, "SELECT typeName FROM invTypes WHERE typeID = ?", array($x['typeID']));
		if ($x['transactionType'] == "sell") {
			rowstart("sell");
		} else {
			rowstart("buy");
		}
		tabledata($x['transactionType']);
		tabledata($x['typeName']);
		tabledata(number_format($x['price'],2));
		tabledata(number_format($x['quantity']));
		tabledata(number_format(($x['price']*$x['quantity']), 2));
		tabledata(substr($x['transactionDateTime'], 5));
		rowend();
	}
}

tableend();
$end = microtime(true);
$time = ($end - $start);
$time = number_format($time, 3);
h4("This page rendered in: ". $time ." Seconds.");
?>
