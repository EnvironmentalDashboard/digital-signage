// open websocket conn to recieve commands from remote controllers
var conn = new WebSocket("ws://localhost:5001/display/{{ app.request.get('id') }}");
conn.onmessage = function (e) {
	var frame_id = parseInt(e.data);
	if (e.origin !== 'ws://localhost:5001' || frame_id < 1) {
		return;
	}
	var frame = document.getElementById('frame' + frame_id);
	var carousel = frame.parentNode;
	var pres = carousel.parentNode;
	var all_frames = carousel.children;
	var i;
	for (i = 0; i < all_frames.length; i++) {
		all_frames[i].style.display = 'none';
	}
	frame.style.display = 'initial';
	if (pres.style.display === 'none') { // not already active pres
		var pres_id = pres.getAttribute('id').substring(4); // cut off 'pres'
		document.getElementById('pres' + pres_ids[cur_pres]).style.display = 'none';
		pres.style.display = 'initial';
		for (i = 0; i < pres_ids.length; i++) {
			if (pres_ids[i] == pres_id) {
				cur_pres = i;
				break;
			}
		}
	}
	clear_timers();
	var carousels = pres.children;
	for (i = 0; i < carousels.length; i++) {
		animate_carousel(carousels[i].children);
	}
	setTimeout(next_pres, pres_durations[pres_ids[cur_pres]]);
};