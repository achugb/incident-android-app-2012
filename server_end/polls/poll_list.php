<?php
require_once 'poll.php';
define('CHECK_VOTED', true);

$res = '';
if (isset($_GET['callback'])) {
	$res = $_GET['callback'] . '(%s)';
} else {
	$res = '%s';
}
if (CHECK_VOTED) {
    $uid = Util::uid();
    $polls = json_decode(file_get_contents('polls.json'), true);
    $new_polls = array();
    foreach($polls as $poll) {
        $p = Poll::fromArray($poll);
        if ($p->hasVoteBy($uid)) {
            $arr = $poll;
            $arr['has_voted'] = true;
            $arr['results'] = $p->getResults();
            $new_polls [] = $arr;
        } else {
            $poll['has_voted'] = false;
            $new_polls[] = $poll;
        }
    }
    echo sprintf($res, json_encode($new_polls));
} else {
    echo sprintf($res, file_get_contents('polls.json'));
}
