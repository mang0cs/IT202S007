<?php require_once(__DIR__ . "/../../partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>

<?php
$page = 1;
$per_page = 10;
$id = get_user_id();
$db = getDB();
$stmt = $db -> prepare("SELECT count(*) as total c.* FROM Competitions c JOIN UserCompetitions uc on competition_id = c.id  WHERE uc.user_id = :id ORDER BY expires ASC");
$r = $stmt->execute([":id" => $id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total = 0;
if ($result) {
    $total = (int)$result["total"];
}

$total_pages = ceil($total/$per_page);
$offset = ($page-1) * $per_page;

$stmt = $db-> prepare("SELECT c.* FROM Competitions c JOIN UserCompetitions uc on competition_id = c.id  WHERE uc.user_id = :id LIMIT :offset, :count");
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
$stmt->bindValue(":id", $id);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>My Competition History</h2>
<?php if (isset($results) && count($results)): ?>
<?php foreach ($results as $r): ?>
<div>name: <?php safer_echo($r["name"]); ?></div>
<div>Participants: <?php safer_echo($r["participants"]); ?></div>
<div>Required Score: <?php safer_echo($r["min_score"]); ?></div>
<div>Reward: <?php safer_echo($r["reward"]); ?></div>
<div>1 st place: <?php safer_echo($r["first_place_per"]); ?></div>
<div>2 nd place: <?php safer_echo($r["second_place_per"]); ?></div>
<div>3 rd place: <?php safer_echo($r["third_place_per"]); ?></div>
<div>expires: <?php safer_echo($r["expires"]); ?></div>
<div><br></div>
<?php endforeach; ?>
<?php else: ?>
<p>No results</p>
<?php endif; ?>
<nav>
<ul>
    <li>
        <a class="page-link" href="?page=<?php echo $page-1;?>" tabindex="-1">Previous</a>
    </li>
    <?php for($i = 0; $i < $total_pages; $i++):?>
        <li>
            <a class="page-link" href="?page=<?php echo ($i+1);?>"><?php echo ($i+1);?></a>
        </li>
    <?php endfor; ?>
    <li>
        <a class="page-link" href="?page=<?php echo $page+1;?>">Next</a>
    </li>
</ul>
</nav>
<?php require(__DIR__ . "/../../partials/flash.php");
