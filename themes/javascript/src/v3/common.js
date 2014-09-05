$(document).ready(function(){

	// ==============================
	// open links in NEW window / tab
	// ==============================

		// listen for clicks on anchor tags
		// that include rel="external" attributes
		$('a[rel="external"]').on('click',function(e){
			// open a new window pointing to
			// the href attribute of THIS anchor click
			window.open(this.href);
			// stop THIS href from loading
			// in the source window
			e.preventDefault();
		});

	// ===============
	// scroll smoothly
	// ===============

		// listen for clicks on elements with a class of scroll
		$('.scroll').on('click',function(){
			// animate the window scroll to
			// #top for 800 milliseconds
			$('#top').animate({ scrollTop: 0 }, 800);
			// stop #top from reloading
			// the source window and appending to the URI
			return false;
		});

	// ============
	// scroll wraps
	// ============

		// look for each scroll-wrap within a setting field
		$('.setting-field .scroll-wrap').each(function(){
			// determine the height of this scroll-wrap.
			var scrollHeightIs = $(this).height();

			// if it's greater than or equal to 200,
			if(scrollHeightIs >= '200'){
				// pop a pr class on it.
				$(this).addClass('pr');
			}
		});

		// look for each tbl-wrap
		$('.tbl-wrap').each(function(){
			// determine the width of this tbl-wrap.
			var scrollWidthIs = $(this).width();
			// determine the width of the table inside this tbl-wrap.
			var tblWidthIs = $(this).children('table').width();

			// if tbl-wrap's width less than the table's width,
			if(scrollWidthIs < tblWidthIs){
				// pop a pb class on it.
				$(this).addClass('pb');
			}
		});

	// =========
	// sub menus
	// =========

		// listen for clicks on elements with a class of has-sub
		$('.has-sub').on('click',function(){
			// close OTHER open sub menus
			// when clicking THIS sub menu trigger
			// thanks me :D
			$('.open').not(this)
				// remove the class of open
				.removeClass('open')
				// hide all siblings of open with a class of sub-menu
				.siblings('.sub-menu').hide();

			// toggles THIS sub menu
			// thanks pascal
			$(this)
				// toggle of siblings of THIS
				// with a class of sub-menu
				.siblings('.sub-menu').toggle()
				// go back to THIS and...
				.end()
				// toggle a class of open on THIS
				.toggleClass('open');
			// stop THIS from reloading
			// the source window and appending to the URI
			// and stop propagation up to document
			return false;
		});

		// listen for clicks to the document
		$(document).on('click',function(e){
			// check to see if we are inside a sub-menu or not.
			if(!$(e.target).closest('.sub-menu, .date-picker-wrap').length){
				// close OTHER open sub menus
				// when clicking outside ANY sub menu trigger
				// thanks me :D
				$('.open')
					// remove the class of open
					.removeClass('open')
					// hide all siblings of open with a class of sub-menu
					.siblings('.sub-menu').hide();
			}
		});

	// ====
	// tabs
	// ====

		// listen for clicks on tabs
		$('.tab-bar ul a').on('click',function(){
			// set the tabClassIs variable
			// tells us which .tab to control
			var tabClassIs = $(this).attr('rel');

			// close OTHER .tab(s), ignores the currently open tab
			$('.tab-bar ul a').not(this).removeClass('act');
			// removes the .tab-open class from any open tabs, and hides them
			$('.tab').not('.tab.'+tabClassIs+'.tab-open').removeClass('tab-open');

			// add a class of .act to THIS tab
			$(this).addClass('act');
			// add a class of .open to the proper .tab
			$('.tab.'+tabClassIs).addClass('tab-open');
			// stop THIS from reloading
			// the source window and appending to the URI
			// and stop propagation up to document
			return false;
		});

	// ==============
	// version pop up
	// ==============

		// hide version-info box
		$('.version-info').hide();

		// listen for clicks to elements with a class of version
		$('.version').on('click',function(e){
			// show version-info box
			$('.version-info').show();
			// stop THIS href from loading
			// in the source window
			e.preventDefault();
		});

		// listen for clicks to elements with a class of close inside of version-info
		$('.version-info .close').on('click',function(){
			// hide version-info box
			$('.version-info').hide();
			// stop THIS from reloading
			// the source window and appending to the URI
			// and stop propagation up to document
			return false;
		});

	// ====================
	// modal windows -> WIP
	// ====================

		// hide overlay and any modals, so that fadeIn works right
		$('.overlay, .modal-wrap').hide();

		// prevent modals from popping when disabled
		$('body').on('click','.disable',function(){
			// stop THIS href from loading
			// in the source window
			return false;
		});

		// listen for clicks to elements with a class of m-link
		$('.m-link').on('click',function(e){
			// set the heightIs variable
			// this allows the overlay to be scrolled
			var heightIs = $(document).height();
			// set the modalIs variable
			var modalIs = $(this).attr('rel');

			// fade in the overlay
			$('.overlay').fadeIn('slow').css('height',heightIs);
			// fade in modal
			$('.'+modalIs).fadeIn('slow');
			// stop THIS href from loading
			// in the source window
			e.preventDefault();
			// scroll up, if needed
			$('#top').animate({ scrollTop: 0 }, 100);
		});

		// listen for clicks on the element with a class of overlay
		$('.m-close').on('click',function(e){
			// fade out the overlay
			$('.overlay').fadeOut('slow');
			// fade out the modal
			$('.modal-wrap').fadeOut('slow');
			// stop THIS from reloading the source window
			e.preventDefault();
		});

	// ==================================
	// highlight checks and radios -> WIP
	// ==================================

		// listen for clicks on inputs within a choice classed label
		$('.choice input').on('click',function(){
			$('.choice input[name="'+$(this).attr('name')+'"]').each(function(index, el) {
				$(this).parents('.choice').toggleClass('chosen', $(this).is(':checked'));
			});
		});

	// ======================
	// grid navigation -> WIP
	// ======================

		// listen for clicks on elements classed with .grid-next
		$('.grid-next').on('click',function(e){
			// animate the scrolling of grid-clip forwards
			// to the next grid-item
			$('.grid-clip').animate({ scrollLeft: '+=310' }, 800);
			// stop page from reloading
			// the source window and appending # to the URI
			e.preventDefault();
		});

		// listen for clicks on elements classed with .grid-back
		$('.grid-back').on('click',function(e){
			// animate the scrolling of grid-clip backwards
			// to the previous grid-item
			$('.grid-clip').animate({ scrollLeft: '-=310' }, 800);
			// stop page from reloading
			// the source window and appending # to the URI
			e.preventDefault();
		});

	// =======================
	// publish collapse -> WIP
	// =======================

		// listen for clicks on .sub-arrows
		$('.setting-txt .sub-arrow').on('click',function(){
			// toggle the .setting-field and .setting-text
			$(this).parents('.setting-txt').siblings('.setting-field').toggle();
			// toggle the instructions
			$(this).parents('h3').siblings('em').toggle();
			// toggle a class of .field-closed on the h3
			$(this).parents('h3').toggleClass('field-closed');
		});

}); // close (document).ready