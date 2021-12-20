<?php require_once(__DIR__ . "/../../../partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    //changing location to Home because doesn't make sense to go to login if you're signed in
    flash("You don't have permission to access this page");
    die(header("Location: home.php"));
}
$db = getDB();


   



$per_page = 10;
$query = "SELECT count(*) as total FROM Competitions WHERE expires > current_timestamp && paid_out = 0 ORDER BY expires ASC";
paginate($query, [], $per_page);
$stmt = $db->prepare("SELECT * FROM Competitions WHERE expires > current_timestamp ORDER BY expires ASC LIMIT :offset,:count");
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>
<div class="container-fluid">
        <h3>Competitions</h3>
        <div class="list-group">
            <?php if (isset($results) && count($results)): ?>
                <?php foreach ($results as $r): ?>
                    <div class="list-group-item" style="background-color: #D7C51B">
                        <div class="row">
                            
                            <div class="col">
                                Name: 
                                <?php safer_echo($r["name"]); ?>
                                <?php if ($r["user_id"] == get_user_id()): ?>
                                    (Created)
                                <?php endif; ?>
                            </div>
                            <div class="col">
                                Participants: 
                                <?php safer_echo($r["participants"]); ?>
                            </div>
				
                            <div class="col">
                                Required Score: 
                                <?php safer_echo($r["min_score"]); ?>
                            </div>
                            <div class="col">
                                Reward: 
                                <?php safer_echo($r["reward"]); ?>
                                <!--TODO show payout-->
                            </div>
                            <div class="col">
                                Expires: 
                                <?php safer_echo($r["expires"]); ?>
                            </div>
			    <div class="col">
				    <?php $compID=$r["id"]; ?>
				    <a href="check_scoreboard.php?id=<?php echo $compID;?>"><?php echo "Click here to see the Scoreboard";?></a>
                            </div>
                            <div class="col">
                            <a href="editedcompetitions.php?id=<?php echo $compID;?>"><?php echo "Edit";?></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="list-group-item">
                    No competitions available right now
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php include(__DIR__ . "/../../../partials/pagination.php");?>
</div>


<?php require(__DIR__ . "/../../../partials/flash.php");