<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Document</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script>
    const teams = ["ソフトバンク", "日本ハム", "ロッテ", "楽天", "オリックス", "西武"];

    // チームを入力して、相手チームのセレクトボックスに
    // そのチーム以外のチームを作る関数
    function updateOpponent(selectedTeam) {
      $("#opponent").empty();
      if (!selectedTeam) return; //チームが選ばれていなかったら、何も追加しない
      teams.forEach((team) => {
        if (team !== selectedTeam) {
          $("#opponent").append($("<option>").val(team).text(team));
        }
      })
    }

    // 相手チームの選択肢の初期値を空にする関数
    $(document).ready(function() {
      updateOpponent("");
      $("#team").on("change", function() {
        const selected = $(this).val();
        updateOpponent(selected);
      });
    });
  </script>
</head>

<body>
  <header class="dash_header">
    <div>
      <h1>プロ野球チーム成績ダッシュボード</h1>
    </div>
    <div class="header_right">
      <div><a href="read.php">成績ダッシュボードへ</a></div>
      <div class="moritaka_img"><img src="img/moritaka_anime00.png" alt="森高アイコン"></div>
    </div>
  </header>

  <form action="create.php" method="POST">
    <fieldset>
      <legend>試合結果の入力画面</legend>
      <div class="input_date">
        <label for="date">日付</label>
        <input type="date" name="date" id="date" required>
      </div>
      <div class="team_box">
        <div class="input_team">
          <label for="team">チーム</label>
          <select name="team" id="team" required>
            <option value="">選択してください</option>
            <option value="ソフトバンク">ソフトバンク</option>
            <option value="日本ハム">日本ハム</option>
            <option value="ロッテ">ロッテ</option>
            <option value="楽天">楽天</option>
            <option value="オリックス">オリックス</option>
            <option value="西武">西武</option>
          </select>
        </div>
        <div class="input_opponent">
          <label for="opponent">相手チーム</label>
          <select name="opponent" id="opponent" required></select>
        </div>
      </div>

      <div class="result_box">
        <div class="input_result">
          <label for="result">勝敗</label>
          <select name="result" id="result" required>
            <option value="">--選択--</option>
            <option value="勝ち">勝ち</option>
            <option value="負け">負け</option>
            <option value="引き分け">引き分け</option>
          </select>
        </div>
        <div class="input_score">
          <label for="score">得点</label>
          <input type="number" name="score" id="score">
        </div>
        <div class="input_lost">
          <label for="lost">失点</label>
          <input type="number" name="lost" id="lost">
        </div>
      </div>

      <div class="submit_btn">
        <button type="submit">入力</button>
      </div>
    </fieldset>

  </form>

  <hr>

  <?PHP

  $gameArray = [];

  $file = fopen("data/data.csv", "r");
  flock($file, LOCK_EX);

  if ($file) {
    while ($line = fgetcsv($file)) { // 行をひとつずつ取り出す
      $tds = "";
      foreach ($line as $cell) { // 各行ごとにセルをひとつづつ<td>にしていく
        $tds .= "<td>" . htmlspecialchars($cell, ENT_QUOTES, "UTF-8") . "</td>";
      }
      $gameArray[] = "<tr>{$tds}</tr>"; // 複数の<td>を<tr>の中に入れ、行数分の配列がある。試合結果一覧の配列

    }
  }
  flock($file, LOCK_UN);
  fclose($file);
  ?>

  <div class="result_wrapper">
    <h2>試合結果一覧</h2>
    <table class="result_list">
      <thead>
        <tr>
          <th>日付</th>
          <th>チーム</th>
          <th>相手</th>
          <th>勝敗</th>
          <th>得点</th>
          <th>失点</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($gameArray as $row) {
          echo $row;
        } ?>
      </tbody>
    </table>
  </div>

</body>

</html>