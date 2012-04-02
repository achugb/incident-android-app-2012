(function ($, window, undefined) {

	function msg(m) {
		$('#status').text(m);
		$('#status').show();
	}

	function hide_msg() {
		$('#status').hide();
	}

	function loadResults (results) {
		console.log(results);
		var none = true;
		for (var i=0, len = results.length; i<len; i+=1) {
			var tw = results[i];

			var hashm_re = /#m[\s\.\,$\|]+|#m$/g;
			// only tweets with #m in them.
			if (tw.text.search(hashm_re) < 0) continue;
			none = false;
			// remove #m
			tw.text.replace(hashm_re, '');

			var time = new Date(tw.created_at);
			var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
			var d_time = '' + time.getHours() + ':' + time.getMinutes() + ' | ' + time.getDate() + ' ' + months[time.getMonth()] + ' ' + time.getFullYear();
			var $tweet_wrap = $('<div class="tweet-wrap"></div>');
			var $tweet = $('<div class="tweet"></div>').text(tw.text);
			var $time = $('<div class="time"></div>').html(d_time);
			$('#updates-wrap').append($tweet_wrap.append($tweet).append($time));
		}
		hide_msg();
		if (none) {
			msg("There are no updates yet. Stay tuned.");
		}
	}

	function loadUpdates () {
		msg("Loading...");
		$.ajax({
			url: 'https://twitter.com/statuses/user_timeline/incidentNITK.json?callback=?',
			dataType: 'jsonp',
			success: function (data) {
				loadResults(data);
			}
		})
	}
	window.loadUpdates = loadUpdates;
})(Zepto, window);