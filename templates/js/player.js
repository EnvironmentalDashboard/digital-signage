var presentations = {{ presentations| json_encode | raw }};
var activeTimeouts = [];
var activeIntervals = [];
var sequence = [];
var frames = [];
var totalDur = 0;
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
							frames[presElement.id].push({ carouselId: carouselId, carousel: carousel[carouselId][i] });
						}
					}
				}
			}
			if (presId === 'duration') {
				duration = presentation[presId];
			}
		}
		sequence.push({ element: presElement, duration: duration });
		totalDur += duration;
		// if (duration !== 0) {
		//     console.log(presElement, duration);
		//     setInterval(function() {
		//         console.log('hide', presElement);
		//     }, duration);
		// }

	}
}


var counter = 0;
while (counter < sequence.length) {
	var timeout = setTimeout(function () {
		if (sequence.length > 1) {
			var interval = setInterval(function () {
				nextPres(sequence);
			}, totalDur);
			activeIntervals.push(interval);
			nextPres(sequence);
		}
	}, sequence[counter].duration);
	activeTimeouts.push(timeout);
	animateFrames(frames[sequence[counter].element.id], sequence[counter].element.id);
	counter++;
}


var index = 0;
function nextPres(sequence) {
	clearTimers();
	var len = sequence.length;
	animateFrames(frames[sequence[index].element.id], sequence[index].element.id);
	sequence[index].element.style.display = 'none';
	if (++index === len) {
		index = 0;
	}
	sequence[index].element.style.display = '';
}


function animateFrames(frameList, curPres, i = 0) {
	// console.log(frameList[i].carousel.url);
	var primaryIframe = document.getElementById(curPres + '-' + frameList[i].carouselId + '-primary');
	var secondaryIframe = document.getElementById(curPres + '-' + frameList[i].carouselId + '-secondary');
	primaryIframe.src = frameList[i].carousel.url;
	primaryIframe.className = 'fade-in';
	secondaryIframe.className = 'fade-out';
	var nextFrame = function () {
		if (++i === frameList.length) {
			i = 0;
		}
		var primaryIframe = document.getElementById(curPres + '-' + frameList[i].carouselId + '-primary');
		var secondaryIframe = document.getElementById(curPres + '-' + frameList[i].carouselId + '-secondary');
		if (primaryIframe.className === 'fade-in') {
			var toHide = primaryIframe;
			var toShow = secondaryIframe;
		} else {
			var toHide = secondaryIframe;
			var toShow = primaryIframe;
		}
		toHide.className = 'fade-out';
		toShow.className = 'fade-in';
		var onAnimEnd = function () {
			this.src = frameList[i].carousel.url;
			this.removeEventListener('animationend', onAnimEnd)
		};
		toHide.addEventListener("animationend", onAnimEnd);
		// toHide.src = frameList[i].carousel.url;
		var timeout = setTimeout(nextFrame, frameList[i].carousel.dur);
		activeTimeouts.push(timeout);
		// var youtube_id = getYoutubeId(frames[i].src);
		// if (youtube_id !== false) {
		// 	var new_src = updateQueryStringParameter(frames[i].src, 'autoplay', '1');
		// 	new_src = updateQueryStringParameter(new_src, 'mute', '1'); // see https://stackoverflow.com/a/50272974 for why mute=1
		// 	frames[i].src = new_src;
		// 	var finished = setTimeout(function () {
		// 		frames[i].src = updateQueryStringParameter(frames[i].src, 'autoplay', '0');
		// 	}, frames[i].getAttribute('data-duration'));
		// 	active_timeouts.push(finished);
		// }
	};
	var timeout = setTimeout(nextFrame, frameList[i].carousel.dur);
	activeTimeouts.push(timeout);
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