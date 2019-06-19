// open websocket conn to receive commands from remote controllers
var conn = new WebSocket("wss://environmentaldashboard.org/digital-signage/websockets/display/{{ app.request.get('id') }}");
var WS_READY = true;
conn.onmessage = function (e) {
	if (WS_READY === false) {
		return;
	} else {
		WS_READY = false;
	}
	var frameId = parseInt(e.data);
	if (e.origin !== 'wss://environmentaldashboard.org' || frameId < 1) {
		return;
	}
	var target = 'frame' + frameId;
	var i, j = 0;
	for (var key in frames) {
		if (frames.hasOwnProperty(key)) {
			for (i = 0; i < frames[key].length; i++) {
				var frame = frames[key][i];
				if (frame.carousel.id === target) {
					clearTimers();
					if (index !== j) {
						sequence[index].element.style.display = 'none';
						index = j;
						sequence[index].element.style.display = '';
					}

					animateFrames(frames[key], key, i);
					break;
				}
			}
		}
		j++;
	}

	WS_READY = true;
};