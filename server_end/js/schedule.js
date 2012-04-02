(function ($, window, undefined) {

	var endpoint = 'http://incident.in/onmobile/android/schedule/';
	function msg(m) {
		$('#status').text(m);
		$('#status').show();
	}

	function hide_msg() {
		$('#status').hide();
	}

	function getItems($day, callback) {
		msg("Loading...");
		$.ajax({
			type: 'GET',
			url: endpoint + 'day.php?day='+ $day.data('day') + '&callback=?',
			dataType: 'jsonp',
			success: function (data) {
				console.log(data);
				putItems($day, data);
				hide_msg();
			},
			error: function (data) {
				msg("There was an error loading the day");
			}
		});
	}
	
	function putItems($day, data) {
	
		for (var i=0,len= data.length; i<len;i+=1) {
			var event = data[i];
			var $items = $day.find('.schedule-items');
			var $item = $('<div class="schedule-item"></div>'),
				$first = $('<div class="first-line"></div>'),
				$venue = $('<div class="item-venue"></div>').html('Venue &mdash; ' + event.venue),
				$desc = $('<div class="description"></div>').text(event.description),
				$desc_wrap = $('<div class="desc-wrap"></div>').append($desc.before($venue)),
				$title = $('<span class="item-title"></span>').text(event.event),
				$time = $('<span class="item-time"></span>').text(event.time);

			$desc_wrap.append($venue);
			$item.append($first.append($time).append($title)).append($desc_wrap);
			$items.append($item);
			$this.find('.desc-wrap').hide();
			$day.data('state', 'expanded');
		}
	}

	function loadDays() {
		var day_string = '<div class="schedule-day"><div class="schedule-day-title"><span class="day">Day {num}</span><span class="date">{date}</span></div><div class="schedule-items-wrap"><div class="schedule-items"></div></div></div>';
		var days = [
			day_string.replace('{num}', '0').replace('{date}', '29 Feb'),
			day_string.replace('{num}', '1').replace('{date}', '01 Mar'),
			day_string.replace('{num}', '2').replace('{date}', '02 Mar'),
			day_string.replace('{num}', '3').replace('{date}', '03 Mar'),
			day_string.replace('{num}', '4').replace('{date}', '04 Mar')
		];

		for (var i=0, len=days.length; i < len; i+=1) {
			$('#schedule-wrap').append($(days[i]).data('day', '' + i));
		}
		
		$('.schedule-day .first-line').live('click tap', function() {
			$this = $(this).siblings('.desc-wrap');
            if ($this.data('state') == 'expanded') {
                // TODO: animate
                $this.hide()
				//$this.anim({height: 0}, 400);
                $this.data('state', 'closed');
                return;
            } else {
				function openDesc () {
					// $this.anim({height: $this.offset().height}); <-- go psycho
					$this.show();
					//$this.anim({height: 'auto'}, 400);
					$this.data('state', 'expanded');
				}

				openDesc();
            }

		});
		
		
		//remove click when putting on phone
        $('.schedule-day .schedule-day-title').live('click tap', function () {
            $this = $(this).parents('.schedule-day');
            if ($this.data('state') == 'expanded') {
                // TODO: animate
                $this.find('.schedule-items-wrap').hide();
                $this.data('state', 'closed');
                return;
            } else {
                var wrap = $this.find('.schedule-items-wrap');

				function openDay () {
					wrap.show();
					$this.data('state', 'expanded');
				}

				if ($this.data('loaded') == 'true') {
					openDay();
				} else {
					// make a request for schedule
					console.log("getting items");
					getItems($this, openDay);
					$this.data('loaded', 'true');
				}
            }
        });
	}

	function loadSchedule () {
		loadDays();
	}

	window.loadSchedule = loadSchedule;
})(Zepto, window);