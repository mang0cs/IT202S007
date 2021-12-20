<?php require_once(__DIR__ . "/../../partials/nav.php"); ?>
<?php






if(isset($_GET["id"])){
$id = $_GET["id"];

}
else{
$id= get_user_id();

}



$db = getDB();

$stmt = $db->prepare("SELECT status from Users WHERE id = :id LIMIT 1");
    $params = array(":id" => $id);
    $r = $stmt->execute($params);
    if($r){
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
	if($result["status"]=="private"){
	flash("You cannot see the profiles of private accounts.");
	die(header("Location: home.php"));
		
		
	}
        

    }



    $stmt = $db->prepare("SELECT Score from Users WHERE id = :id LIMIT 1");
    $params = array(":id" => $id);
    $r = $stmt->execute($params);
    if($r){
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $profilePoints = $result["Score"];
flash("This account has " . $profilePoints . " points.");
    }



?>



<?php



$stmt = $db->prepare("SELECT * from Scores where user_id = :id order by id desc limit 10");
$params = array(":id" => $id);
$results = $stmt->execute($params);
$results = $stmt->fetchAll();




?>


<html>
    
    <script>
        
        
    </script>
    
    
    

</html>

<div class="container-fluid">
        <h3>The Last 10 Scores of this account</h3>
        <div class="list-group">
            <?php if (isset($results) && count($results)): ?>
                <?php foreach ($results as $r): ?>
                    <div class="list-group-item" style="background-color: #25E418">
                        <div class="row">
				
                            <div class="col">
                                They scored: 
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

<?php require(__DIR__ . "/../../partials/flash.php");?>