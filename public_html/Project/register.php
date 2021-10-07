<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<form onsubmit="return validate(this)" method="POST">
    <div>
        <label for="email">Email</label>
        <input type="email" name="email" required />
    </div>
    <div>
        <label for="username">Username</label>
        <input type ="text" name = "username" required maxlength = "30" />
    </div>
    <div>
        <label for="pw">Password</label>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div>
    <div>
        <label for="confirm">Confirm</label>
        <input type="password" name="confirm" required minlength="8" />
    </div>
    <input type="submit" value="Register" />
</form>
<script>
    function validate(form) {
        //TODO 1: implement JavaScript validation
        //ensure it returns false for an error and true for success

        return true;
    }
</script>
<?php
//TODO 2: add PHP Code
if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm"])) {
    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);
    $confirm = se($_POST, "confirm", "", false);
    $username = se($_POST, "username", "", false);
    //TODO 3


    $errors = [];
    if (empty($email)) {
        flash("Email must not be empty");
    }
    $email = sanitize_email($email);
    if (!is_valid_email($email)) {
        flash("Email is invalid");
    }
    if (empty($password)) {
        flash("Password must not be empty");
    }
    if (empty($confirm)) {
        flash("Confirm Password must not be empty");
    }
    if (strlen($password) < 8) {
        flash("Password too short");
    }
    if(!preg_match('/[^a-z_\-0-9]/i', $username)){
        flash("Username can only contain alphanumeric characters and _ or -");
    }
    if (strlen($password) > 0 && $password !== $confirm) {
        flash("Passwords must match");
    }
    if (count($errors) > 0) {
        flash("<pre>" . var_export($errors, true) . "</pre>");
    } else {
        flash("Welcome, $email");
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Users (email, password, username) VALUES(:email, :password, :username)");
        try {
            $stmt->execute([":email" => $email, ":password" => $hash, ":username" => $username]);
            flash("You've registered, yay...");
        } catch (Exception $e) {
            flash("There was a problem registering");
            flash("<pre>" . var_export($e, true) . "</pre>");
        }
    }
}
require(__DIR__. "/../../partials/flash.php");
?>