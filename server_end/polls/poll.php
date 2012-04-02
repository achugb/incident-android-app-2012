<?php

class PollNotFoundException extends Exception {}
class PollLockedException extends Exception {}

define('POLL_LIST', 'polls.json');

class Poll {
    var $id;
    var $question;
    var $options = array();
    var $multiple = null;
    var $created = null;

    static function getById($id)
    {
        $polls = json_decode(file_get_contents(POLL_LIST), true);

        foreach($polls as $poll) {
            if ($poll['id'] == $id) {
                return Poll::fromArray($poll);
            }
        }

        throw new PollNotFoundException();
    }

    static function newId()
    {
        $polls = json_decode(file_get_contents(POLL_LIST), true);
        $max = $polls[0]['id'];

        foreach($polls as $poll) {
            if ($max < $poll['id']) $max = $poll['id'];
        }

        return $max + 1;
    }

    public function save()
    {
        $this->created = microtime() / 1000000;
        try {
            $data = json_decode(file_get_contents(POLL_LIST));
            if (!is_array($data)) $data = array();
            $data[] = $this->toArray();
            return file_put_contents(POLL_LIST, json_encode($data));
        } catch(Exception $e) {
            throw new $e;
        }
    }

    /** voting and locking **/

    private function lock()
    {
        return touch(Poll::lockFile($this->id));
    }

    private function unlock()
    {
        return unlink(Poll::lockFile($this->id));
    }

    public function locked()
    {
        return file_exists(Poll::lockFile($this->id));
    }

    public function vote($options=array(), $uid)
    {
        if (!is_array($options)) $options = array($options);

        if (count($options) > 1 && !$this->multiple) {
            throw new Exception("Cannot vote on multiple options in this poll." . $this->multiple);
        }

        foreach($options as $opt) {
            if (!isset($this->options[$opt])) {
                throw new Exception("Wrong or non-existant option");
                return false;
            }
        }

        // check if the user has already voted
        if ($this->hasVoteBy($uid)) {
            throw new Exception("You have already voted for this poll");
        } else {
            // cast vote
            $this->addVote($options, $uid);
        }
    }

    private function getVotes()
    {
		static $votes=null;
        static $id=null;

        if (!empty($votes) && $id == $this->id) {
            return $votes;
        }

        $votes = array();
        $votes_file = Poll::votesFile($this->id);

        if (file_exists($votes_file)) {
            $votes = unserialize(file_get_contents($votes_file));
            if (count($votes) != count($this->options)) {
                throw new Exception("Mismatch in the number of vote fields " .
                                    "and number of options. This should " .
                                    "never have happened, report to the " .
                                    "app developer");
            }
        } else {
            foreach($this->options as $key => $opt) {
                $votes[$key] = array();
            }
        }

        $id = $this->id;

        return $votes;
    }

    private function addVote($options, $uid)
    {
        $votes = $this->getVotes();

        foreach($options as $option) {
            if (isset($this->options[$option])) {
                $votes[$option][] = $uid;
            } else {
                throw new Exception("Invalid option <code>$option</code> to add the voter to.");
            }
        }
        $vf = Poll::votesFile($this->id);

        // Locking
        if ($this->locked()) {
            throw new PollLockedException();
        }
        $this->lock();

        file_put_contents($vf, serialize($votes));

        // add 1 to the results file

        $rf = Poll::resultFile($this->id);
        $res = array();

        if (file_exists($rf)) {
            $res = json_decode(file_get_contents($rf));
        } else {
            foreach($this->options as $key => $opt) {
                $res[$key] = 0;
            }
        }

        foreach($options as $option) {
            $res[$option] += 1;
        }

        file_put_contents($rf, json_encode($res));

        $this->unlock();
    }

    function hasVoteBy($uid)
    {
        $votes = $this->getVotes();

        foreach($votes as $option => $opt_votes) {
            if (in_array($uid, $opt_votes)) {
                return true;
            }
        }

        return false;
    }

    /** Conversion to arrays for json dumping **/

    static function fromArray($arr)
    {
        $p = new Poll();
        $keys = array('id', 'question', 'options', 'multiple');
        foreach($keys as $key) {
            if (!isset($arr[$key])) {
                throw new Exception("Array does not contain key: $key");
            }
            $p->{$key} = $arr[$key];
        }
        return $p;
    }

    function toArray($keys=null)
    {
        $arr = array();
        if (!is_array($keys))
            $keys = array('id', 'question', 'options', 'multiple');

        foreach($keys as $key) {
            $arr[$key] = $this->{$key};
        }

        $arr['lift'] = $this->lift();

        return $arr;
    }

    function getResults()
    {
        return json_decode(file_get_contents(Poll::resultFile($this->id)), true);
    }

    /** a number to be used while sorting **/
    function lift()
    {
        $reference = 1329664670; // Feb 19 2012 something
        $seconds = $this->created - $reference;

        $votes = $this->getTotalVotes();
        if ($votes<1) $votes = 1;

        return log($votes, 2) - $seconds / 45000;
    }

    function getTotalVotes()
    {
        $rf = Poll::resultFile($this->id);
        if (!file_exists($rf)) {
            return 0;
        }

        $votes = json_decode(file_get_contents($rf));

        $count = 0;
        foreach($votes as $option => $vs) {
            $count += intval($vs);
        }
    }

    /** Poll utility functions **/

    static function getFile($id)
    {
        if (empty($id)) {
            throw new Exception("URL for an empty poll ID?");
        }
        return 'polls/' . $id . '.json';
    }

    static function lockFile($id)
    {
        return sprintf('polls/%d.json', $id);
    }

    static function votesFile($id)
    {
        return sprintf('polls/votes/%d.ser', $id);
    }

    static function resultFile($id)
    {
        return sprintf('polls/results/%d.json', $id);
    }
}

class PollList {
    var $polls = array();

    function addPoll(Poll $p)
    {
        $this->polls[] = $p;
    }

    function sort()
    {
        usort(&$this->polls, array('PollList', 'compare'));
        return $this->polls;
    }

    function compare(Poll $p1, Poll $p2)
    {
        return $p1->lift() - $p2->lift();
    }

    /**
     * Load all the polls to memory, sort them using the lift and
     * put them back in the same titles file
     */
    static function resort($file='polls.json')
    {
        $f = POLL_LIST;
        if (file_exists($f)) {
            $list = new self();
            $polls = json_decode(file_get_contents($f), true);
            foreach($polls as $poll) {
                $list->addPoll(Poll::fromArray($poll));
            }

            $list->sort();

            $titles = array();
            foreach($list->polls as $poll) {
                $titles[] = $poll->toArray();
            }

            file_put_contents($f, json_encode($titles));

            return $polls;
        } else {
            throw new Exception("No polls file: " . POLL_LIST);
        }
    }
}

class Util {
	static function uid()
	{
		return substr(sha1(trim($_SERVER['HTTP_USER_AGENT'])), 2, 10);
		//return $_SERVER['HTTP_USER_AGENT'];
	}
}