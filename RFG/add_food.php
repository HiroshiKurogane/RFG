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

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['title']) || !isset($data['description']) || !isset($data['recipe_url'])) {
    http_response_code(400);
    die(json_encode(["error" => "Missing required fields"]));
}

$title = $conn->real_escape_string($data['title']);
$description = $conn->real_escape_string($data['description']);
$recipe_url = $conn->real_escape_string($data['recipe_url']);

$query = "INSERT INTO datas (Title, Description, Recipe_URL) VALUES ('$title', '$description', '$recipe_url')";
$result = $conn->query($query);

if (!$result) {
    http_response_code(500);
    die(json_encode(["error" => "Query error: " . $conn->error]));
}

echo json_encode([
    "success" => true,
    "message" => "Food added successfully",
    "id" => $conn->insert_id
]);

$conn->close();

?>
