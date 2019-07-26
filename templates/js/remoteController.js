var conn = new WebSocket("{{ websocketBaseUrl }}/digital-signage/websockets/remote-controller/{{controllerId}}");
	conn.onmessage = function(e) { console.log(e.data); };
	conn.onerror = function() { location.reload(true); };
	conn.onclose = function() { location.reload(true); };
var clicked = function() {
	var button_id = this.getAttribute("data-id");
	var button_type = this.getAttribute("data-type");
	var that = this;
	that.style.top = '15px';
	setTimeout(function() { that.style.top = ''; }, 150);
	if (button_type === "{{constant('App\\Entity\\Button::TRIGGER_URL')}}") {
		location.href = that.getAttribute("data-url");
	} else {
		console.log(button_type);
		conn.send(button_id);
	}
}
var buttons = document.getElementsByTagName("IMG");
for (var i = 0; i < buttons.length; i++) {
	buttons[i].addEventListener('click', clicked, false);
}