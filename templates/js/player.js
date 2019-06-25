// console.log(presentations);
activeTimeouts = [];
showPresentation(Object.keys(presentations)[0]);

function showPresentation(presentationId, targetFrame = null) {
	// console.log(presentationId, targetFrame);
	var presentationElement = document.getElementById('pres' + presentationId);
	presentationElement.style.display = '';
	animateCarousels(presentations[presentationId].carousels, 'pres' + presentationId, targetFrame);
	var timeout = setTimeout(function() {
		clearTimeouts();
		clearCurrentPres(presentationId);
		showPresentation(presentations[presentationId].next);
	}, presentations[presentationId].duration);
	activeTimeouts.push(timeout);
}

function animateCarousels(carousels, presentationId, targetFrame = null) {
	var loadFrame = function(frameList, frameId, showIn, targetElement, loadingElement) {
		for (var curFrame = 0; curFrame < frameList.length; curFrame++) {
			if (frameList[curFrame].id === frameId) {
				break;
			}
		}
		loadingElement.classList = 'fade-out';
		loadingElement.src = frameList[curFrame].url;
		targetElement.classList = 'fade-in';
		var timeout = setTimeout(function() {
			loadFrame(frameList, frameList[curFrame].next, frameList[curFrame].dur, loadingElement, targetElement);
		}, showIn);
		activeTimeouts.push(timeout);
	}
	
	for (var carouselId in carousels) {
		if (carousels.hasOwnProperty(carouselId)) {
			var frameList = carousels[carouselId];
			var targetElement = document.getElementById(presentationId + '-' + carouselId + '-primary');
			var loadingElement = document.getElementById(presentationId + '-' + carouselId + '-secondary');
			var curFrame;
			if (targetFrame !== null) {
				for (curFrame = 0; curFrame < frameList.length; curFrame++) {
					if (frameList[curFrame].id === targetFrame) {
						break;
					}
				}
			} else {
				curFrame = 0;
			}

			targetElement.src = frameList[curFrame].url;
			if (frameList.length > 1) {
				loadFrame(frameList, frameList[curFrame].next, frameList[curFrame].dur, targetElement, loadingElement);
			}
		}
	}
}



function clearTimeouts() {
	while (activeTimeouts.length > 0) {
		var timeout = activeTimeouts.pop();
		clearTimeout(timeout);
	}
}

function clearCurrentPres(presentationId) {
	var presentation = document.getElementById('pres' + presentationId);
	var carousels = presentation.children;
	for (var i = 0; i < carousels.length; i++) {
		var frameList = carousels[i].children;
		for (let j = 0; j < frameList.length; j++) {
			frameList[j].src = 'about:blank';
		}
	}
	presentation.style.display = 'none';
}
