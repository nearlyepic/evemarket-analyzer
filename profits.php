<?php
$typeid = $_GET['typeid'];
$system = $_GET['system'];
$accounting = $_GET['acct'];

$evecentral = curl_init();
curl_setopt_array($evecentral, array(
	CURLOPT_URL=>'http://api.eve-central.com/api/marketstat?typeid='. $typeid . '&usesystem=' . $system,
	CURLOPT_RETURNTRANSFER=>1,
	CURLOPT_USERAGENT=>'nearlyepic eve market analyzer'));
$resp = curl_exec($evecentral);

$marketData = new DOMDocument();
$marketData->loadXML($resp);

$marketPrice = $marketData->getElementsByTagName('avg');

$avgBuyPrice = $marketPrice->item(0)->nodeValue;
$avgSellPrice = $marketPrice->item(1)->nodeValue;


