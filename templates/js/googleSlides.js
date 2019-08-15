//
function specialUrl() {
	var input = $(this);
	var originalId = input.attr('id');
	var url = input.val();
	var id = getYoutubeId(url);
	if (id !== false) {
		var duration_input_id = originalId.replace('create_frame_url_', 'create_frame_duration_');
		$.getJSON("{{ url('index') }}youtube/" + id + "/length", function(length) {
			$('#' + duration_input_id).val(length);
		});
	}
	id = getSlidesId(url);
	if (id !== false) { // fetch google slides durations in presenter notes if given such a url
		if (confirm('We have detected a Google Slides URL. Would you like to read the presenter notes to create a remote controller, set frame URLs, and set frame durations? You will need to authenticate with Google.')) {
			$('#' + originalId.replace('url', 'duration')).attr('id', 'dur' + id);
			input.attr('id', id); // give input a useful id to find later
			var tag = document.createElement('script');
			tag.src = "https://apis.google.com/js/api.js";
			tag.onload = function() { handleClientLoad() };
			window.google_slide = id;
			var firstScriptTag = document.getElementsByTagName('script')[0];
			firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
		}
	}
}

function getSlidesId(url) {
	if (url != undefined || url != '') {
		var regExp = /docs\.google\.com[\/]presentation[\/]d[\/](.*?)[\/]edit/;
		var match = url.match(regExp);
		if (match && match[1].length == 44) {
			return match[1];
		}
	}
	return false;
}

// see https://developers.google.com/slides/quickstart/javascript
// Client ID and API key from the Developer Console
var CLIENT_ID = '20652970958-jm3pne4rhiof2jdafbgd159ld41a9l89.apps.googleusercontent.com';
var API_KEY = 'AIzaSyDCd-YZa4SAf_VU_9kjR-bCWA_YcnNu7jU';

// Array of API discovery doc URLs for APIs used by the quickstart
var DISCOVERY_DOCS = ["https://slides.googleapis.com/$discovery/rest?version=v1"];

// Authorization scopes required by the API; multiple scopes can be
// included, separated by spaces.
var SCOPES = "https://www.googleapis.com/auth/presentations.readonly";

/**
	*  On load, called to load the auth2 library and API client library.
	*/
function handleClientLoad() {
	gapi.load('client:auth2', initClient);
}

/**
	*  Initializes the API client library and sets up sign-in state
	*  listeners.
	*/
function initClient() {
	gapi.client.init({
		apiKey: API_KEY,
		clientId: CLIENT_ID,
		discoveryDocs: DISCOVERY_DOCS,
		scope: SCOPES
	}).then(function () {
		gapi.auth2.getAuthInstance().signIn();
		getPresenterNotes();
	}, function (error) {
		console.log(error);
	});
}

/**
	* Fetch presenter notes
	*/
function getPresenterNotes() {
	gapi.client.slides.presentations.get({
		presentationId: window.google_slide
	}).then(function (response) {
		var presentation = response.result;
		var notes = [];
		for (var i = 0; i < presentation.slides.length; i++) {
			var slide = presentation.slides[i];
			var note_id = slide.slideProperties.notesPage.notesProperties.speakerNotesObjectId;
			var full_note = '';
			for (var j = 0; j < slide.slideProperties.notesPage.pageElements.length; j++) {
				var page_elements = slide.slideProperties.notesPage.pageElements[j];
				if (page_elements.shape.text) {
					for (var k = 0; k < page_elements.shape.text.textElements.length; k++) {
						if (page_elements.shape.text.textElements[k].textRun) {
							full_note += page_elements.shape.text.textElements[k].textRun.content;
						}
					}
				}
			}
			notes.push(full_note);
		}
		save_slide_data(notes, window.google_slide);
	}, function (response) {
		console.log(response.result.error.message);
	});
}

function save_slide_data(arr, id) {
	// console.log('notes', arr);
	var frameInput = $('#' + id);
	$.post("{{ path('index') }}google-slides/" + id + "/save", { notes: JSON.stringify(arr), carousel: frameInput.data('carousel-for') }, function(data, textStatus) {
		for (var i = 0; i < data.length; i++) {
			var el = newFrame({target: frameInput});
			el.find("input[type='hidden']").val(data[i].frame);
			el.find("input[type='number']").val(data[i].dur);
			el.find("input[type='url']").val(data[i].url);
		}
		frameInput.parent().parent().remove();
		// todo: reload controller table to show newly created controller
	}, "json");
}