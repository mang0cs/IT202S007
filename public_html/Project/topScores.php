<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
$query = $_GET["score"];
$result = [];
if($query == "Top weekly") {
    $db = getDB();
    $stmt = $db->prepare("SELECT user_id,score,Users.username FROM Scores JOIN Users on Scores.user_id = Users.id WHERE Scores.user_id = Users.id  ORDER BY score DESC LIMIT 10");
    $r = $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$result) {
        $e = $stmt->errorInfo();
        flash($e[2]);
    }
}

elseif($query == "Top monthly"){
    $db = getDB();
    $stmt = $db->prepare("SELECT user_id,score,Users.username FROM Scores as Scores JOIN Users on Scores.user_id = Users.id where Scores.user_id = Users.id ORDER BY score DESC LIMIT 10");
    $r = $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$result) {
        $e = $stmt->errorInfo();
        flash($e[2]);
    }
}
elseif($query == "Top Lifetime"){
    $db = getDB();
    $stmt = $db->prepare("SELECT user_id,score,Users.username FROM Scores as Scores JOIN Users on Scores.user_id = Users.id where Scores.user_id = Users.id ORDER BY score DESC  LIMIT 10");
    $r = $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$result) {
        $e = $stmt->errorInfo();
        flash($e[2]);
    }
}
 else {
	flash("something is wrong");
}

?>

<?php if (count($result) > 0): ?>
    <?php foreach ($result as $r): ?>
        <div style= "text-align: center ">
            <div>Score: <?php safer_echo($r["score"]); ?> </div>
        </div>
        <div style= "text-align: center">
            <div>Owner: <?php safer_echo($r["username"]); ?></div>
        </div>
        <br>
    <?php endforeach; ?>
<?php else: ?>
    <p>No results</p>
<?php endif; ?>
<?php require(__DIR__ . "/partials/flash.php");
