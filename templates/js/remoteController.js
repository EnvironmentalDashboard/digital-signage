{% set wsPort = app.request.port + 1 %}
var conn = new WebSocket('ws://{{app.request.host}}:{{wsPort}}/remote-controller/{{controllerId}}');
	conn.onmessage = function(e) { console.log(e.data); };
	conn.onopen = function(e) { conn.send('1') };
var clicked = function() {
	var button_id = this.getAttribute("data-id");
	console.log(button_id);
	conn.send(button_id);
}
var buttons = document.getElementsByTagName("IMG");
for (var i = 0; i < buttons.length; i++) {
    buttons[i].addEventListener('click', clicked, false);
}