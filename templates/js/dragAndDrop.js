// https://developer.mozilla.org/en-US/docs/Web/API/HTML_Drag_and_Drop_API,
// https://mdn.github.io/dom-examples/drag-and-drop/copy-move-DataTransfer.html
function dragstart_handler(ev) {
	// Change the source element's background color to signify drag has started
	ev.currentTarget.style.background = "var(--light)";
	// Add the id of the drag source element to the drag data payload so
	// it is available when the drop event is fired
	ev.dataTransfer.setData("text", ev.target.id);
	// Tell the browser both copy and move are possible
	ev.effectAllowed = "copyMove";
}
function dragover_handler(ev) {
	// Change the target element's border to signify a drag over event
	// has occurred
	// ev.currentTarget.style.background = "var(--light)";
	ev.preventDefault();
}
function drop_handler(ev) {
	ev.preventDefault();
	// Get the id of drag source element (that was added to the drag data
	// payload by the dragstart event handler)
	var id = ev.dataTransfer.getData("text");
	var text = document.getElementById(id).innerHTML;
	ev.target.innerHTML = text;
	var twigKey = ev.target.getAttribute('data-twig');
	var carousel_id = id.substring(8); // cut off 'carousel'
	var input = document.getElementById('frame-arrangement-' + ev.target.parentNode.getAttribute('data-pres'));
	var cur_elems = JSON.parse(input.value);
	cur_elems[twigKey] = carousel_id;
	input.value = JSON.stringify(cur_elems);
}
function controller_drop_handler(ev) {
	ev.preventDefault();
	var id = ev.dataTransfer.getData("text");
	var text = document.getElementById(id).getAttribute('src');
	var buttonElement = ev.target.parentNode;
	var twigKey = buttonElement.getAttribute('data-twig');
	// console.log(ev.target);
	buttonElement.innerHTML = "<img src='" + text + "' class='img-fluid' />";
	// console.log(ev.target);
	var button_id = id.substring(6); // cut off 'button'
	var input = document.getElementById('button-arrangement-' + buttonElement.parentNode.getAttribute('data-controller'));
	// console.log(buttonElement, buttonElement.parentNode, input)
	var cur_elems = JSON.parse(input.value);
	cur_elems[twigKey] = button_id;
	input.value = JSON.stringify(cur_elems);
}
function dragend_handler(ev) {
	// Restore source's border
	ev.target.style.border = "none";
	// Remove all of the drag data
	ev.dataTransfer.clearData();
}