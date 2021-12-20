<?php require_once(__DIR__ . "/../../../partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>


<?php 

if(isset($_POST["name"])){

    $name  = $_POST["name"];
    $duration = $_POST["duration"];
    $min_score = $_POST["min_score"];
    $split = $_POST["split"];
    $reward = $_POST["reward"];
    $fee = $_POST["fee"];
    $db = getDB();
    if(isset($name)){
        $stmt = $db -> prepare("UPDATE Competitions set name = :name, duration = :duration, min_score = :min_score, split = :split, reward = :reward, fee = :fee where id = :id");
        $stmt = $db->prepare($query);
        $params = [
            ":id" => $compID,
            ":name" => $name,
            ":duration" => $duration,
            ":min_score" => $min_score,
            ":fee" => $fee,
            ":reward" => $reward
        ];
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

        if($r)
        {
            flash("updated sucessfully with id: " . $id);

        }
        else {
            $e = $stmt -> errorInfo();
            flash("Error updating: " . var_export($e, true));
        }
    }
    else{
        flash("ID isn't set, we need an ID in order to update");
    }
 }
 ?>
 <?php

?>

<div class="container-fluid">
        <h3>Edit Competition</h3>
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
            <input type="submit" class="btn btn-success" value="Update"/>
        </form>
    </div>
<?php require(__DIR__ . "/../../../partials/flash.php");