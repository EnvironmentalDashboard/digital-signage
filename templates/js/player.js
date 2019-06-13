var pres_durations = {{ pres_durations|json_encode|raw }};
var pres_ids = Object.keys(pres_durations);
var cur_pres = 0, last_pres = pres_ids.length - 1;
var active_intervals = [], active_timeouts = [];


function next_pres() {
	document.getElementById('pres' + pres_ids[cur_pres]).style.display = 'none';
	if (cur_pres === last_pres) {
		cur_pres = 0;
	} else {
		cur_pres++;
	}
	document.getElementById('pres' + pres_ids[cur_pres]).style.display = 'initial';
	clear_timers();
	// rotate frames in carousels
	var carousels = document.getElementById('pres' + pres_ids[cur_pres]).children;
	for (var i = 0; i < carousels.length; i++) {
		animate_carousel(carousels[i].children);
	}
	if (pres_durations[pres_ids[cur_pres]] !== 0) {
		setTimeout(next_pres, pres_durations[pres_ids[cur_pres]]);	
	}
}

function animate_carousel(frames) {
	if (frames.length > 1) { // if there are multiple frames to swap between
		var i;
		var total_time = 0; // total duration of the cycle of presentations
		for (i = 0; i < frames.length; i++) {
			frames[i].src = frames[i].src;
			var dur = parseInt(frames[i].getAttribute('data-duration'));
			total_time += dur;
		}

		var start_in = 0; // offset when each frame starts by duration of the frames that come before it
		for (i = 0; i < frames.length; i++) {
			if (i > 0) {
				var last_frame = frames[i - 1];
				start_in += parseInt(last_frame.getAttribute('data-duration'));
			}
			if (i === frames.length - 1) {
				var next_frame = frames[0];
			} else {
				var next_frame = frames[i + 1];
			}
			// console.log('moving from', frames[i], '=>', next_frame, 'in', total_time)
			set_timers(frames[i], next_frame, total_time, start_in);
			var youtube_id = getYoutubeId(frames[i].src);
			if (youtube_id !== false) {
				var new_src = updateQueryStringParameter(frames[i].src, 'autoplay', '1');
				new_src = updateQueryStringParameter(new_src, 'mute', '1'); // see https://stackoverflow.com/a/50272974 for why mute=1
				frames[i].src = new_src;
				var finished = setTimeout(function () {
					frames[i].src = updateQueryStringParameter(frames[i].src, 'autoplay', '0');
				}, frames[i].getAttribute('data-duration'));
				active_timeouts.push(finished);
			}
		}

	}
}

function set_timers(cur_frame, next_frame, total_time, start_in) {
	var offset = setTimeout(function () {
		var every = setInterval(function () {
			cur_frame.className = 'fade-out';
			next_frame.className = 'fade-in';
			// need to add ?autoplay=1&mute=1 to youtube url to play
			var youtube_id = getYoutubeId(next_frame.src);
			if (youtube_id !== false) {
				var new_src = updateQueryStringParameter(next_frame.src, 'autoplay', '1');
				new_src = updateQueryStringParameter(new_src, 'mute', '1');
				next_frame.src = new_src;
				var finished = setTimeout(function () {
					next_frame.src = updateQueryStringParameter(next_frame.src, 'autoplay', '0');
				}, next_frame.getAttribute('data-duration'));
				active_timeouts.push(finished);
			}
		}, total_time);
		active_intervals.push(every);
	}, start_in);
	active_timeouts.push(offset);
}
function clear_timers() {
	var i;
	for (i = 0; i < active_intervals.length; i++) {
		clearInterval(active_intervals[i]);
	}
	for (i = 0; i < active_timeouts.length; i++) {
		clearTimeout(active_timeouts[i]);
	}
}

// begin cycling through presentations
if (pres_durations[pres_ids[cur_pres]] !== 0) {
	setTimeout(next_pres, pres_durations[pres_ids[cur_pres]]);	
}
// begin rotating frames in carousels
var carousels = document.getElementById('pres' + pres_ids[cur_pres]).children;
for (var i = 0; i < carousels.length; i++) {
	animate_carousel(carousels[i].children);
}

// utilities
function updateQueryStringParameter(uri, key, value) {
	var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
	var separator = uri.indexOf('?') !== -1 ? "&" : "?";
	if (uri.match(re)) {
		return uri.replace(re, '$1' + key + "=" + value + '$2');
	}
	else {
		return uri + separator + key + "=" + value;
	}
}