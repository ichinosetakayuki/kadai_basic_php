<?PHP

$teamStats = [];

$file = fopen("data/data.csv", "r");
flock($file, LOCK_EX);

if ($file) {
  while ($line = fgetcsv($file)) { // 行をひとつずつ取り出す

    $team = $line[1];
    $opponent = $line[2];
    $result = $line[3];

    if (!isset($teamStats[$team])) { // チームスタッツがなければ0をセットする
      $teamStats[$team] = ["game" => 0, "win" => 0, "lose" => 0, "draw" => 0, "score" => 0, "lost" => 0];
    }
    if (!isset($teamStats[$opponent])) { // 相手チームのスタッツがなければ0をセットする
      $teamStats[$opponent] = ["game" => 0, "win" => 0, "lose" => 0, "draw" => 0, "score" => 0, "lost" => 0];
    }

    // それぞれ勝敗に応じて+1ずつしていく
    if ($result === "勝ち") {
      $teamStats[$team]["win"]++;
      $teamStats[$opponent]["lose"]++;
    } else if ($result === "負け") {
      $teamStats[$team]["lose"]++;
      $teamStats[$opponent]["win"]++;
    } else if ($result === "引き分け") {
      $teamStats[$team]["draw"]++;
      $teamStats[$opponent]["draw"]++;
    }

    $teamScore = (int)$line[4]; // 念の為整数化
    $teamLost = (int)$line[5]; // 念の為、整数化
    $opponentScore = $teamLost;
    $opponentLost = $teamScore;

    $teamStats[$team]["score"] += $teamScore; //足し上げていく
    $teamStats[$team]["lost"] += $teamLost;
    $teamStats[$team]["game"]++;

    $teamStats[$opponent]["score"] += $opponentScore;
    $teamStats[$opponent]["lost"] += $opponentLost;
    $teamStats[$opponent]["game"]++;
  }
}
flock($file, LOCK_UN);
fclose($file);


// 勝率の計算
$teamStatsSorted = []; //チーム名も含めた配列を作る

foreach ($teamStats as $team => $stats) {
  $games = $stats["win"] + $stats["lose"]; // 勝率を計算するための分母になる試合数
  $rate = ($games > 0) ? $stats["win"] / $games : 0; // 試合数が0でなkれば、勝率計算
  $stats["rate"] = round($rate, 3); // 小数点以下、3桁にする
  $stats["team"] = $team; // チームも配列に加える
  $teamStatsSorted[] = $stats; // チーム名も含めた配列
}

usort($teamStatsSorted, function ($a, $b) {
  return $b["rate"] <=> $a["rate"]; // 勝率で降順にソート
});

// 散布図を作るため、平均得点と平均失点を計算し、x軸、y軸、チーム名の配列を作る
$scatterData = [];
foreach ($teamStatsSorted as $stats) {
  $games = $stats["game"];
  $aveScore = ($games > 0) ? round(($stats["score"] / $games), 2) : 0; // 試合数が0でなければ平均得点を計算
  $aveLost = ($games > 0) ? round(($stats["lost"] / $games), 2) : 0; // 同じく平均視点の計算
  $scatterData[] = [
    "x" => $aveLost, // x軸：平均失点
    "y" => $aveScore, // y軸：平均得点
    "team" => $stats["team"]
  ];
}

// echo "<pre>";
// var_dump($scatterData);
// echo "<pre>";
// exit();

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
  <link rel="stylesheet" href="style.css">
  <title>Baseball Dashboard</title>
</head>

<body>
  <header class="dash_header">
    <div>
      <h1>プロ野球チーム成績ダッシュボード</h1>
    </div>
    <div class="header_right">
      <div><a href="index.php">データ入力・結果一覧画面に戻る</a></div>
      <div class="moritaka_img"><img src="img/moritaka_anime00.png" alt="森高アイコン"></div>
    </div>
  </header>

  <main class="wrapper">
    <div class="dash_wrapper">
      <div class="upper_side">
        <div class="upper_side_left">
          <h2>パ・リーグ順位表</h2>
          <table class="ranking">
            <thead>
              <tr>
                <th>順位</th>
                <th>チーム</th>
                <th>試合数</th>
                <th>勝利数</th>
                <th>敗戦数</th>
                <th>引分数</th>
                <th>勝率</th>
                <th>得点</th>
                <th>失点</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $rank = 1;
              foreach ($teamStatsSorted as $teamData) { // 順位表の描画
                echo "<tr>";
                echo "<td>{$rank}</td>";
                echo "<td>" . htmlspecialchars($teamData["team"], ENT_QUOTES, "UTF-8") . "</td>";
                echo "<td>{$teamData["game"]}</td>";
                echo "<td>{$teamData["win"]}</td>";
                echo "<td>{$teamData["lose"]}</td>";
                echo "<td>{$teamData["draw"]}</td>";
                echo "<td>" . number_format($teamData["rate"], 3) . "</td>";
                echo "<td>{$teamData["score"]}</td>";
                echo "<td>{$teamData["lost"]}</td>";
                echo "</tr>";
                $rank++;
              }
              ?>
            </tbody>
          </table>
        </div>
        <div class="upper_side_right">
          <h2>チーム別　勝率グラフ</h2>
          <!-- Chart.jsによる円グラフ描画 -->
          <canvas id="winRateChart" width="500" height="400"></canvas>
        </div>
      </div>

      <div class="lower_side">
        <div>
          <h2>総得点・総失点グラフ</h2>
          <!-- Chart.jsによる棒グラフ描画 -->
          <canvas id="scoreChart" width="600" height="400"></canvas>
        </div>
        <div>
          <h2>チーム別平均得失点チャート</h2>
          <!-- Chart.jsによる散布図描画 -->
          <canvas id="scatterChart" width="600" height="400"></canvas>
        </div>
      </div>
    </div>
  </main>

  <footer class="dash_footer">

  </footer>
  <script>
    // Chart.jsでチャート化するために、データをJSON化
    const teamLabels = <?= json_encode(array_column($teamStatsSorted, "team")) ?>;
    const teamScores = <?= json_encode(array_column($teamStatsSorted, "score")) ?>;
    const teamLosts = <?= json_encode(array_column($teamStatsSorted, "lost")) ?>;
    const teamRate = <?= json_encode(array_column($teamStatsSorted, "rate")) ?>;
    const scatterPoints = <?= json_encode($scatterData) ?>;
  </script>
  <script>
    // 勝率　円グラフ
    const ctxWinRate = document.getElementById("winRateChart").getContext("2d");
    new Chart(ctxWinRate, {
      type: "doughnut",
      data: {
        labels: teamLabels,
        datasets: [{
          label: "勝率",
          data: teamRate,
          backgroundColor: [
            "#01609A", "#FCC700", "#051E46", "#A47B01", "#870010", "#C0C0C0"
          ],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false, // アスペクト比を無視する。必ず親要素にwidth,heightを設定すること!!
        plugins: {
          legend: {
            position: "right",
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                return `${context.label}:${(context.raw*100).toFixed(1)}%`
              }
            }
          }
        }
      }
    });

    // 得点・失点　棒グラフ
    const ctxScoreLost = document.getElementById("scoreChart").getContext("2d");
    new Chart(ctxScoreLost, {
      type: "bar",
      data: {
        labels: teamLabels,
        datasets: [{
            label: "得点",
            data: teamScores,
            backgroundColor: "rgba(54, 162, 235, 0.7)"
          },
          {
            label: "失点",
            data: teamLosts,
            backgroundColor: "rgba(255, 99, 132, 0.7)"
          }
        ]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1
            }
          }
        }
      }
    })


    // 散布図（scatter）
    const ctxScatter = document.getElementById("scatterChart").getContext("2d");
    new Chart(ctxScatter, {
      type: "scatter",
      data: {
        datasets: [{
          label: "チーム別 平均得点Y・平均失点X",
          data: scatterPoints.map(p => ({
            x: p.x,
            y: p.y,
            team: p.team
          })),
          backgroundColor: "#4caf50",
          pointRadius: 6
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: false, // 標準の凡例を非表示
          },
          datalabels: { // オプションのプラグインにより設定
            align: "top",
            font: {
              weight: "bold"
            },
            formatter: function(value) {
              return value.team;
            }
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                const {
                  x,
                  y
                } = context.raw;
                return `得点:${y}, 失点:${x}`;
              }
            }
          }
        },
        scales: {
          x: {
            title: {
              display: true,
              text: "平均失点"
            }
          },
          y: {
            title: {
              display: true,
              text: "平均得点"
            }
          }
        }
      },
      plugins: [ChartDataLabels]
    });
  </script>
</body>

</html>