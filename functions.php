<?php

//inserts a string $data inbetween table data tags in HTML
function tabledata($data) {
	echo "<td>" . $data . "</td>\n";
}
//creates table rows with certain styles, depending on if it's a sell order or buy order. also applies to transactions.
function rowstart($type) {
	if ($type == "sell") {
		echo"<tr style=\"background-color:#BADA55;\">\n";
	} elseif ($type == "buy") {
		echo"<tr style=\"background-color:#7555da; color:#EEEEEE;\">\n";
	} else { 
		echo"<tr>\n";
	}
}
//Ends a row.
function rowend() {
	echo "</tr>\n";
}

function tablestart($caption, $type) 
{
	
if($type == "transaction") {
	echo "<table class=\"table table-hover\">\n";
	echo "<caption>" . $caption . "</caption>\n";
	echo "<th>Type</th> <th>Item</th> <th>Price/Unit</th> <th>Quantity</th> <th>Subtotal</th> <th>Time</th>\n";
} else {
	echo "<table id=\"orders\" class=\"table table-hover\">\n";
	echo "<caption>" . $caption . "</caption>\n";
	echo"<th>Item</th> <th>Price</th> <th>Quantity</th>\n";
}

}
function customTable($caption, $columns)
{
	echo "<table id=\"orders\" class=\"table table-hover\">\n";
	echo "<caption>" . $caption . "</caption>\n";
	foreach($colums as $x) {
		echo "<th>" . $x . "</th>";
	}
}
function tableend() {
	echo "</table>\n";
}

function startHTML() {
	echo"<!DOCTYPE html>\n<html lang=\"en\"l>\n";
}

function endHTML() {
	echo"</body>\n\n</html>";
}

function startDiv($id) {
	echo"<div class=\"" . $id . "\">\n";
}

function endDiv() {
	echo"</div>\n";
}
function h1($content) {
	echo"<h1>" . $content . "</h1>\n";
}

function h2($content) {
	echo"<h2>" . $content . "</h2>\n";
}

function h3($content) {
	echo"<h3>" . $content . "</h3>\n";
}

function h4($content) {
	echo"<h4>" . $content . "</h4>\n";
}

function doQuery($pdo, $query, $parameters)
{
	//takes a PDO object, a query string, and an array of parameters, and binds them all and then executes. returns the fetch() output.
	//this is really the only function of importance in this document. all the other functions are just to make the code look nice.
	$pdoQuery = $pdo->prepare($query);
	foreach ($parameters as $key=>$x) {
		$pdoQuery->bindParam($key+1, $x);
	}
	$pdoQuery->execute();
	return $pdoQuery->fetch();
}
	
function htmlImage($image)
{
	echo"<img src=\"". $image . "\" alt=\"\">\n";
}
?>
