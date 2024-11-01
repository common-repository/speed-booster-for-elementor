(function($){
	$(document).ready(function() {
		var list = $('#adminmenu li:not(.toplevel_page_speed-booster-for-elementor) > a'),
			bar  = $('#wpadminbar a[href^="https:"], #wpadminbar a[href^="http:"]');

		
		list.each(function() {
			var $this = $(this);

			$this.on('click', function(e) {
				if ( window.WPPOOLSBE.saveChanges ) {
					e.preventDefault();
					WPPOOLSBE.setSaveChangesAlert(true);
					window.WPPOOLSBE.setRedirectURL($this.prop('href'));
				}
			});
		});


		bar.each(function() {
			var $this = $(this);

			$this.on('click', function(e) {
				if ( window.WPPOOLSBE.saveChanges ) {
					e.preventDefault();
					WPPOOLSBE.setSaveChangesAlert(true);
					window.WPPOOLSBE.setRedirectURL($this.prop('href'));
				}
			});
		})
	})
})(jQuery);