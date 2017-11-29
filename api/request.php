<?php

require "../vendor/autoload.php";
require "../php/require.php";

if($_SERVER['REQUEST_METHOD'] == "GET"){
		$stopId = $_GET['id'];
		GetTimeCarrisSend($stopId);
		sleep(10);
		$receive = GetTimeCarrisReceive($stopId, date("Y-m-d H:i"));
		if($receive){
			echo $receive;
			http_response_code(200);
		}else{
			http_response_code(408);
		}
}else{
	http_response_code(405);
}
