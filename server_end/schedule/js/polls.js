(function ($, window, undefined) {
    var polls = undefined;

    function count(arr)
    {
        var c = 0;
        for (i in arr) {
            if (!arr.hasOwnProperty(i)) continue;
            c += arr[i];
        }
        return c;
    }

    function ud(something)
    {
        return typeof(something) == 'undefined';
    }

    function showResults(id, data)
    {
        console.log($('#poll-' +  id));
        var $poll = $('#poll-' + id);
        var sum = count(data);
        $poll.find('.poll-option').each(function(e) {
            votes = data[e];
            var $vote = $('<span class="vote"></span>').text(votes);
            var full_w = $(this).offset().width;
            var bar_l = votes / sum * full_w;
            var $bar = $('<div class="votes-bar"></div>').text(' ').css('width', bar_l);
            $(this).find('.left-space').empty().append($vote);
            $(this).append($bar);
        });

        $poll.find('.poll-controls').hide();
    }

    function pollControls(id)
    {
        var v = $('<input type="button" class="poll-vote" value="vote"></input>'),
            i = $('<input type="hidden" name="id" value="' + id +'"></input>');

        v.click(function () {
            var $form = $(this).parents('form'), data = {};

            data.id   = $form.find('[name=id]').attr('value');
            data.vote = [];

            $form.find('[name=vote]').each(function (e) {
                if ($(this).attr('checked')) {
                    data.vote.push($(this).attr('value'));
                }
            });

            if (data.vote.length < 1) {
                alert("You didn't select any option.");
            }

            data.vote = data.vote.join(',');

            $.ajax({
                url: 'vote.php',
                dataType: 'json',
                data: data,
                success: function (data) {
                    alert(data);
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    console.log(id, data);
                    showResults(id, data);
                },
                error: function (data) {
                    alert("There was an error voting. Try again.");
                }
            });
        });

        var wrap = $('<div class="poll-controls"></div>')
                        .append(v)
                        .append(i);
        return wrap;
    }

    function setup()
    {
        $('.poll .poll-question').live('click tap', function () {
            $this = $(this).parents('.poll');
            if ($this.data('state') == 'expanded') {
                // TODO: animate
                $this.find('.poll-options-wrap').anim({height: 0});
                $this.data('state', 'closed');
                return;
            } else {
                var wrap = $this.find('.poll-options-wrap');

                wrap.anim({height: wrap.find('.poll-options').offset().height});
                $this.data('state', 'expanded');
            }
        });
    }

    function renderOptions(poll, $this) {
        console.log(poll);
        $this.find('.poll-options').empty();
        var opts = poll.options;

        console.log(poll);
        for(i in opts) {
            if (!opts.hasOwnProperty(i)) continue;
            var pid = $this.data('id');
            var left = $('<div class="left-space"></div>');
            var opt = $('<input/>')
                .attr('value', i)
                .attr('name', 'vote')
                .attr('id', 'poll-' + pid + '-option-' + i)
                .attr('type', poll.multiple ? 'checkbox' : 'radio'),
               
                label = $('<label for="poll-' + pid + '-option-'+ i + '"></label>').text(opts[i]),
                nl = $('<br/>');

            left.append(opt);
            var wrap = $('<div class="poll-option"></div>');
            wrap.append(left)
                .append(label)
                .append(nl);

            $this.find('.poll-options').append(wrap);
        }
        $this.find('.poll-options').append(pollControls(poll.id));
    }

    function getPoll(id, callback)
    {
        $.ajax({
            url: 'polls/' + id + '.json',
            type: 'GET',
            dataType: 'json',
            params: {random: Math.random()},
            success: callback,
            error: function () {
                alert("There was an error loading the poll options.");
            }
        });
    }

    function loadPolls(offset, count)
    {
        if (ud(offset)) offset = 0;
        if (ud(count))  count = 20;

        function showPolls(ps) {
            var i;
            for(i in ps) {
                if (!ps.hasOwnProperty(i)) continue;

                var item  = ps[i],
                    wrap  = $('<div class="poll"></div>'),
                    opts  = $('<form class="poll-options"></form>'),
                    opts_wrap = $('<div class="poll-options-wrap"></div>'),
                    title = $('<div class="poll-question"></div>')
                                .text(item.question);
                opts_wrap.append(opts);

                wrap.append(title).append(opts_wrap).data('id', item.id);
                wrap.attr('id', 'poll-' + item.id);
                renderOptions(ps[i] , wrap);

                $('#polls-wrap').append(wrap);

                // if user has already voted show results
                if (!ud(item.has_voted) && item.has_voted) {
                    showResults(item.id, item.results);
                }
            }
        }

        getPolls(showPolls);
    }

    function getPolls(callback)
    {
        if (!ud(polls) && polls) {
            callback(polls);
            return;
        }

        $.ajax({
            type: 'GET',
            url: 'poll_list.php',
            dataType: 'json',
            data: {random: Math.random()},
            success: function (data) {
                console.log(data);
                polls = data;
                callback(polls);
            },
            error: function (data) {
            console.log(data);
            }
        });
    }

    window.loadPolls = loadPolls;
    $(document).ready(setup);
})(Zepto, window);
