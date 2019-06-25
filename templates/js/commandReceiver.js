// open websocket conn to receive commands from remote controllers
var conn = new WebSocket("{{ websocketBaseUrl }}/digital-signage/websockets/display/{{ app.request.get('id') }}");
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
	if (e.origin !== '{{ websocketBaseUrl }}' || frameId < 1) {
		return;
	}
	var target = 'frame' + frameId;
	for (var key in presentations) {
		if (presentations.hasOwnProperty(key)) {
			var presentation = presentations[key];
			var carousels = presentation['carousels'];
			for (var carouselId in carousels) {
				if (carousels.hasOwnProperty(carouselId)) {
					var frames = carousels[carouselId];
					for (let i = 0; i < frames.length; i++) {
						if (frames[i].id === target) {
							WS_READY = true;
							showPresentation(key, frames[i].id);
							return;
						}
						
					}
				}
			}
		}
	}
	WS_READY = true;
	
};
