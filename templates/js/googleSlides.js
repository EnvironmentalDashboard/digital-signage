// Fetch google slides durations in presenter notes if given such a url
function detectDuration() {
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
	if (id !== false) {
		if (confirm('We have detected a Google Slides URL. Would you like to read the presenter notes to set slide durations? You will need to authenticate with Google.')) {
			$.post("{{ path('index') }}google-slides/" + id + "/exists", function(res) {
				if (res === false || confirm('Presenter notes for these slides have already been imported. Would you like to delete existing durations and re-import?')) {
					$('#' + originalId.replace('url', 'duration')).attr('id', 'dur' + id);
					input.attr('id', id); // give input a useful id to find later
					var tag = document.createElement('script');
					tag.src = "https://apis.google.com/js/api.js";
					tag.onload = function() { handleClientLoad() };
					window.google_slide = id;
					var firstScriptTag = document.getElementsByTagName('script')[0];
					firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
				}
			}, "json");
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
		save_durations(notes, window.google_slide);
	}, function (response) {
		console.log(response.result.error.message);
	});
}

function save_durations(arr, id) {
	// console.log('notes', arr);
	$.post("{{ path('index') }}google-slides/" + id + "/save", { durations: JSON.stringify(arr) }, function(data, textStatus) {
		// console.log(data);
		var frameInput = $('#' + id);
		var durInput = $('#dur' + id);
		frameInput.addClass('is-valid');
		var sum = data.reduce(function(a, b) { return a + b; }, 0);
		durInput.val(Math.round(sum/1000));
		frameInput.after('<small class="form-text text-muted">Slides 1 through '+(data.length)+' will have the durations '+(data.join(', '))+'</small>');
	}, "json");
}