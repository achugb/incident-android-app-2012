<?php
try {
	$cb = $_GET['callback'];
	$day = $_GET['day'];

	if (!is_numeric($_GET['day'])) {
		throw new Exception("Invalid day. Not a number.");
	} else {
		$day = intval($day);
	}

	$f = "data/day" . $day . ".json";

	if (file_exists($f)) {
		print sprintf(empty($cb) ? "%s" : "$cb(%s)", file_get_contents($f));
	} else {
		throw new Exception("Invalid day.");
	}
} catch(Exception $e) {
	print sprintf('{error: "%s"}', $e->getMessage());
}