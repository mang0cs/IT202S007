<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
?>
<?php
$db = getDB();
if (isset($_POST["makePub"])) {
    $stmt = $db->prepare("UPDATE Users set status = :status where id = :id");
        $r = $stmt->execute([":status" => "public", ":id" => get_user_id()]);
        if ($r) {
            flash("Your profile is public");
        }
        else {
            flash("Error updating profile");
        }
}

if (isset($_POST["makePriv"])) {
    $stmt = $db->prepare("UPDATE Users set status = :status where id = :id");
        $r = $stmt->execute([":status" => "private", ":id" => get_user_id()]);
        if ($r) {
            flash("Your profile is private");
        }
        else {
            flash("Error updating profile");
        }
}
if(isset($_GET["id"])){
    $id = $_GET["id"];
    }
    else{
    $id= get_user_id();
    }
if (isset($_POST["save"])) {
    $email = se($_POST, "email", null, false);
    $username = se($_POST, "username", null, false);

    $params = [":email" => $email, ":username" => $username, ":id" => get_user_id()];
    $db = getDB();
    $stmt = $db->prepare("UPDATE Users set email = :email, username = :username where id = :id");
    try {
        $stmt->execute($params);
    } catch (Exception $e) {
        if ($e->errorInfo[1] === 1062) {
            //https://www.php.net/manual/en/function.preg-match.php
            preg_match("/Users.(\w+)/", $e->errorInfo[2], $matches);
            if (isset($matches[1])) {
                flash("The chosen " . $matches[1] . " is not available.", "warning");
            } else {
                //TODO come up with a nice error message
                echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
            }
        } else {
            //TODO come up with a nice error message
            echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
        }
    }
    //select fresh data from table
    $stmt = $db->prepare("SELECT id, email, IFNULL(username, email) as `username` from Users where id = :id LIMIT 1");
    try {
        $stmt->execute([":id" => get_user_id()]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            //$_SESSION["user"] = $user;
            $_SESSION["user"]["email"] = $user["email"];
            $_SESSION["user"]["username"] = $user["username"];
        } else {
            flash("User doesn't exist", "danger");
        }
    } catch (Exception $e) {
        flash("An unexpected error occurred, please try again", "danger");
        //echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
    }


    //check/update password
    $current_password = se($_POST, "currentPassword", null, false);
    $new_password = se($_POST, "newPassword", null, false);
    $confirm_password = se($_POST, "confirmPassword", null, false);
    if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
        if ($new_password === $confirm_password) {
            //TODO validate current
            $stmt = $db->prepare("SELECT password from Users where id = :id");
            try {
                $stmt->execute([":id" => get_user_id()]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if (isset($result["password"])) {
                    if (password_verify($current_password, $result["password"])) {
                        $query = "UPDATE Users set password = :password where id = :id";
                        $stmt = $db->prepare($query);
                        $stmt->execute([
                            ":id" => get_user_id(),
                            ":password" => password_hash($new_password, PASSWORD_BCRYPT)
                        ]);

                        flash("Password reset", "success");
                    } else {
                        flash("Current password is invalid", "warning");
                    }
                }
            } catch (Exception $e) {
                echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
            }
        } else {
            flash("New passwords don't match", "warning");
        }
    }
}

$stmt = $db->prepare("SELECT * from Scores where user_id = :id order by id desc limit 10");
$params = array(":id" => get_user_id());
$results = $stmt->execute($params);
$results = $stmt->fetchAll();
?>

<?php
$email = get_user_email();
$username = get_username();
?>
<form method="POST" onsubmit="return validate(this);">
    <div class="mb-3">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?php se($email); ?>" />
    </div>
    <div class="mb-3">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" value="<?php se($username); ?>" />
    </div>
    <!-- DO NOT PRELOAD PASSWORD -->
    <div>Password Reset</div>
    <div class="mb-3">
        <label for="cp">Current Password</label>
        <input type="password" name="currentPassword" id="cp" />
    </div>
    <div class="mb-3">
        <label for="np">New Password</label>
        <input type="password" name="newPassword" id="np" />
    </div>
    <div class="mb-3">
        <label for="conp">Confirm Password</label>
        <input type="password" name="confirmPassword" id="conp" />
    </div>
    <input type="submit" value="Update Profile" name="save" />
</form>
<form action ="scorehistory.php" method ="POST">
        <input type = "submit" value = "Previous scores">
    </form>

<script>
    function validate(form) {
        let pw = form.newPassword.value;
        let con = form.confirmPassword.value;
        let isValid = true;
        //TODO add other client side validation....

        //example of using flash via javascript
        //find the flash container, create a new element, appendChild
        if (pw !== con) {
            //find the container
            /*let flash = document.getElementById("flash");
            //create a div (or whatever wrapper we want)
            let outerDiv = document.createElement("div");
            outerDiv.className = "row justify-content-center";
            let innerDiv = document.createElement("div");

            //apply the CSS (these are bootstrap classes which we'll learn later)
            innerDiv.className = "alert alert-warning";
            //set the content
            innerDiv.innerText = "Password and Confirm password must match";

            outerDiv.appendChild(innerDiv);
            //add the element to the DOM (if we don't it merely exists in memory)
            flash.appendChild(outerDiv);*/
            flash("Password and Confirm password must match", "warning");
            isValid = false;
        }
        return isValid;
    }
    
</script>

<br>
<form action="topScores.php" method ="get">
    <label for="score">Choose the top score: </label>
    <input list ="scores" name = "score" id ="score">
    <datalist id = "scores">
        <option value= "Top weekly">
        <option value="Top monthly">
        <option value ="Top Lifetime">
    </datalist>
    <input type = "submit">
    </form> 
    <br>
    <form action ="create_competition.php" method ="POST">
        <input type = "submit" value = "Create Competition">
    </form>

    <form action ="mycompetition.php" method ="POST">
        <input type = "submit" value = "My Competition">
    </form>

    <form action ="Competition.php" method ="POST">
        <input type = "submit" value = "Active Competitions">
    </form>

    <form action ="competitionhistory.php" method ="POST">
        <input type = "submit" value = "Competition History">
    </form>
        <form method="POST">
            <table style="width:100%">
            <div id="currStatus"></div>
            <tr>
        <td>  <input class="btn btn-primary" type="submit" name="makePub" value="Set your profile to Public"/>  </td>
        <td>  <input class="btn btn-primary" type="submit" name="makePriv" value="Set your profile to Private"/>  </td>
            </tr>
            </table>
        </form>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>