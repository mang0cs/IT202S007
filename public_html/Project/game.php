<?php require_once(__DIR__ . "/../../partials/nav.php"); ?>

<?php





if (!is_logged_in()) {
    
    flash("You are not logged in, your score won't be saved!");
    
}
	if (isset($_POST["sendscore"])) {
		if(!is_logged_in()) {
			flash("You are not logged in so the score was not saved");
			
		}
		else{
		$db = getDB();
        	if (isset($db)) {
			$user_id = get_user_id();
			$score = $_POST["count"];
			
			
			
			$stmt = $db->prepare("INSERT INTO Scores( user_id, score) VALUES(:user_id,:score)");
			
			$params = array( ":user_id" => $user_id, ":score" => $score);
			$r = $stmt->execute($params);
			
			
		
			$e = $stmt->errorInfo();
			if ($e[0] == "00000") {
				flash("Successfully recorded score");
				
			
			
			$points_change = (int)($score-5)/10;
			
			$reason = "Scored points playing the game";
			
			$stmt = $db->prepare("INSERT INTO PointsHistory(user_id, points_change, reason) VALUES(:user_id,:points_change,:reason)");
			$params = array( ":user_id" => $user_id, ":points_change" => $points_change, ":reason" => $reason);
			$r = $stmt->execute($params);
			
			
			
			$stmt = $db->prepare("UPDATE Users set Score = (SELECT IFNULL(SUM(points_change), 0) FROM PointsHistory p where p.user_id = :id) WHERE id = :id");
            		$params = array(":id" => get_user_id());
            		$r = $stmt->execute($params);
			
			
			
			
			
			
			
			
				
			    $stmt = $db->prepare("SELECT Score from Users WHERE id = :id LIMIT 1");
			    $params = array(":id" => get_user_id());
			    $r = $stmt->execute($params);
			    if($r){
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$profilePoints = $result["Score"];
				$_SESSION["user"]["Score"] = $profilePoints;
				
				
			    }
				
			}
			
		}
	}
	}
	
?>





<html>
<head>
<script>

var constTime=20;   
var constTime2=5;   


var gameOff=true;
var clickcount=0;
var time=constTime;


var cooldownTime=constTime2; 
var onCooldown=false;


document.getElementById("timeLeft").innerHTML = "You have " + time + " seconds to click the button. Timer starts when you click.";
document.getElementById("result").innerHTML = "Your current score is " + clickcount;


function clickCounter() {
	
    if(!onCooldown){
	clickcount++;
    }
	document.getElementById("result").innerHTML = "Your current score is " + clickcount;

  if(gameOff && !onCooldown){
    gameOff=false;
    startTimer();
  }

}
	
	
	


function submitScore() {
count.value=clickcount;
 clickcount = 0;
}

	
	
	

function startTimer(){
	
	clickcount=1;
	document.getElementById("result").innerHTML = "Your current score is " + clickcount;
	
    time = constTime;
	document.getElementById("timeLeft").innerHTML = "You have " + time + " seconds left to click the button!";
    timer = setInterval(function(){
       time--;
	    if(time==1) { 
		document.getElementById("timeLeft").innerHTML = "You have " + time + " second left to click the button!"; 
	    }else{
	document.getElementById("timeLeft").innerHTML = "You have " + time + " seconds left to click the button!"; 
	    }
       if(time<=0){
           
           gameOff=true;
           clearInterval(timer);
	       
	       onCooldown=true;
	       startCooldown();
	       
       }
    }, 1000);
}

	
	

function startCooldown(){
	cooldownTime=constTime2; 
	onCooldown=true;
	
	document.getElementById("timeLeft").innerHTML = "Game Over, wait " + cooldownTime + " seconds to start again. (Hit the \"Submit Score\" Button now to save your score)";
    timer = setInterval(function(){
       cooldownTime--;
       
	    if(cooldownTime==1) { 
		document.getElementById("timeLeft").innerHTML = "Game Over, wait " + cooldownTime + " second to start again. (Hit the \"Submit Score\" Button now to save your score)"; 
	    }else{
	document.getElementById("timeLeft").innerHTML = "Game Over, wait " + cooldownTime + " seconds to start again. (Hit the \"Submit Score\" Button now to save your score)"; 
	    }
       if(cooldownTime<=0){
           
           gameOff=true;
           clearInterval(timer);
	   onCooldown=false;
	    time=constTime;
	   document.getElementById("timeLeft").innerHTML = "You have " + time + " seconds to click the button. Timer starts when you click.";
	       
       }
    }, 1000);
}

</script>
</head>
	
<body>
	<form method="POST">
	<h3>Click to start, 20 seconds to set a high score</h3>
	<div id="timeLeft"></div>
	<button onclick="clickCounter()" id="clicker" type="button"  name="clicker" style="width: 100%; height: 200px;" >Click Me!</button>
	<div id="result"></div>
		<input type="hidden" id="count" name="count" value=0 />
	<input class="btn btn-primary" onclick="submitScore()" type="submit" name="sendscore" value="Submit Score" />
	<!----></form>
</body>
	

</html>

<?php require(__DIR__ . "/../../partials/flash.php");
	