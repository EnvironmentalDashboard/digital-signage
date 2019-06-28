// console.log(presentations);
activeTimeouts = [];
showPresentation(Object.keys(presentations)[0]);

function showPresentation(presentationId, targetFrame = null) {
	// console.log(presentationId, targetFrame);
	clearTimeouts();
	clearPresentations();
	var presentationElement = document.getElementById('pres' + presentationId);
	presentationElement.style.display = '';
	animateCarousels(presentations[presentationId].carousels, 'pres' + presentationId, targetFrame);
	var timeout = setTimeout(function() {
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
	
	var thisIteration = false;
	for (var carouselId in carousels) {
		if (carousels.hasOwnProperty(carouselId)) {
			var frameList = carousels[carouselId];
			var targetElement = document.getElementById(presentationId + '-' + carouselId + '-primary');
			var loadingElement = document.getElementById(presentationId + '-' + carouselId + '-secondary');
			var curFrame;
			if (targetFrame !== null) {
				for (curFrame = 0; curFrame < frameList.length; curFrame++) {
					if (frameList[curFrame].id === targetFrame) {
						thisIteration = true;
						break;
					}
				}
				if (!thisIteration) {
					// continue;
					curFrame = 0;
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

function clearPresentations() {
	var presentationIds = Object.keys(presentations);
	for (var i = 0; i < presentationIds.length; i++) {
		var presentationId = presentationIds[i];
		var presentation = document.getElementById('pres' + presentationId);
		var carousels = presentation.children;
		for (var j = 0; j < carousels.length; j++) {
			var frameList = carousels[j].children;
			for (var k = 0; k < frameList.length; k++) {
				frameList[k].src = 'about:blank';
			}
		}
		presentation.style.display = 'none';
	}
}
