<?php

header('Content-Type: application/json; charset=utf-8');

$serveraddress = "localhost";
$uid = "root";
$pwd = "";
$database = "foods";

$conn = new Mysqli($serveraddress, $uid, $pwd, $database);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["error" => "Connection error: " . $conn->connect_error]));
}

$conn->set_charset("utf8");

$query = "SELECT * FROM datas";
$result = $conn->query($query);

if (!$result) {
    http_response_code(500);
    die(json_encode(["error" => "Query error: " . $conn->error]));
}

$foods = $result->fetch_all(MYSQLI_ASSOC);
echo json_encode($foods);

$conn->close();

?>

