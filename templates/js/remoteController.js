var conn = new WebSocket("wss://environmentaldashboard.org/digital-signage/websockets/remote-controller/{{controllerId}}");
	conn.onmessage = function(e) { console.log(e.data); };
	conn.onerror = function() { location.reload(true); };
	conn.onclose = function() { location.reload(true); };
var clicked = function() {
	var button_id = this.getAttribute("data-id");
	conn.send(button_id);
}
var buttons = document.getElementsByTagName("IMG");
for (var i = 0; i < buttons.length; i++) {
	buttons[i].addEventListener('click', clicked, false);
}