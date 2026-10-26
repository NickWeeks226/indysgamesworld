<?php
// Click counter for the world chooser on index.html.
// GET  counter.php              -> {"george":3,"hattie":5}
// POST counter.php?world=hattie -> adds one to that world, returns the new totals.
// Totals are kept in counts.json next to this file on the web server (not in the repo).

header('Content-Type: application/json');
header('Cache-Control: no-store');

$worlds = array('george', 'hattie');
$fh = @fopen(__DIR__ . '/counts.json', 'c+');
if (!$fh) {
  http_response_code(500);
  echo '{"error":"storage"}';
  exit;
}

flock($fh, LOCK_EX);
$saved = json_decode(stream_get_contents($fh), true);
$counts = array();
foreach ($worlds as $w) {
  $counts[$w] = (is_array($saved) && isset($saved[$w])) ? (int)$saved[$w] : 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $world = isset($_GET['world']) ? $_GET['world'] : '';
  if (!in_array($world, $worlds, true)) {
    flock($fh, LOCK_UN);
    fclose($fh);
    http_response_code(400);
    echo '{"error":"unknown world"}';
    exit;
  }
  $counts[$world]++;
  ftruncate($fh, 0);
  rewind($fh);
  fwrite($fh, json_encode($counts));
  fflush($fh);
}

flock($fh, LOCK_UN);
fclose($fh);
echo json_encode($counts);
