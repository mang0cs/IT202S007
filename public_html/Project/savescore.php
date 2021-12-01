<?php
require_once(__DIR__ . "/lib/helpers.php");

if (!is_logged_in()) {
    die(header(':', true, 403));
}

$user = get_user_id();
$score = $_POST["myscore"];
$db = getDB();
$stmt = $db -> prepare("INSERT INTO Scores (score, user_id) VALUES(:score, :user)");
$r = $stmt -> execute([
    ":score" => $score,
    ":user" => $user
]);

if ($r) {
    $response = ["status" => 200, "score" => $score];
    echo json_encode($response);
    die();
}
else {
    $e = $stmt->errorInfo();
    $response = ["status" => 400, "error" => $e];
    echo json_encode($response);
    die();
}

?>


