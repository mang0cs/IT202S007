<?php require_once(__DIR__ . "/../../partials/nav.php"); ?>

<?php
if (!is_logged_in()) {
    
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>


<?php

$db = getDB();

$per_page = 10;
$theID = get_user_id();

$query = "SELECT count(*) as total FROM Scores WHERE user_id = $theID ORDER BY created DESC";
paginate($query, [], $per_page);



$stmt = $db->prepare("SELECT * FROM Scores WHERE user_id = :id ORDER BY created DESC LIMIT :offset,:count");
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
$stmt->bindValue(":id", get_user_id(), PDO::PARAM_INT);
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container-fluid">
        <h3>Your Score History</h3>
        <div class="list-group">
            <?php if (isset($results) && count($results)): ?>
                <?php foreach ($results as $r): ?>
                    <div class="list-group-item" style="background-color: #25E418">
                        <div class="row">
				
                            <div class="col">
                                You scored: 
                                <?php safer_echo($r["score"]); ?>
                            </div>
                            <div class="col">
                                Scored on: 
                                <?php safer_echo($r["created"]); ?>
                            </div>
			    <div class="col">
                                <form method="POST">
				</form>
                            </div>
                             
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="list-group-item">
                    No scores to show, sorry.
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php include(__DIR__ . "/../../partials/pagination.php");?>

<?php require(__DIR__ . "/../../partials/flash.php");