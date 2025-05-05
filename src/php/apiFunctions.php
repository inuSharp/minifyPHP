<?php

function responseJson($ary = [], $status = 200)
{
    header('Content-Type: application/json; charset=utf-8');
    if ($status != 200 && !isCommandLineInterface()) {
        http_response_code($status);
    }
    if (is_array($ary) || is_object($ary)) {
        echo json_encode($ary);
    } else {
        echo $ary;
    }
}
function isCommandLineInterface()
{
    return (php_sapi_name() === 'cli');
}
function request($key = null, $default=null)
{
    if (is_null($key)) {
        return null;
    }
    static $request;
    if (is_null($request) || isCommandLineInterface()) {
        if (!isCommandLineInterface()) {
        //if ($_SERVER["REQUEST_METHOD"] == "PUT") {
            $jsonData = file_get_contents('php://input');
            //$jsonData = explode("&", $jsonData);
            //foreach ($jsonData as $value) {
            //    $row = explode("=", $value);
            //    $request[$row[0]] = $row[1];
            //}
            $jsonData = json_decode($jsonData, true);
            $request = [];
            if (is_array($jsonData)) {
                foreach ($jsonData as $name => $value) {
                    $request[$name] = $value;
                }
            }
            $request = array_merge($request, $_GET);

        //} else {
        //    $request = $_GET + $_POST;
        //}
        } else {
            $request = getCommandRequest();
        }
    }
    Log::info($request);

    if (!array_key_exists($key, $request)) {
        return $default;
    }

    return $request[$key];
}
$commandRqeust = [];
function setCommandRequest($data) {
    global $commandRqeust;
    if (!isCommandLineInterface()) {
        return;
    }
    $commandRqeust = $data;
    request();
}
function getCommandRequest() {
    global $commandRqeust;
    if (!isCommandLineInterface()) {
        return [];
    } else {
        return $commandRqeust;
    }
}
