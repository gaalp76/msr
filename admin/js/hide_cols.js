/* ----------------------------------------------------------------------
* File name:		jquery.hide_cols.js
* Description:	xxx
* Website:			generic jQuery plugin
* Version:			1.0
* Date:					28-6-2016
* Author:				Ray Hyde - www.rayhyde.nl
---------------------------------------------------------------------- */

(function ($) {
	
	$.fn.hideCols = function (options) {

		// default settings
		var settings = $.extend({
			hideColumn: '&times;',
			unhideColumn: '<span class="glyphicon glyphicon-eye-open"></span>  Oszlop',
			unhideAll: '<span class="glyphicon glyphicon-eye-open"></span>  Összes oszlop',
			autoSort: true
		}, options);
		
	//translations
	var $table = this,
			$show = $('#show'),
			links = 0;

	// add close divs to each th, wrapped in a div as absolute positioning does not work
	// in a table cell
	$table + $('th')
			.css({
				paddingRight: 0,
				paddingTop: 0		
			})
			.prepend('<div class="closeWrap"><div class="hide-col">' + settings.hideColumn + '</div>')
			.append('</div>')
		;
		if ( settings.autoSort == false) {
			$show.append('<a href="" class="sort btn btn-sm btn-default">Sort</a>');
		}
		function sortdivs() {
			var listitems = $show.find('div').get();
			listitems.sort(function(a, b) {
				 var compA = $(a).data('show');
				 var compB = $(b).data('show');
				 return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
			})
			$.each(listitems, function(idx, itm) { $show.append(itm); });
		}
	//this happens when a close div is clicked:
	$table + $('th .hide-col').click(function() {
		

		$show.find('.sort').show();
		//hides the th of the column that is clicked
		var col = $(this).parent().parent('th').index();
		$(this).parent().parent('th').hide();

		//hides the td of the column that is clicked in each row
		$table + $('tr').each(function() { 
			$('td:eq(' + col + ')',this).hide();
		});

		//check if the link to show all columns already exists, if not then add one
		if($show.find('div').length == 0) {
			$show.append('<div data-show="0" class="btn btn-sm btn-warning unhideAll">' + settings.unhideAll + '</div>');
		}

		//adds a link to again show a single hidden column
		$show.append('<div data-show="' + col + '" class="btn btn-sm btn-primary">' + $(this).parent().parent().find('.odt-col').text() + '</div>');

		links++;
		if (settings.autoSort == true ) {			
			sortdivs();
		}
		return false;
	});
		
	//this happens when a link to show one or all columns is clicked:
	$show.on('click','div', function(event){

		if ($(this).hasClass('unhideAll')) {
			//when the link to show all columns is clicked
			$show.children('div').remove(); //remove all show columns links
			$show.find('.sort').hide(); //hide sort div
			this + $('td, th').show(); //show all hidden cells
		} else {
			//gets the number of the columns to be shown
			var col = $(this).data('show');

		//displays the td and th of the column that is clicked in each row
		$table + $('tr').each(function() { 
			$('th:eq(' + col + '),td:eq(' + col + ')',this).fadeIn('slow');
		});

		links--;
		//remove unhideAll when there are no more individual show links
		if (links == 0) {
			$show.children().remove(); //remove all show columns links
		}				
		//remove this show link
		$(this).next('br').remove();
		$(this).remove();
		}
	});	
		
		$show.on('click', '.sort', function() {
			sortdivs();
			return false;
		});
	}
}(jQuery));