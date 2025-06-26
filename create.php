<?php

// var_dump($_POST);
// exit();

$date = $_POST["date"] ?? ""; // 値がある場合代入、なければ""にする。
$team = $_POST["team"] ?? "";
$opponent = $_POST["opponent"] ?? "";
$result = $_POST["result"] ?? "";
$score = $_POST["score"] ?? "";
$lost = $_POST["lost"] ?? "";

if($date === "" || $team === "" || $opponent === "" || $result === "" || $score === "" || $lost === "") {
  exit("入力漏れがあります");
}

$data = [$date, $team, $opponent, $result, $score, $lost];

$file = fopen("data/data.csv","a");
flock($file, LOCK_EX);
fputcsv($file, $data);
flock($file, LOCK_UN);
fclose($file);

header("Location:index.php");
exit();
