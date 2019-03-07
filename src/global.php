<?php

date_default_timezone_set("America/Sao_Paulo");
//date_default_timezone_set("Europe/Bucharest");
mb_internal_encoding("UTF-8");

/*
 * Set an error to exception
 * @type string
 * @info string
 * 
 */
function set_error($type, $info, $serialize = true) {
	
	if ( $serialize )
		return serialize( ["type" => $type, "info" => $info] );
	else
		return ["type" => $type, "info" => $info];
}

function set_success($type, $info) {
	return ["success" => ["type" => $type, "info" => $info]];
}

function finish($status, $code, $message, $array = null, $array_name = null) {
	
  echo json_encode([
    "status" 		=> $status,
    "code" 			=> $code,
    "message" 	=> $message,
		$array_name => $array
  ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit();
}

function get_data(&$array) {
	parse_str(http_build_query($_GET), $array);
}