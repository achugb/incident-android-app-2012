<?php

$res = '';
if (isset($_GET['callback'])) {
	$res = $_GET['callback'] . '(%s)';
} else {
	$res = '%s';
}

try {
    require_once 'poll.php';

    $id = $_REQUEST['id'];
    $votes = @$_REQUEST['vote'];
	if (strlen($id) < 1) throw new Exception("You must specify a poll id.");
	if (strlen($votes) < 1) throw new Exception("Not option was selected");

    $votes = split(',', $votes);

    foreach($votes as &$v) {
        $v = intval($v);
    }

    $p = Poll::getById(intval($id));
    $p->vote($votes, Util::uid());

    $r = $p->getResults();
	print sprintf($res, json_encode($r));

} catch (PollNotFoundException $e) {
    print sprintf($res, '{"error": "Not a valid poll."}');
} catch (PollLockedException $e) {
    print sprintf($res, '{"error": "Poll is currently locked, try again please."}');
} catch (Exception $e) {
    print sprintf($res, sprintf('{"error": "%s"}', $e->getMessage()));
}
