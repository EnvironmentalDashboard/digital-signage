// open websocket conn to receive commands from remote controllers
var conn = new WebSocket("wss://environmentaldashboard.org/digital-signage/websockets/display/{{ app.request.get('id') }}");
var WS_READY = true;
conn.onmessage = function (e) {
	if (WS_READY === false) {
		console.log("NOT READY");
		return;
	} else {
		WS_READY = false;
	}
	var frameId = parseInt(e.data);
	if (e.origin !== 'wss://environmentaldashboard.org' || frameId < 1) {
		return;
	}
	var target = 'frame' + frameId;
	for (let i = 0; i < frames[sequence[index].element.id].length; i++) {
		if (frames[sequence[index].element.id][i].carousel.id === target) {
			clearTimers();
			console.log(i);
			animateFrames(frames[sequence[index].element.id], sequence[index].element.id, i);
			break;
		}
		
	}
	WS_READY = true;
};