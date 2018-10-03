<?php

require_once('database.php');

$id = $_REQUEST["id"];
$gid = $_REQUEST["gid"];
$bet = $_REQUEST["bet"];
//$wp = $_REQUEST["wp"];


/* Added for Redis */
$gameDataKey = "game.data.$gid";
if ($redis->exists($gameDataKey)) {
	$gameData = $redis->get($gameDataKey);
} else {
	$gameData = [];
	$sql = "SELECT k, v FROM game_data WHERE game_id = $gid";
	$retval = $conn->query($sql);
	if (!$retval) {
		$m = "Could not retrieve stats from game_data: " . $conn->error;
		error_log($m);
		die('{"status":"error", "message":"' . $m . '"}');
	}
	$wp = 0;

	while ($row = $retval->fetch_assoc()) {
		$gameData[$row["k"]] = $row["v"];
	}
}
$wp = floatval($gameData['wp']);

$sql = 'INSERT INTO user_game_data (user_id, game_id, k, v) VALUES ';
$sql = $sql . "($id, $gid, 'last_play', now()) ";
$sql = $sql . "ON DUPLICATE KEY UPDATE v = now()";
$retval = $conn->query( $sql );
if(! $retval ) {	
	$m = "Could not update last_play: " . $conn->error;
        error_log($m);
	die('{"status":"error", "message":"' . $m . '"}');
}

// Get the win percent for this game.
$userGameDataKey = "user.game.$id.$gid";
if ($redis->exists($userGameDataKey) {
	$userGameData = $redis->get($userGameDataKey);
} else {
	$sql = "SELECT k, v FROM user_game_data WHERE user_id = $id AND game_id = $gid";
	$retval = $conn->query( $sql);
	if(! $retval ) {	
		$m = "Could not retrieve stats from user_game_data: " . $conn->error;
        	error_log($m);
		die('{"status":"error", "message":"' . $m . '"}');
	}
	while ($row = $retval->fetch_assoc()) {
		$userGameData[$row['k']] = $row["v"]);
	}
}

$win_count = 0;
$lose_count = 0;
$win_total = 0;
$lose_total = 0;
$win_count = intval($userGameData["win_count"]);
$lose_count = intval($userGameData["lose_count"]);
$win_total = intval($userGameData["win_total"]);
$lose_total = intval($userGameData["lose_total"]);

$delta_coins = (rand() / getrandmax() < $wp / 2) ? $bet : -$bet;
$delta_xp = intval($bet);
$delta_level = (rand() / getrandmax() < 0.005) ? 1 : 0; // Level up 1 in 200 plays on average

if ($delta_coins > 0) {
	$count_key = "win_count";
	$total_key = "win_total";
	$count_value = $win_count + 1;
	$total_value = $win_total + $delta_coins;
} else {
	$count_key = "lose_count";
	$total_key = "lose_total";
	$count_value = $lose_count + 1;
	$total_value = $lose_total - $delta_coins;
}

$sql = "INSERT INTO user_game_data (user_id, game_id, k, v)";
$sql .= "VALUES ($id, $gid, '$count_key', '$count_value')";
$sql .= "ON DUPLICATE KEY UPDATE v = '$count_value'";
$retval = $conn->query(sql);
if (!retval) {
	$m = "Could not update win/lose count in user_game_data: " . $conn->error;
	error_log($m);
	die('{"status":"error", "message":"' . $m . '"}');
}

$win_total = $win_total + $delta_coins;
$sql = "INSERT INTO user_game_data (user_id, game_id, k, v)";
$sql .= "VALUES ($id, $gid, '$total_key', '$total_value')";
$sql .= "ON DUPLICATE KEY VALUE v = '$total_value'";
$retval = $conn->query(sql);
if (!retval) {
	$m = "Could not update win/lose total in user_game_data: " . $conn->error;
	error_log($m);
	die('{"status":"error", "message":"' . $m . '"}');
}

$result = [];

$result["status"] = "success";
$result["delta_coins"] = $delta_coins;
$result["delta_xp"] = $delta_xp;
$result["delta_level"] = $delta_level;


echo json_encode($result);
$conn->close();
?>

