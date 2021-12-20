<?php require_once(__DIR__ . "/../../partials/nav.php"); ?>

<?php
$db = getDB();

$per_page = 10;
$theID = get_user_id();
//$query = "SELECT count(*) as total FROM Competitions WHERE expires > current_timestamp ORDER BY expires ASC";
$query = "SELECT count(*) as total FROM Scores WHERE user_id = $theID ORDER BY created DESC";
paginate($query, [], $per_page);
$id = get_user_id();
$result = [];
if(isset($id)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT user_id,Scores.score,Users.username FROM Scores as Scores JOIN Users on Scores.user_id = Users.id where Scores.user_id = :id LIMIT 10");
    $r = $stmt->execute([":id" => $id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$result) {
        $e = $stmt->errorInfo();
        flash($e[2]);
    }
}
?>
<?php if (count($result) > 0): ?>
    <?php foreach ($result as $r): ?>
        <div style= 'text-align: center '>
            <div>Score: <?php safer_echo($r["score"]); ?> </div>
        </div>
        <div style= 'text-align: center' >
            <div>Owner: <?php safer_echo($r["username"]); ?></div>
        </div>
        <br>
    <?php endforeach; ?>
<?php else: ?>
    <p>No results</p>
<?php endif; ?>
<?php include(__DIR__ . "/../../partials/pagination.php");?>
<?php require(__DIR__ . "/../../partials/flash.php");