<?php
require_once(__DIR__ . "/db.php");
$BASE_PATH = '/Project/'; 
function se($v, $k = null, $default = "", $isEcho = true)
{
    if (is_array($v) && isset($k) && isset($v[$k])) {
        $returnValue = $v[$k];
    } else if (is_object($v) && isset($k) && isset($v->$k)) {
        $returnValue = $v->$k;
    } else {
        $returnValue = $v;
        
        
        if (is_array($returnValue) || is_object($returnValue)) {
            $returnValue = $default;
        }
    }
    if (!isset($returnValue)) {
        $returnValue = $default;
    }
    if ($isEcho) {
        
        echo htmlspecialchars($returnValue, ENT_QUOTES);
    } else {
        
        return htmlspecialchars($returnValue, ENT_QUOTES);
    }
}
function sanitize_email($email = "")
{
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}
function is_valid_email($email = "")
{
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
}
function is_logged_in($redirect = false, $destination = "login.php")
{
    $isLoggedIn = isset($_SESSION["user"]);
    if ($redirect && !$isLoggedIn) {
        flash("You must be logged in to view this page", "warning");
        die(header("Location: $destination"));
    }
    return $isLoggedIn; 
}
function has_role($role)
{
    if (is_logged_in() && isset($_SESSION["user"]["roles"])) {
        foreach ($_SESSION["user"]["roles"] as $r) {
            if ($r["name"] === $role) {
                return true;
            }
        }
    }
    return false;
}
function getBalance() {
    if (is_logged_in() && isset($_SESSION["user"]["Score"])) {
        return $_SESSION["user"]["Score"];
    }
    return 0;
}
function get_username() {
    if (is_logged_in() && isset($_SESSION["user"]["username"])) {
        return $_SESSION["user"]["username"];
    }
    return "";
}
function get_user_email()
{
    if (is_logged_in()) { 
        return se($_SESSION["user"], "email", "", false);
    }
    return "";
}
function get_user_id()
{
    if (is_logged_in()) { 
        return se($_SESSION["user"], "id", false, false);
    }
    return false;
}
function flash($msg = "", $color = "info")
{
    $message = ["text" => $msg, "color" => $color];
    if (isset($_SESSION['flash'])) {
        array_push($_SESSION['flash'], $message);
    } else {
        $_SESSION['flash'] = array();
        array_push($_SESSION['flash'], $message);
    }
}
function getMessages()
{
    if (isset($_SESSION['flash'])) {
        $flashes = $_SESSION['flash'];
        $_SESSION['flash'] = array();
        return $flashes;
    }
    return array();
}
function reset_session()
{
    session_unset();
    session_destroy();
}
function users_check_duplicate($errorInfo)
{
    if ($errorInfo[1] === 1062) {
        
        preg_match("/Users.(\w+)/", $errorInfo[2], $matches);
        if (isset($matches[1])) {
            flash("The chosen " . $matches[1] . " is not available.", "warning");
        } else {
            
            flash("<pre>" . var_export($errorInfo, true) . "</pre>");
        }
    } else {
        
        flash("<pre>" . var_export($errorInfo, true) . "</pre>");
    }
}
function safer_echo($var) {
    if (!isset($var)) {
        echo "";
        return;
    }
    echo htmlspecialchars($var, ENT_QUOTES, "UTF-8");
}

function get_url($dest)
{
    global $BASE_PATH;
    if (str_starts_with($dest, "/")) {
        
        return $dest;
    }
    
    return $BASE_PATH . $dest;
}
function getScore(){
    if(is_logged_in() && isset($_SESSION["user"]["Score"])){
        return $_SESSION["user"]["Score"];
    }
    return 0;
}
function paginate($query, $params = [], $per_page = 10) {
    global $page;
    if (isset($_GET["page"])) {
        try {
            $page = (int)$_GET["page"];
        }
        catch (Exception $e) {
            $page = 1;
        }
    }
    else {
        $page = 1;
    }
    $db = getDB();
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = 0;
    if ($result) {
        $total = (int)$result["total"];
        
    }
    global $total_pages;
    $total_pages = ceil($total / $per_page);
    global $offset;
    $offset = ($page - 1) * $per_page;
function get_status() {
    if (is_logged_in() && isset($_SESSION["user"]["status"])) {
        return $_SESSION["user"]["status"];
    }
    return "";
}
}
function get10week(){
    $arr = [];
    $db = getDB();
    $stmt = $db->prepare("SELECT score from Scores where created >= :timeCon order by score desc limit 10");
    
    $timeType="Week";
    $testtime=strtotime("-1 " . $timeType); 
    $params = array(":timeCon" => date("Y-m-d h:i:s", $testtime));
    $results = $stmt->execute($params);
    $results = $stmt->fetchAll();
        
    $stmt2 = $db->prepare("SELECT Users.username FROM Users JOIN Scores on Users.id = Scores.user_id where Scores.created >= :timeCon order by Scores.score desc limit 10");   
    $params2 = array(":timeCon" => date("Y-m-d h:i:s", $testtime));
    $results2 = $stmt2->execute($params2);
    $results2 = $stmt2->fetchAll();
            
    $stmt3 = $db->prepare("SELECT Users.score FROM Users JOIN Scores on Users.id = Scores.user_id where Scores.created >= :timeCon order by Scores.score desc limit 10");   
    $params3 = array(":timeCon" => date("Y-m-d h:i:s", $testtime));
    $results3 = $stmt3->execute($params2);
    $results3 = $stmt3->fetchAll();
    
    $stmt4 = $db->prepare("SELECT Users.id FROM Users JOIN Scores on Users.id = Scores.user_id where Scores.created >= :timeCon order by Scores.score desc limit 10");   
    $params4 = array(":timeCon" => date("Y-m-d h:i:s", $testtime));
    $results4 = $stmt4->execute($params4);
    $results4 = $stmt4->fetchAll();
        
    
    $hasScores=true;
    if (count($results)==0) {
        $hasScores=false;
        echo "There have been no scores set in the past " . $timeType . "</br>";
    }
    if($hasScores) {
            echo "The Top " . count($results) . " scores of the last " . $timeType . "</br>";
        $i=10-count($results);
        $a=1;
        $w=0;
        do {
            
            
            $numlength = strlen(implode($results[$a-1]))/2; 
            $modifier = 10**$numlength;
            $finalNum = implode($results[$a-1]) % $modifier;
            
            $numlength = strlen(implode($results2[$a-1]))/2; 
            $modifier = 10**$numlength;
            $user = implode($results2[$a-1]);
    
    
            $numlength = strlen(implode($results3[$a-1]))/2; 
            $modifier = 10**$numlength;
            $points = implode($results3[$a-1]) % $modifier;
            
            $numlength = strlen(implode($results4[$a-1]))/2; 
            $modifier = 10**$numlength;
            $id = implode($results4[$a-1]) % $modifier;
            $arr[$w]=$a;
            $w++;
            $arr[$w]=$finalNum;
            $w++;
            $arr[$w]=$user;
            $w++;
            $arr[$w]=$points;
            $w++;
            
            
            if(get_username() == $user){
               
                echo "The #" . $a . " top score is " . round($finalNum) . " scored by user <a href='profile.php?id=$id'>$user</a> who has " . $points . " profile points" . "</br>";
            }
            else{
                $id = implode($results4[$a-1]);            
                echo "The #" . $a . " top score is " . round($finalNum) . " scored by user <a href='other_profile.php?id=$id'>$user</a> who has " . $points . " profile points" . "</br>";
                
            }
          $a++;
          $i++;
        }
        while($i<10);
    }
    echo "</br>";
    echo "</br>";
    echo "</br>";
            foreach($results as $r):
            endforeach;
        return $arr;
    }
    
    
    
    
    
    
    
    
    
    function get10month(){
    $arr = [];
    $db = getDB();
    $stmt = $db->prepare("SELECT score from Scores where created >= :timeCon order by score desc limit 10");
    
    $timeType="Month";
    $testtime=strtotime("-1 " . $timeType); 
    $params = array(":timeCon" => date("Y-m-d h:i:s", $testtime));
    $results = $stmt->execute($params);
    $results = $stmt->fetchAll();
        
    $stmt2 = $db->prepare("SELECT Users.username FROM Users JOIN Scores on Users.id = Scores.user_id where Scores.created >= :timeCon order by Scores.score desc limit 10");   
    $params2 = array(":timeCon" => date("Y-m-d h:i:s", $testtime));
    $results2 = $stmt2->execute($params2);
    $results2 = $stmt2->fetchAll();
            
    $stmt3 = $db->prepare("SELECT Users.score FROM Users JOIN Scores on Users.id = Scores.user_id where Scores.created >= :timeCon order by Scores.score desc limit 10");   
    $params3 = array(":timeCon" => date("Y-m-d h:i:s", $testtime));
    $results3 = $stmt3->execute($params2);
    $results3 = $stmt3->fetchAll();
          
    $stmt4 = $db->prepare("SELECT Users.id FROM Users JOIN Scores on Users.id = Scores.user_id where Scores.created >= :timeCon order by Scores.score desc limit 10");   
    $params4 = array(":timeCon" => date("Y-m-d h:i:s", $testtime));
    $results4 = $stmt4->execute($params4);
    $results4 = $stmt4->fetchAll();
        
        
    $hasScores=true;
    if (count($results)==0) {
        $hasScores=false;
        echo "There have been no scores set in the past " . $timeType . "</br>";
    }
    if($hasScores) {
            echo "The Top " . count($results) . " scores of the last " . $timeType . "</br>";
        $i=10-count($results);
        $a=1;
        $w=0;
        do {
            
            
            $numlength = strlen(implode($results[$a-1]))/2; 
            $modifier = 10**$numlength;
            $finalNum = implode($results[$a-1]) % $modifier;
            
            $numlength = strlen(implode($results2[$a-1]))/2; 
            $modifier = 10**$numlength;
            $user = implode($results2[$a-1]);
            
            $numlength = strlen(implode($results3[$a-1]))/2; 
            $modifier = 10**$numlength;
            $points = implode($results3[$a-1]) % $modifier;
            
            $numlength = strlen(implode($results4[$a-1]))/2; 
            $modifier = 10**$numlength;
            $id = implode($results4[$a-1]) % $modifier;
            $arr[$w]=$a;
            $w++;
            $arr[$w]=$finalNum;
            $w++;
            $arr[$w]=$user;
            $w++;
            $arr[$w]=$points;
            $w++;
            
            if(get_username() == $user){
               
                echo "The #" . $a . " top score is " . round($finalNum) . " scored by user <a href='profile.php?id=$id'>$user</a> who has " . $points . " profile points" . "</br>";
            }else{
                $id = implode($results4[$a-1]);
                
                
                
                
                
                
                echo "The #" . $a . " top score is " . round($finalNum) . " scored by user <a href='other_profile.php?id=$id'>$user</a> who has " . $points . " profile points" . "</br>";
            }
          $a++;
          $i++;
        }
        while($i<10);
    }
    echo "</br>";
        echo "</br>";
        echo "</br>";
            foreach($results as $r):
            endforeach;
    }
    function get10lifetime(){
        $arr = [];
        $db = getDB();
        $stmt = $db->prepare("SELECT score from Scores where created >= :timeCon order by score desc limit 10");
        
        $timeType="Lifetime";
        $testtime=strtotime("-1 Year"); 
        $params = array(":timeCon" => date("Y-m-d h:i:s", $testtime));
        $results = $stmt->execute($params);
        $results = $stmt->fetchAll();
            
        $stmt2 = $db->prepare("SELECT Users.username FROM Users JOIN Scores on Users.id = Scores.user_id where Scores.created >= :timeCon order by Scores.score desc limit 10");   
        $params2 = array(":timeCon" => date("Y-m-d h:i:s", $testtime));
        $results2 = $stmt2->execute($params2);
        $results2 = $stmt2->fetchAll();
                
        $stmt3 = $db->prepare("SELECT Users.score FROM Users JOIN Scores on Users.id = Scores.user_id where Scores.created >= :timeCon order by Scores.score desc limit 10");   
        $params3 = array(":timeCon" => date("Y-m-d h:i:s", $testtime));
        $results3 = $stmt3->execute($params2);
        $results3 = $stmt3->fetchAll();
            
        $stmt4 = $db->prepare("SELECT Users.id FROM Users JOIN Scores on Users.id = Scores.user_id where Scores.created >= :timeCon order by Scores.score desc limit 10");   
        $params4 = array(":timeCon" => date("Y-m-d h:i:s", $testtime));
        $results4 = $stmt4->execute($params4);
        $results4 = $stmt4->fetchAll();
            
            
        $hasScores=true;
        if (count($results)==0) {
            $hasScores=false;
            echo "There have been no scores set in the past " . $timeType . "</br>";
        }
        if($hasScores) {
                echo "The Top " . count($results) . " scores of the games whole " . $timeType . "</br>";
            $i=10-count($results);
            $a=1;
            $w=0;
            do {
                
                $numlength = strlen(implode($results[$a-1]))/2; 
                $modifier = 10**$numlength;
                $finalNum = implode($results[$a-1]) % $modifier;
                
                $numlength = strlen(implode($results2[$a-1]))/2; 
                $modifier = 10**$numlength;
                $user = implode($results2[$a-1]);
                
                $numlength = strlen(implode($results3[$a-1]))/2; 
                $modifier = 10**$numlength;
                $points = implode($results3[$a-1]) % $modifier;
                
                $numlength = strlen(implode($results4[$a-1]))/2; 
                $modifier = 10**$numlength;
                $id = implode($results4[$a-1]) % $modifier;
                $arr[$w]=$a;
                $w++;
                $arr[$w]=$finalNum;
                $w++;
                $arr[$w]=$user;
                $w++;
                $arr[$w]=$points;
                $w++;
                
                if(get_username() == $user){
                   
                    echo "The #" . $a . " top score is " . round($finalNum) . " scored by user <a href='profile.php?id=$id'>$user</a> who has " . $points . " profile points" . "</br>";
                    
                }else{
                    $id = implode($results4[$a-1]);
                    echo "The #" . $a . " top score is " . round($finalNum) . " scored by user <a href='other_profile.php?id=$id'>$user</a> who has " . $points . " profile points" . "</br>";
                }
              $a++;
              $i++;
            }
            while($i<10);
        }
        echo "</br>";
        echo "</br>";
        echo "</br>";
        foreach($results as $r):
        endforeach;
        }
    
    
    
    
    
    
    
    
    
    ?>