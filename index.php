<?php

error_reporting(E_ALL);
ini_set("display_errors", "On");

$backends = file_get_contents("services.json");
$backends = json_decode($backends, true);

$keys = file_get_contents(".secret.json");
$keys = json_decode($keys, true);

$is_login = isset($_REQUEST["state"]);
$token = "";

if ($is_login) {
	$state = json_decode($_REQUEST["state"], true);
	$url = $state["url"];
	$backend = $state["backend"];
	$code = $_REQUEST["code"];
	$info = $backends[$backend] + $keys[$backend];

	if ($code) {
		$ch = curl_init($info["url"]);

		$params = array_key_exists("fields", $info) ? "$info[fields]&" : "";
		$params = $params . "client_id=$info[client_id]&client_secret=$info[client_secret]&code=$code";

		if (isset($_REQUEST["redirect_uri"])) {
			$redirect_uri = $_REQUEST["redirect_uri"];
		}
		else {
			$protocol = ((!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") || $_SERVER["SERVER_PORT"] === 443) ? "https://" : "http://";
			$redirect_uri = $protocol . $_SERVER["HTTP_HOST"];
		}

		$params = $params . "&redirect_uri=$redirect_uri";

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json"]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

		$response = curl_exec($ch);

		curl_close($ch);

		$token = json_decode($response, true);
		$token = $token["access_token"];
	}
}

require "index.view.php";
