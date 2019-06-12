/*{% set wsPort = app.request.port + 1 %}*/
// var conn = new WebSocket('ws://{{app.request.host}}:{{wsPort}}/remote-controller/{{controllerId}}');
var conn = new WebSocket("wss://environmentaldashboard.org/digital-signage/websockets/remote-controller/{{controllerId}}");
	conn.onmessage = function(e) { console.log(e.data); };
var clicked = function() {
	var button_id = this.getAttribute("data-id");
	conn.send(button_id);
}
var buttons = document.getElementsByTagName("IMG");
for (var i = 0; i < buttons.length; i++) {
	buttons[i].addEventListener('click', clicked, false);
}