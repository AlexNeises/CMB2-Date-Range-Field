window.cmb2DateRange = window.cmb2DateRange || {};

(function(window, document, $, app, undefined){
	'use strict';

	app.init = function() {

		var $body = $( 'body' );

		$( '[data-timerange]' ).each( function() {

			var $this = $( this );
			var data = $this.data( 'daterange' );
			$('#' + $this.parent().attr('id') + ' > .time').timepicker({
				'showDuration': true,
				'timeFormat': 'g:i A'
			});
			$('#' + $this.parent().attr('id')).datepair();

			$body.trigger( 'cmb2_timerange_init', { '$el' : $this } );
		});

		// $( '.cmb-type-date-range .comiseo-daterangepicker-triggerbutton' ).addClass( 'button-secondary' ).removeClass( 'comiseo-daterangepicker-top comiseo-daterangepicker-vfit' );
		// $( '.comiseo-daterangepicker' ).addClass( 'cmb2-element' );

	};

	$( app.init );

})(window, document, jQuery, window.cmb2DateRange);