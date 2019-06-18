var mainProgressBar = function () {
	var myXhr = $.ajaxSettings.xhr();
	var progress = $('main-progress');
	console.log('a');
	if (myXhr.upload) {
		progress.removeClass('d-none');
		myXhr.upload.addEventListener('progress', function (e) {
			if (e.lengthComputable) {
				var pct = (e.loaded/e.total) * 100;
				progress.attr('aria-valuenow', pct);
				progress.css('width', pct + '%');
				if (pct === 100) {
					setTimeout(function() {progress.addClass('d-none');}, 500);
				}
			}
		}, false);
	}
	return myXhr;
};
// Submit event to fire when carousel form submitted
var createCarousel = function (e) {
	e.preventDefault();
	var input = $(this).find('input[type="text"]'), submit = $(this).find('button[type="submit"]');
	input.addClass('is-valid');
	submit.html('<div class="spinner-border" role="status" style="height: 1.5rem;width: 1.5rem;margin: 0px 20px;"><span class="sr-only">Loading...</span></div>');
	// Update carousel list & modals
	$.post($(this).attr('action'), $(this).serialize()).done(function () {
		$.ajax({
			url: '{{ path("carousel-table") }}',
			type: "GET",
			success: function (data) {
				$("#nav-all-carousels").html(data);
				$('form[action="{{ path("carousel-create") }}"]').on('submit', createCarousel); // Re-apply submit event to new forms
				$('.detect-duration').each(function () {
					$(this).on('input', detectDuration);
				});
				$(".carousel-add-new-frame").each(function () {
					$(this).on('click', newFrame);
				});
				$('form[action$="/frames/save"]').on('submit', saveFrame);
				input.removeClass('is-valid');
				input.val('');
				submit.html('Create carousel');
				$('#nav-all-carousels-tab').tab('show');
				var new_row = $('#nav-all-carousels > table > tbody > tr:last-child');
				new_row.addClass('table-primary');
				setTimeout(function () { new_row.removeClass('table-primary'); }, 1500);
			},
			error: function (xhr, status, error) {
				console.log(xhr, status, error);
			},
			xhr: mainProgressBar
		});
	}).fail(function (xhr, status, error) {
		console.log(xhr, status, error);
	});
};

// Submit event to fire when display form submitted
var createDisplay = function (e) {
	e.preventDefault();
	var input = $(this).find('input[type="text"]'), submit = $(this).find('button[type="submit"]');
	input.addClass('is-valid');
	submit.html('<div class="spinner-border" role="status" style="height: 1.5rem;width: 1.5rem;margin: 0px 20px;"><span class="sr-only">Loading...</span></div>');
	// Update display list & modals
	$.post($(this).attr('action'), $(this).serialize()).done(function () {
		$.ajax({
			url: '{{ path("display-table") }}',
			type: "GET",
			success: function (data) {
				$("#nav-all-displays").html(data);
				$('form[action="{{ path("display-create") }}"]').on('submit', createDisplay); // Re-apply submit event to new forms
				input.removeClass('is-valid');
				input.val('');
				submit.html('Create display');
				$('#nav-all-displays-tab').tab('show');
				var new_row = $('#nav-all-displays > table > tbody > tr:last-child');
				new_row.addClass('table-primary');
				setTimeout(function () { new_row.removeClass('table-primary'); }, 1500);
			},
			error: function (xhr, status, error) {
				console.log(xhr, status, error);
			},
			xhr: mainProgressBar
		});
	}).fail(function (xhr, status, error) {
		console.log(xhr, status, error);
	});
};

// Submit event to fire when create remote controller form submitted
var createController = function (e) {
	e.preventDefault();
	var input = $(this).find('input[type="text"]'), submit = $(this).find('button[type="submit"]');
	input.addClass('is-valid');
	submit.html('<div class="spinner-border" role="status" style="height: 1.5rem;width: 1.5rem;margin: 0px 20px;"><span class="sr-only">Loading...</span></div>');
	// Update display list & modals
	$.post($(this).attr('action'), $(this).serialize()).done(function () {
		$.ajax({
			url: '{{ path("controller-table") }}',
			type: "GET",
			success: function (data) {
				$("#nav-all-controllers").html(data);
				$('form[action="{{ path("controller-create") }}"]').on('submit', createController); // Re-apply submit event to new forms
				input.removeClass('is-valid');
				input.val('');
				submit.html('Create remote controller');
				$('#nav-all-controllers-tab').tab('show');
				var new_row = $('#nav-all-controllers > table > tbody > tr:last-child');
				new_row.addClass('table-primary');
				setTimeout(function () { new_row.removeClass('table-primary'); }, 1500);
			},
			error: function (xhr, status, error) {
				console.log(xhr, status, error);
			},
			xhr: mainProgressBar
		});
	}).fail(function (xhr, status, error) {
		console.log(xhr, status, error);
	});
};

var savePresentation = function (e) {
	e.preventDefault();
	var modal = $(this).closest('.modal');
	modal.modal('hide');
	$.post($(this).attr('action'), $(this).serialize()).done(function () {
		$.ajax({
			url: '{{ path("display-table") }}',
			type: "GET",
			success: function (data) {
				$("#nav-all-displays").html(data);
				$('form[action="{{ path("display-create") }}"]').on('submit', createDisplay);
				$('form[action$="/presentations/save"]').on('submit', savePresentation);
			},
			error: function (xhr, status, error) {
				console.log(xhr, status, error);
			},
			xhr: mainProgressBar
		});
	}).fail(function (xhr, status, error) {
		console.log(xhr, status, error);
	});
};

var saveFrame = function (e) {
	e.preventDefault();
	var modal = $(this).closest('.modal');
	modal.modal('hide');
	$.post($(this).attr('action'), $(this).serialize()).done(function () {
		$.ajax({
			url: '{{ path("carousel-table") }}',
			type: "GET",
			success: function (data) {
				$("#nav-all-carousels").html(data);
				$('form[action="{{ path("carousel-create") }}"]').on('submit', createCarousel);
				$('form[action$="/frames/save"]').on('submit', saveFrame);
				$('.detect-duration').each(function () {
					$(this).on('input', detectDuration);
				});
			},
			error: function (xhr, status, error) {
				console.log(xhr, status, error);
			},
			xhr: mainProgressBar
		});
	}).fail(function (xhr, status, error) {
		console.log(xhr, status, error);
	});
};

var newFrame = function (e) {
	var carousel = $(this).data('carousel-for');
	var clone = $('#new-frame' + carousel).clone();
	randomize_ids(clone);
	clone[0].firstElementChild.innerHTML = 'New frame';
	clone.find("input[type='hidden']").val('newframe' + carousel);
	clone.find("input[type='number']").val('');
	var url = clone.find("input[type='url']");
	url.val('');
	url.removeClass('is-valid');
	url.on('input', detectDuration);
	$("#carousel-" + carousel + "-frame-edit").append(clone);
}

var newPresentation = function (e) {
	e.preventDefault();
	var display = $(this).data('display');
	var clone = $('#new-pres' + display).clone();
	randomize_ids(clone);
	clone[0].firstElementChild.innerHTML = 'New presentation';
	var drop_zone = clone.find('.drop-zone'), select_drop_zone = clone.find('select'), fa_input = clone.find('.hidden-frame-arrangement'), id_input = clone.find('.hidden-id');
	var new_pres_id = guidGenerator();
	var new_dropzone_id = guidGenerator();
	select_drop_zone.attr('data-controls', '#' + new_dropzone_id);
	drop_zone.attr('data-pres', new_pres_id);
	drop_zone.attr('id', new_dropzone_id);
	fa_input.attr('id', 'frame-arrangement-' + new_pres_id);
	id_input.val('new');
	var custom_option = select_drop_zone.children().last();
	if (custom_option.text() === 'Custom') {
		custom_option.remove();
	}
	// console.log(clone.html());
	$("#display-" + display + "-presentation-edit").append(clone);
}

var newButton = function (ev, el) {
	ev.preventDefault();
	var $el = $(el);
	var controllerId = $el.find('input[name="controllerId"]').val();
	var target = $('#buttonList' + controllerId);
	var progressBar = $('#progress-bar' + controllerId);
	var progressBarContainer = progressBar.parent();
	progressBarContainer.css('display', '');
	$.ajax({
		// Your server script to process the upload
		url: $el.attr('action'),
		type: 'POST',

		// Form data
		data: new FormData(el),

		// Tell jQuery not to process data or worry about content-type
		// You *must* include these options!
		cache: false,
		contentType: false,
		processData: false,

		success: function(res) {
			console.log(res);
			$.ajax({
				url: '{{ path("button-list-all") }}',
				type: "GET",
				success: function(data) {
					target.html(data);
					setTimeout(function() {
						progressBarContainer.css('display', 'none');	
					}, 2000);
				},
				error: function(xhr, status, error) {
					console.log(xhr, status, error);
				}
			});
		},

		// Custom XMLHttpRequest
		xhr: function () {
			var myXhr = $.ajaxSettings.xhr();
			// var progress = $('progress');
			if (myXhr.upload) {
				// For handling the progress of the upload
				myXhr.upload.addEventListener('progress', function (e) {
					if (e.lengthComputable) {
						var pct = (e.loaded/e.total) * 100;
						progressBar.attr('aria-valuenow', pct);
						progressBar.css('width', pct + '%');
					}
				}, false);
			}
			return myXhr;
		}
	});
};

var saveButton = function (e) {
	e.preventDefault();
	var modal = $(this).closest('.modal');
	modal.modal('hide');
	$.post($(this).attr('action'), $(this).serialize()).done(function () {
		$.ajax({
			url: '{{ path("controller-table") }}',
			type: "GET",
			success: function (data) {
				$("#nav-all-controllers").html(data);
				$('form[action="{{ path("controller-create") }}"]').on('submit', createController);
				$('form[action$="/frames/save"]').on('submit', saveFrame);
			},
			error: function (xhr, status, error) {
				console.log(xhr, status, error);
			},
			xhr: mainProgressBar
		});
	}).fail(function (xhr, status, error) {
		console.log(xhr, status, error);
	});
};

var loadFrames = function (e) {
	var select = $(e);
	var target = $(select.data('target'));
	var id = select.val();
	$.ajax({
		url: '/digital-signage/display/' + id + '/frame/all',
		type: "GET",
		success: function (data) {
			var markup = '<label for="' + id + '">Select frame</label><select class="form-control" id="' + id + '" name="buttonFrameSelect">';
			for (var i = 0; i < data.length; i++) {
				var frame = data[i];
				var id = guidGenerator();
				markup += '<option value="' + frame.id + '">' + frame.url + '</option>';
			}
			markup += '</select>';
			target.html(markup);
		},
		error: function (xhr, status, error) {
			console.log(xhr, status, error);
		},
		xhr: mainProgressBar
	});
};

var displayDropdownTemplate = function (e) {
	e.preventDefault();
	var controls = $(this).data('controls');
	var selected = $(this).find(":selected").val();
	$(controls).html(displayTemplates[selected - 1]);
}

var controllerDropdownTemplate = function (e) {
	e.preventDefault();
	var controls = $(this).data('controls');
	var selected = $(this).find(":selected").val();
	$(controls).html(controllerTemplates[selected - 1]);
}


$('form[action="{{ path("carousel-create") }}"]').on('submit', createCarousel);
$('form[action="{{ path("display-create") }}"]').on('submit', createDisplay);
$('form[action="{{ path("controller-create") }}"]').on('submit', createController);
$('form[action$="/presentations/save"]').on('submit', savePresentation);
$('form[action$="/frames/save"]').on('submit', saveFrame);
$('form[action$="/buttons/save"]').on('submit', saveButton);
$('.detect-duration').each(function () {
	$(this).on('input', detectDuration);
});
$(".carousel-add-new-frame").each(function () {
	$(this).on('click', newFrame);
});
$(".display-add-new-pres").each(function () {
	$(this).on('click', newPresentation);
});

// Swap carousel placeholder when option selected
/* {% spaceless %} */
var displayTemplates = ['{{ render(controller("App\\Controller\\Display::template",{"name": "fullscreen"})) }}',
	'{{ render(controller("App\\Controller\\Display::template", {"name": "marquee"})) }}'];
var controllerTemplates = ['{{ render(controller("App\\Controller\\RemoteController::template", {"name": "2 Buttons"})) }}',
						'{{ render(controller("App\\Controller\\RemoteController::template", {"name": "4 Buttons"})) }}',
						'{{ render(controller("App\\Controller\\RemoteController::template", {"name": "8 Buttons"})) }}'	];
/* {% endspaceless %} */

$(".template-select-dropdown").each(function () {
	$(this).on('change', displayDropdownTemplate);
});
$(".controller-select-dropdown").each(function () {
	$(this).on('change', controllerDropdownTemplate);
});

$(function () {
	var popovers = $('[data-toggle="popover"]');
	popovers.popover({ html: true });
	popovers.on('shown.bs.popover', function (e) {
		var id = $(e.target).attr('data-displayList');
		loadFrames(document.getElementById(id));
	});
});

// utilities
function randomize_ids(container_div) {
	var modal_body = container_div[0];
	var children = modal_body.children;
	for (var i = 0; i < children.length; i++) {
		var descendants = children[i].children;
		var cur_id = guidGenerator();
		for (var j = 0; j < descendants.length; j++) {
			if (descendants[j].tagName === 'LABEL') {
				descendants[j].setAttribute('for', cur_id);
			} else if (descendants[j].tagName === 'INPUT') {
				descendants[j].setAttribute('id', cur_id);
			} else if (descendants[j].tagName === 'SELECT') {
				descendants[j].setAttribute('id', cur_id);
				$(descendants[j]).on('change', displayDropdownTemplate);
			}
		}

	}
}
function guidGenerator() {
	var S4 = function () {
		return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
	};
	return (S4() + S4() + "-" + S4() + "-" + S4() + "-" + S4() + "-" + S4() + S4() + S4());
}