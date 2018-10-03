<?php

require_once('database.php');

$id = $_REQUEST["id"];
$gid = $_REQUEST["gid"];

$gameData = [];
$userGameData = [];

/* Added for Redis caching Lab 3B */
$gameDataKey = "game.data.$gid";

if ($redis->exists($gameDataKey)) {
	$gameData = $redis->get($gameDataKey);
} else {
	$sql = 'SELECT * FROM game_data WHERE game_id = ' . $gid;
	$retval = $conn->query($sql);
	if (!$retval) {
		$m = "Could not retrieve game data: " . $conn->error;
        	error_log($m);
		die('{"status":"error", "message":"' . $m . '"}');
	}

	while ($row = $retval->fetch_assoc()) {
		$gameData[$row["k"]] = $row["v"];
	}
	$redis->set($gameDataKey, $gameData);	
}

$sessionCount = 0;
$userGameDataKey = "user.game.$id.$gid";

if ($redis->exists($userGameDataKey) {
	$userGameData = $redis->get($userGameDataKey);
	$sessionCount = intval($userGameData['sessions']);
	$sessionCount = $sessionCount + 1;
} else {
	$sql = "SELECT * FROM user_game_data WHERE user_id = $id AND game_id = $gid";
	$retval = $conn->query($sql);
	if (!$retval) {
		$m = "Could not retrieve user game data: " . $conn->error;
		error_log($m);
		die('{"status":"error", "message":"' . $m . '"}');
	}
	while ($row = $retval->fetch_assoc()) {
		$userGameData[$row["k"]] = $row["v"];
		if ($row["k"] == "sessions") {
			$sessionCount = intval($row["v"]) + 1;
		}	
	}
}

if ($sessionCount == 0) {
	$sql = "INSERT INTO user_game_data (user_id, game_id, k, v) VALUES ($id, $gid, 'sessions', '1')";
	$sessionCount = 1;
} else {
	$sql = "UPDATE user_game_data SET v='" . strval($sessionCount) . "' WHERE user_id = $id AND game_id = $gid AND k = 'sessions'";
}

$retval = $conn->query($sql);
if (!$retval) {
	$m = "Could not set user game session count: " . $conn->error;
        error_log($m);
	die('{"status":"error", "message":"' . $m . '"}');
}
$userGameData['sessions'] = strval($sessionCount);
$redis->set($userGameDataKey, $userGameData);
unset($gameData["wp"]);
echo '{"game_data":' . json_encode($gameData) . ', "user_game_data":' . json_encode($userGameData) . '}';

$conn->close();

?>

