var activeTimeouts = [];
var activeIntervals = [];

var sequence = [];
var frames = [];

var presentationIdx = 0;

for (var key in presentations) {
	if (presentations.hasOwnProperty(key)) {
		var presentation = presentations[key];
		var presElement = document.getElementById('pres' + key);
		var duration = 0;
		for (var presId in presentation) {
			if (presId === 'carousels') {
				var carousel = presentation[presId];
				for (var carouselId in carousel) {
					if (carousel.hasOwnProperty(carouselId)) {
						for (var i = 0; i < carousel[carouselId].length; i++) {
							// console.log(carouselId, presElement.id, carousel[carouselId][i]);
							if (frames[presElement.id] === undefined) {
								frames[presElement.id] = [];
							}
							if (frames[presElement.id][carouselId] === undefined) {
								frames[presElement.id][carouselId] = [];
							}
							frames[presElement.id][carouselId].push(carousel[carouselId][i]);
						}
					}
				}
			}
			if (presId === 'duration') {
				duration = presentation[presId];
			}
		}
		sequence.push({ element: presElement, duration: duration });

	}
}

animateCarousels(frames[sequence[presentationIdx].element.id], sequence[presentationIdx].element.id);
if (sequence.length > 1 && sequence[presentationIdx].duration > 0) {
	setTimeout(nextPres, sequence[presentationIdx].duration);
}

function animateCarousels(carouselList, curPres, triggerFrame = null) {

	var nextFrame = function(sequence, frameIdx, targetElement, loadingElement) {
		if (frameIdx === sequence.length) {
			frameIdx = 0;
		}
		loadingElement.classList = 'fade-out';
		// console.log('next', frameSequence[frameIdx].url, sequence[frameIdx].dur);
		loadingElement.src = sequence[frameIdx].url;
		targetElement.classList = 'fade-in';
		var timeout = setTimeout(function() {
			nextFrame(sequence, frameIdx + 1, loadingElement, targetElement);
		}, sequence[frameIdx].dur);
		activeTimeouts.push(timeout);
	}

	for (var carouselId in carouselList) {
		if (carouselList.hasOwnProperty(carouselId)) {
			var frames = carouselList[carouselId];
			var frameSequence = [];
			var primaryIframe = document.getElementById(curPres + '-' + carouselId + '-primary');
			var secondaryIframe = document.getElementById(curPres + '-' + carouselId + '-secondary');
			var frameIdx = 0;
			if (secondaryIframe == null) { // todo hacky fix
				break;
			}
			for (var i = 0; i < frames.length; i++) {
				frameSequence.push(frames[i]);
				if (frames[i].id === triggerFrame) {
					frameIdx = i;
				}
			}
			// console.log('now', frameSequence[frameIdx].url);
			secondaryIframe.src = frameSequence[frameIdx].url;
			if (i > 1) {
				nextFrame(frameSequence, frameIdx + 1, secondaryIframe, primaryIframe);
			}
		}
	}

	return true;
	
}

function nextPres() {
	clearTimers();
	sequence[presentationIdx].element.style.display = 'none';
	if (++presentationIdx === sequence.length) {
		presentationIdx = 0;
	}
	sequence[presentationIdx].element.style.display = '';
	animateCarousels(frames[sequence[presentationIdx].element.id], sequence[presentationIdx].element.id);
	// console.log('next pres in', sequence[presentationIdx].duration);
	setTimeout(nextPres, sequence[presentationIdx].duration);
}


function clearTimers() {
	while (activeTimeouts.length > 0) {
		var timeout = activeTimeouts.pop();
		clearInterval(timeout);
	}
	while (activeIntervals.length > 0) {
		var interval = activeIntervals.pop();
		clearInterval(interval);
	}
}

// utilities
function updateQueryStringParameter(uri, key, value) {
	var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
	var separator = uri.indexOf('?') !== -1 ? "&" : "?";
	if (uri.match(re)) {
		return uri.replace(re, '$1' + key + "=" + value + '$2');
	}
	else {
		return uri + separator + key + "=" + value;
	}
}