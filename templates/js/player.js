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

// begin rotating frames in all carousels in the given presentation (ie. sequence[presentationIdx])
animateCarousels(frames[sequence[presentationIdx].element.id], sequence[presentationIdx].element.id);
// advance to next presentation in after current presentations duration
if (sequence.length > 1 && sequence[presentationIdx].duration > 0) {
	// console.log('first nextPres in', sequence[presentationIdx].duration);
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

	for (var carouselId in carouselList) { // for each carousel in the presentation
		if (carouselList.hasOwnProperty(carouselId)) {
			var frames = carouselList[carouselId];
			var frameSequence = [];
			var primaryIframe = document.getElementById(curPres + '-' + carouselId + '-primary');
			var secondaryIframe = document.getElementById(curPres + '-' + carouselId + '-secondary');
			var frameIdx = 0;
			if (secondaryIframe == null) { // todo hacky fix
				// console.log(secondaryIframe, primaryIframe, curPres, carouselId);
				break;
			}
			for (var i = 0; i < frames.length; i++) { // for each frame in the carousel
				frameSequence.push(frames[i]);
				if (frames[i].id === triggerFrame) {
					frameIdx = i;
				}
			}
			// console.log('now', frameSequence[frameIdx].url, frameSequence[frameIdx].dur);
			secondaryIframe.src = frameSequence[frameIdx].url; // reveal first frame of carousel
			if (i > 1) { // if there are multiple frames in carousel
				nextFrame(frameSequence, frameIdx + 1, secondaryIframe, primaryIframe);
			} else { // todo hacky fix
				// setTimeout(nextPres, frameSequence[frameIdx].dur);
			}
		}
	}

	return true;
	
}

function nextPres(increment = true, target = null) {
	if (target === null) {
		clearTimers();
		clearCurrentPres();
	}
	// console.log('arg', sequence[presentationIdx].element);
	// if (nextPres.caller === null) {
	// 	return;
	// }
	sequence[presentationIdx].element.style.display = 'none';
	if (increment && ++presentationIdx === sequence.length) {
		presentationIdx = 0;
	}
	sequence[presentationIdx].element.style.display = '';
	if (target === null) {
		animateCarousels(frames[sequence[presentationIdx].element.id], sequence[presentationIdx].element.id);
	} else {
		animateCarousels(frames[sequence[presentationIdx].element.id], sequence[presentationIdx].element.id, target);
	}
	// console.log('nextPres in', sequence[presentationIdx].duration);
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

function clearCurrentPres() {
	var carousels = sequence[presentationIdx].element.children;
	for (var i = 0; i < carousels.length; i++) {
		var frameList = carousels[i].children;
		for (let j = 0; j < frameList.length; j++) {
			frameList[j].src = 'about:blank';
		}
	}
	// console.log(sequence[presentationIdx].element);
	sequence[presentationIdx].element.style.display = 'none';
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