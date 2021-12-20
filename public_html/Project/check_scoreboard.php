<?php require_once(__DIR__ . "/../../partials/nav.php"); ?>


<?php

if(isset($_GET["id"])){
$id = $_GET["id"];
}
flash("comp id is " . $id);
?>



<div class="container-fluid">
        <h3>The Top 10 Scores</h3>
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

<?php require(__DIR__ . "/../../partials/flash.php");