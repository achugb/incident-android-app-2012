<!doctype html>
<html>
<head>

</head>

<body>
<pre>
<?php
require_once "poll.php";
$arr = array();

if (isset($_POST['newpoll'])) {
    echo "Adding new poll.";
    $poll = $_POST['newpoll'];

    $data = preg_split('/\s*\`\s*/', $poll);
    if (count($data) < 3) {
        throw new Exception("too less arguments; use ` as seperator.");
    }

    $arr['question'] = array_shift($data);

    $multiple = array_shift($data);

    if ($multiple == 'true') $multiple = true;
    else if ($multiple == 'false') $multiple = false;
    else throw new Exception("2nd field must be either true or false");

    $arr['multiple'] = $multiple;

    $arr['options'] = $data;

    $arr['id'] = Poll::newId();

    $p = Poll::fromArray($arr);
    $p->save();

    echo "poll added.\n";
}

echo "reordering list of polls...";
$polls = PollList::resort();
echo " done.";

foreach ($polls as $poll) {
    $poll = Poll::fromArray($poll);

    echo "\n" . $poll->question . "\n";
    echo '<a href="?delete=' . $poll->id . '">delete</a>';
    echo "\t" . ($poll->multiple ? 'multiple' : 'single') . " answer". ($poll->multiple ? "s" : "") ."\n";

    foreach($poll->options as $opt) {
        echo "\t\t$opt\n";
    }
}

?>

new poll.
format: question` multiple_correct (true|false)` options..
<form action="" method="POST">
<input type="text" name="newpoll">
<input type="submit" value="create">
</form>
</body>
</html>
