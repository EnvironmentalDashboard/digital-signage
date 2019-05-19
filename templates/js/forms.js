// Submit event to fire when carousel form submitted
var createCarousel = function(e) {
	e.preventDefault();
	var input = $(this).find('input[type="text"]'), submit = $(this).find('button[type="submit"]');
	input.addClass('is-valid');
	submit.html('<div class="spinner-border" role="status" style="height: 1.5rem;width: 1.5rem;margin: 0px 20px;"><span class= "sr-only">Loading...</span></div>');
	// Update carousel list & modals
	$.post($(this).attr('action'), $(this).serialize()).done(function() {
		$.ajax({
			url: '{{ path("carousel-table") }}',
			type: "GET",
			success: function(data) {
				$("#nav-all-carousels").html(data);
				$('form[action="{{ path("carousel-create") }}"]').on('submit', createCarousel); // Re-apply submit event to new forms
				$('.detect-duration').each(function () {
					$(this).on('input', detectDuration);
				});
				input.removeClass('is-valid');
				input.val('');
				submit.html('Create carousel');
				$('#nav-all-carousels-tab').tab('show');
				var new_row = $('#nav-all-carousels > table > tbody > tr:last-child');
				new_row.addClass('table-primary');
				setTimeout(function () { new_row.removeClass('table-primary'); }, 1500);
			},
			error: function(xhr, status, error) {
				console.log(xhr, status, error);
			}
		});
	}).fail(function (xhr, status, error) {
		console.log(xhr, status, error);
	});
};

// Submit event to fire when display form submitted
var createDisplay = function(e) {
	e.preventDefault();
	var input = $(this).find('input[type="text"]'), submit = $(this).find('button[type="submit"]');
	input.addClass('is-valid');
	submit.html('<div class="spinner-border" role="status" style="height: 1.5rem;width: 1.5rem;margin: 0px 20px;"><span class= "sr-only">Loading...</span></div>');
	// Update display list & modals
	$.post($(this).attr('action'), $(this).serialize()).done(function() {
		$.ajax({
			url: '{{ path("display-table") }}',
			type: "GET",
			success: function(data) {
				$("#nav-all-displays").html(data);
				$('form[action="{{ path("display-create") }}"]').on('submit', createDisplay); // Re-apply submit event to new forms
				input.removeClass('is-valid');
				input.val('');
				submit.html('Create display');
				$('#nav-all-displays-tab').tab('show');
				var new_row = $('#nav-all-displays > table > tbody > tr:last-child');
				new_row.addClass('table-primary');
				setTimeout(function() { new_row.removeClass('table-primary'); }, 1500);
			},
			error: function(xhr, status, error) {
				console.log(xhr, status, error);
			}
		});
	}).fail(function(xhr, status, error) {
		console.log(xhr, status, error);
	});
};

var savePresentation = function(e) {
	e.preventDefault();
	var modal = $(this).closest('.modal');
	modal.modal('hide');
	$.post($(this).attr('action'), $(this).serialize()).done(function() {
		$.ajax({
			url: '{{ path("display-table") }}',
			type: "GET",
			success: function(data) {
				$("#nav-all-displays").html(data);
				$('form[action="{{ path("display-create") }}"]').on('submit', createDisplay);
				$('form[action$="/presentations/save"]').on('submit', savePresentation);
			},
			error: function(xhr, status, error) {
				console.log(xhr, status, error);
			}
		});
	}).fail(function(xhr, status, error) {
		console.log(xhr, status, error);
	});
};

var saveFrame = function(e) {
	e.preventDefault();
	var modal = $(this).closest('.modal');
	modal.modal('hide');
	$.post($(this).attr('action'), $(this).serialize()).done(function() {
		$.ajax({
			url: '{{ path("carousel-table") }}',
			type: "GET",
			success: function(data) {
				$("#nav-all-carousels").html(data);
				$('form[action="{{ path("carousel-create") }}"]').on('submit', createCarousel);
				$('form[action$="/frames/save"]').on('submit', saveFrame);
				$('.detect-duration').each(function() {
					$(this).on('input', detectDuration);
				});
			},
			error: function(xhr, status, error) {
				console.log(xhr, status, error);
			}
		});
	}).fail(function(xhr, status, error) {
		console.log(xhr, status, error);
	});
};

$('form[action="{{ path("carousel-create") }}"]').on('submit', createCarousel);
$('form[action="{{ path("display-create") }}"]').on('submit', createDisplay);
$('form[action$="/presentations/save"]').on('submit', savePresentation);
$('form[action$="/frames/save"]').on('submit', saveFrame);
$('.detect-duration').each(function() {
	$(this).on('input', detectDuration);
});

// Add new frame
$(".carousel-add-new-frame").each(function(){
	$(this).on('click', function(e) {
		var carousel = $(this).data('carousel-for');
		var clone = $('#new-frame' + carousel).clone();
		randomize_ids(clone);
		clone[0].firstElementChild.innerHTML = 'New frame';
		clone.find("input[type='hidden']").val('newframe' + carousel);
		$("#carousel-" + carousel + "-frame-edit").append(clone);
	});
});

// Add new presentation
$(".display-add-new-pres").each(function(){
	$(this).on('click', function(e) {
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
	});
});

// Swap carousel placeholder when option selected
/* {% spaceless %} */
var templates = [ '{{ render(controller("App\\Controller\\Display::template",{"name": "fullscreen"})) }}',
									'{{ render(controller("App\\Controller\\Display::template", {"name": "marquee"})) }}' ];
/* {% endspaceless %} */

$(".template-select-dropdown").each(function(){
	$(this).on('change', dropdown_template);
});
function dropdown_template(e) {
	e.preventDefault();
	var controls = $(this).data('controls');
	var selected = $(this).find(":selected").val();
	$(controls).html(templates[selected - 1]);
}

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
				$(descendants[j]).on('change', dropdown_template);
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