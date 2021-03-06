<?php require_once(__DIR__ . "/../../partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php

flash("Your balance is " . getBalance() . "!");
if (isset($_POST["name"])) {

    $cost = (int)$_POST["reward"];
    if ($cost <= 0) {
        $cost = 0;
    }
    $cost++;
    //flash("cost is $cost");
    //TODO other validation
    $balance = getBalance();
    if ($cost > $balance) {
        flash("You can't afford to create this competition", "warning");
    }
    else {
        $db = getDB();
        $expires = new DateTime();
        $days = (int)$_POST["duration"];
        $expires->add(new DateInterval("P" . $days . "D"));
        $expires = $expires->format("Y-m-d H:i:s");
        $query = "INSERT INTO Competitions (name, duration, expires, cost, min_score, first_place_per, second_place_per, third_place_per, fee, user_id, reward) VALUES(:name, :duration, :expires, :cost, :min_score, :fp, :sp, :tp, :fee, :uid, :reward)";
        $stmt = $db->prepare($query);
        $params = [
            ":name" => $_POST["name"],
            ":duration" => $days,
            ":expires" => $expires,
            ":cost" => $cost,
            ":min_score" => $_POST["min_score"],
            ":uid" => get_user_id(),
            ":fee" => $_POST["fee"],
            ":reward" => $_POST["reward"]
        ];
        //flash(get_user_id());
        switch ((int)$_POST["split"]) {
            // case 0:
              //   break;  using default for this
            case 1:
                $params[":fp"] = .8;
                $params[":sp"] = .2;
                $params[":tp"] = 0;
                break;
            case 2:
                $params[":fp"] = .7;
                $params[":sp"] = .3;
                $params[":tp"] = 0;
                break;
            case 3:
                $params[":fp"] = .7;
                $params[":sp"] = .2;
                $params[":tp"] = .1;
                break;
            case 4:
                $params[":fp"] = .6;
                $params[":sp"] = .3;
                $params[":tp"] = .1;
                break;
	    case 5:
                $params[":fp"] = .5;
                $params[":sp"] = .3;
                $params[":tp"] = .2;
                break;
            default:
                $params[":fp"] = 1;
                $params[":sp"] = 0;
                $params[":tp"] = 0;
                break;
        }
        $r = $stmt->execute($params);
        if ($r) {
            flash("Successfully created competition", "success");
            
            		$user_id=get_user_id();
			$points_change = -($cost);
			$reason = "Created a new competition";
            
            $stmt = $db->prepare("INSERT INTO PointsHistory( user_id, points_change, reason) VALUES(:user_id,:points_change,:reason)");
			$params = array( ":user_id" => $user_id, ":points_change" => $points_change, ":reason" => $reason);
			$r = $stmt->execute($params);
            
            $stmt = $db->prepare("UPDATE Users set Score = (SELECT IFNULL(SUM(points_change), 0) FROM PointsHistory p where p.user_id = :id) WHERE id = :id");
            $params = array(":id" => get_user_id());
            $r = $stmt->execute($params);
            
                //Update the session variable for points/balance
			    $stmt = $db->prepare("SELECT Score from Users WHERE id = :id LIMIT 1");
			    $params = array(":id" => get_user_id());
			    $r = $stmt->execute($params);
			    if($r){
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$profilePoints = $result["Score"];
				$_SESSION["user"]["Score"] = $profilePoints;
				
				//flash("Your account has " . $profilePoints . " points.");
			    }
            
            
            die(header("Location: #"));
        }
        else {
            flash("There was a problem creating a competition: " . var_export($stmt->errorInfo(), true), "danger");
        }
    }
}
?>
    <div class="container-fluid">
        <h3>Create Competition</h3>
        <form method="POST">
            <div class="form-group">
                <label for="name">Name</label>
                <input id="name" name="name" required minlength="4" required maxlength="60" class="form-control"/>
            </div>
            <div class="form-group">
                <label for="d">Duration (in days)</label>
                <input id="d" name="duration" type="number" min="1" class="form-control"/>
            </div>
            <div class="form-group">
                <label for="s">Minimum Required Score</label>
                <input id="s" name="min_score" type="number" min="0" class="form-control"/>
            </div>
            <div class="form-group">
                <label for="r">Reward Split (First, Second, Third)</label>
                <select id="r" name="split" type="number" class="form-control">
                    <option value="0">100%</option>
                    <option value="1">80%/20%</option>
                    <option value="2">70%/30%</option>
                    <option value="3">70%/20%/10%</option>
                    <option value="4">60%/30%/10%</option>
                    <option value="5">50%/30%/20%</option>
                </select>
            </div>
            <div class="form-group">
                <label for="rw">Reward/Payout</label>
                <input id="rw" name="reward" type="number" min="0" class="form-control"/>
            </div>
            <div class="form-group">
                <label for="f">Entry Fee</label>
                <input id="f" name="fee" type="number" min="0" class="form-control"/>
            </div>
            <input type="submit" class="btn btn-success" value="Create (Cost: 1)"/>
        </form>
    </div>
<?php require(__DIR__ . "/../../partials/flash.php");