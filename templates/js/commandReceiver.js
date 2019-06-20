// open websocket conn to receive commands from remote controllers
var conn = new WebSocket("wss://environmentaldashboard.org/digital-signage/websockets/display/{{ app.request.get('id') }}");
var WS_READY = true;
conn.onerror = function() { location.reload(true); };
conn.onclose = function() { location.reload(true); };
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
	var j = 0;
	for (var key in frames) {
		if (frames.hasOwnProperty(key)) {
			for (const carousel in frames[key]) {
				if (frames[key].hasOwnProperty(carousel)) {
					for (var i = 0; i < frames[key][carousel].length; i++) {
						if (frames[key][carousel][i].id === target) {
							clearTimers();
							if (animateCarousels(frames[key], key, target)) {
								if (presentationIdx !== j) {
									sequence[presentationIdx].element.style.display = 'none';
									presentationIdx = j;
									sequence[presentationIdx].element.style.display = '';
								}	
							}
							// console.log(frames[key], key, i);
							break;
						}
						
					}
					
				}
			}
		}
		j++;
	}

	WS_READY = true;
};