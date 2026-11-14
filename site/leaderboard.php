<?php
// Leaderboards for the games on george.html.
// GET  leaderboard.php?game=snake                    -> {"scores":[{"n":"Sam","s":42},...]}  (best 10)
// POST leaderboard.php  game=snake&name=Sam&score=42 -> adds the score, returns the new best 10.
// Each name appears once per game with its best score. Scores are kept in scores.json next to
// this file on the web server (not in the repo). Delete that file to clear every leaderboard.

header('Content-Type: application/json');
header('Cache-Control: no-store');

// game => [lower score is better?, biggest believable score]
$games = array(
  'maze' => array(false, 200), 'foot' => array(false, 10), 'rug' => array(false, 10),
  'cri' => array(false, 100), 'code' => array(false, 50), 'olymp' => array(false, 12),
  'pin' => array(false, 1000000), 'space' => array(false, 1000000), 'golf' => array(true, 500),
  'snake' => array(false, 100000), 'whack' => array(false, 100000), 'fish' => array(false, 100000), 'fight' => array(false, 100000),
);
$keep = 10;

function fail($code, $why) {
  http_response_code($code);
  echo json_encode(array('error' => $why));
  exit;
}

function rude($name) {
  $flat = strtolower($name);
  $squashed = preg_replace('/[^a-z]/', '', strtr($flat, '013457', 'oieast'));
  $stems = array('fuck', 'shit', 'cunt', 'bitch', 'bastard', 'wank', 'twat', 'slut', 'whore',
    'nigg', 'fag', 'piss', 'penis', 'vagina', 'porn', 'nazi', 'rape', 'bollock', 'boob');
  foreach ($stems as $s) if (strpos($squashed, $s) !== false) return true;
  $words = array('sex', 'ass', 'arse', 'dick', 'cock', 'tit', 'tits', 'kill', 'die', 'poop', 'willy');
  foreach (preg_split('/\s+/', $flat) as $w) if (in_array($w, $words, true)) return true;
  return false;
}

$game = isset($_REQUEST['game']) ? $_REQUEST['game'] : '';
if (!isset($games[$game])) fail(400, 'game');
$low = $games[$game][0];

$fh = @fopen(__DIR__ . '/scores.json', 'c+');
if (!$fh) fail(500, 'storage');
flock($fh, LOCK_EX);
$all = json_decode(stream_get_contents($fh), true);
if (!is_array($all)) $all = array();
$list = (isset($all[$game]) && is_array($all[$game])) ? $all[$game] : array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim(preg_replace('/\s+/u', ' ', isset($_POST['name']) ? (string)$_POST['name'] : ''));
  $score = isset($_POST['score']) ? $_POST['score'] : '';
  $bad = null;
  if (!preg_match('/^[\p{L}\p{N} ]{1,12}$/u', $name)) $bad = 'name';
  elseif (rude($name)) $bad = 'rude';
  elseif (!preg_match('/^\d{1,7}$/', (string)$score) || (int)$score > $games[$game][1]) $bad = 'score';
  if ($bad) { flock($fh, LOCK_UN); fclose($fh); fail(400, $bad); }
  $score = (int)$score;

  $found = false;
  foreach ($list as &$row) {
    if (strcasecmp($row['n'], $name) === 0) {
      $found = true;
      if ($low ? $score < $row['s'] : $score > $row['s']) { $row['s'] = $score; $row['n'] = $name; }
    }
  }
  unset($row);
  if (!$found) $list[] = array('n' => $name, 's' => $score);
  usort($list, function ($a, $b) use ($low) { return $low ? $a['s'] - $b['s'] : $b['s'] - $a['s']; });
  $list = array_slice($list, 0, $keep);
  $all[$game] = $list;
  ftruncate($fh, 0);
  rewind($fh);
  fwrite($fh, json_encode($all));
  fflush($fh);
}

flock($fh, LOCK_UN);
fclose($fh);
echo json_encode(array('scores' => $list));
