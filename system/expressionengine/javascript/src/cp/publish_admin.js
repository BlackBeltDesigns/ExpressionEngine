/*jslint browser: true, devel: true, onevar: true, undef: true, nomen: true, eqeqeq: true, plusplus: true, bitwise: true, regexp: true, strict: true, newcap: true, immed: true */
/*global $, jQuery, EE */

"use strict";

// Some of the ui widgets are slow to set up (looking at you, sortable) and we
// don't really need these until the sidebar is shown so to save on yet more
// things happening on document.ready we'll init them when they click on the sidebar link

$("a", "#showToolbarLink").one("click", function() {
	
	// set up resizing of publish fields
	$(".publish_field").resizable({
		handles: "e",
		minHeight: 49,
		stop: function(e){
			var percent_width = Math.round(($(this).width() / $(this).parent().width()) * 10) * 10;
			// minimum of 10%
			if (percent_width < 10) {
				percent_width = 10;
			}
		
			// maximum of 100
			if (percent_width > 99) {
				percent_width = 100;
			}
		
			$(this).css("width", percent_width + "%");
		}
	});

	$("#tools ul li a.field_selector").draggable({
		revert: true,
		zIndex: 33,
		helper: "clone"
	}).click(function() {
		return false;
	});

	var newTabButtons = {},
		addAuthorButtons = {};
	
	newTabButtons[EE.lang.add_tab] = add_publish_tab;

	$("#new_tab_dialog").dialog({
		autoOpen: false,
		resizable: false,
		modal: true,
		position: "center",
		minHeight: "0px",
		buttons: newTabButtons
	});


	$("#add_author_dialog").dialog({
		autoOpen: false,
		resizable: false,
		modal: true,
		position: "center",
		height: 400,
		width: 700,
		buttons: addAuthorButtons
	});
	
	
	$(".add_tab_link").click(function(){
		$("#tab_name").val("");
		$("#add_tab label").text(EE.lang.tab_name+": ");
		$("#new_tab_dialog").dialog("open")
		$("#tab_name").focus();
		setup_tabs()
		return false;
	});
	
	
});

$('.add_author_link').click(function() {
	$.get(EE.BASE+'&C=content_publish&M=build_author_table&channel_id='+EE.publish.channel_id+'&is_ajax=y', function(data) {
		$('#add_authors_dialog_form div').html(data);
		$('#add_author_dialog').find('#add_author_pagination').appendTo('.ui-dialog-buttonpane');
		
		$('#add_author_pagination a').live('click', function() {
			
			$('.ui-dialog-buttonpane #add_author_pagination').remove();
			
			$.get(this.href, function(data) {
				$('#add_authors_dialog_form div').html(data);
				$('#add_author_dialog').find('#add_author_pagination').appendTo('.ui-dialog-buttonpane');
			});

		});
		return false;
	});

});

$("#tab_menu_tabs").sortable({
	tolerance: "intersect",
	items: "li:not(.addTabButton)",
	axis: "x"
});

$("#tools h3 a").toggle(
	function(){
		$(this).parent().next("div").slideUp();
		$(this).toggleClass("closed");
	}, function(){
		$(this).parent().next("div").slideDown();
		$(this).toggleClass("closed");
	}
);


$("a", "#showToolbarLink").toggle(
	function(){
		
		// disable all form elements
		disable_fields(true);

		$(".tab_menu").sortable({
			axis: "x",
			tolerance: "pointer",	// feels easier in this case
			placeholder: "publishTabSortPlaceholder",
			items: "li:not(.addTabButton)"
		});
		
		$("a span", "#showToolbarLink").text(EE.lang.hide_toolbar);
		$("#showToolbarLink").animate({
			marginRight: "210"
		});
		$("#holder").animate({
			marginRight: "196"
		}, function(){
			$("#tools").show();
		});
		$(".publish_field").animate({backgroundPosition: "0px 0px"}, "slow");
		$(".handle").css("display", "block");

		$(".ui-resizable-e").animate({
			marginRight: "0px"
		});
		$(".addTabButton").css("display", "inline");
		
		// Swap the image
		$("#showToolbarImg").hide();
		$("#hideToolbarImg").css("display", "inline");	// .show() uses block
		
	}, function (){

		// enable all form elements
		disable_fields(false);

		$("#tools").hide();
		$(".tab_menu").sortable("destroy");
		$("a span", "#showToolbarLink").text(EE.lang.show_toolbar);
		$("#showToolbarLink").animate({
			marginRight: "20"
		});
		$("#holder").animate({
			marginRight: "10"
		});
		$(".publish_field").animate({backgroundPosition: "-15px 0px"}, "slow");
		$(".handle").css("display", "none");

		$(".ui-resizable-e").animate({
			marginRight: "-10px"
		});
		$(".addTabButton").hide();
		
		// Swap the image
		$("#hideToolbarImg").hide();
		$("#showToolbarImg").css("display", "inline");	// .show() uses block
	}
);

$("#toggle_member_groups_all").toggle(
	function(){
		$("input.toggle_member_groups").each(function() {
			this.checked = true;
		});
	}, function (){
		$("input.toggle_member_groups").each(function() {
			this.checked = false;
		});
	}
);






$(".delete_field").toggle(
	function() {
		var field_id = $(this).attr("id").substring(13),
			field = $("#hold_field_"+field_id);
		
		// If the field is not in the active tab - slideUp has no effect
		if (field.is(":hidden")) {
			field.css("display", "none");
		}
		
		field.slideUp();
		$(this).children().attr("src", EE.THEME_URL+"images/closed_eye.png");
	},
	function() {
		var field_id = $(this).attr("id").substring(13);
		$("#hold_field_"+field_id).slideDown();
		$(this).children().attr("src", EE.THEME_URL+"images/open_eye.png");
	}
);

_delete_tab_hide = function(the_li, tab_to_delete) {

	$(".menu_"+tab_to_delete).parent().fadeOut();	// hide the tab
	$(the_li).fadeOut();							// remove from sidebar
	$("#"+tab_to_delete).fadeOut();					// hide the fields

	// If the tab is selected - move focus to the left
	selected_tab = get_selected_tab();

	if (tab_to_delete == selected_tab) {
		prev = $(".menu_"+selected_tab).parent().prevAll(":visible");
		if (prev.length > 0) {
			prev = prev.attr("id").substr(5);
		}
		else {
			prev = "publish_tab";
		}
		tab_focus(prev);
	}

	// $("#"+tab_to_delete).remove() // remove from DOM

	return false;
}

get_selected_tab = function() {
 return jQuery("#tab_menu_tabs .current").attr('id').substring(5);
}

_delete_tab_reveal = function() {
	tab_to_show = $(this).attr("href").substring(1);
	// $(".menu"+tab_to_show).parent().animate({width:0, margin:0, padding:0, border:0, opacity:0}, "fast");
	$(".menu_"+tab_to_show).parent().fadeIn(); // show the tab
	$(this).children().attr("src", EE.THEME_URL+"images/content_custom_tab_show.gif"); // change icon
	$("#"+tab_to_delete).fadeIn(); // show the fields

	return false;
}

tab_req_check = function(tab_name) {
	var illegal = false;
	var illegal_fields = new Array();
	var required = EE.publish.required_fields;

	$("#"+tab_name).find(".publish_field").each(function() {

		var id = this.id.replace(/hold_field_/, ""),
			i = 0,
			key = "";
				
		for (key in required) {
			if (required[key] == id) {
				illegal = true;
				illegal_fields[i] = id;
				i++;	
            }
		}
	});
		
	if (illegal === true) {
		$.ee_notice(EE.publish.lang.tab_has_req_field + illegal_fields.join(","), {"type" : "error"});
		return true;
	}
	
	return false;
}


function delete_publish_tab()
{
	// Toggle cannot use a namespaced click event so we need to unbind using the
	// function reference instead
	$("#publish_tab_list").unbind("click.tab_delete");
	$("#publish_tab_list").bind("click.tab_delete", function(evt) {
	
		if (evt.target !== this) {
	    	var the_li = $(evt.target).closest("li");
			the_id = the_li.attr("id").replace(/remove_tab_/, "");

			if ( ! tab_req_check(the_id)) {
				_delete_tab_hide(the_li, the_id);
			}
	    }
	});
}

 

// when the page loads set up existing tabs to delete
delete_publish_tab();

add_publish_tab = function() {
	tab_name = $("#tab_name").val();

	var legalChars = /^[a-zA-Z0-9 _-]+$/; // allow only letters, numbers, spaces, underscores, and dashes

	if ( ! legalChars.test(tab_name))
	{
		$.ee_notice(EE.lang.illegal_characters);
	}
	else if (tab_name == "")
	{
		$.ee_notice(EE.lang.tab_name_required);
	}
	else
	{
		if ( ! _add_tab(tab_name))
		{
			$.ee_notice(EE.lang.duplicate_tab_name);
		}
		else
		{
			// remove thickbox
			$("#new_tab_dialog").dialog("close");
		}
	}
}

function _add_tab(tab_name) {
	tab_name_filtered = tab_name.replace(/ /g, "_").toLowerCase();

	// ensure there are no duplicate ids provided
	if ($("#"+tab_name_filtered).length) {
		return false;
	}

	// add the custom tab
	$(".addTabButton").before("<li id=\"menu_"+tab_name_filtered+"\" class=\"content_tab\"><a href=\"#\" class=\"menu_"+tab_name_filtered+"\" title=\"menu_"+tab_name_filtered+"\">"+tab_name+"</a></li>").fadeIn();

	// add the tab to the list in the toolbar
	$("#publish_tab_list").append("<li class=\"remove_"+tab_name_filtered+"\"><a class=\"menu_focus\" title=\"menu_+tab_name_filtered+\" href=\"#\">"+tab_name+"</a> <a href=\"#"+tab_name_filtered+"\" class=\"delete delete_tab\"><img src=\""+EE.THEME_URL+"images/content_custom_tab_delete.png\" alt=\"Delete\" width=\"19\" height=\"18\" /></a></li>");

	new_tab = $("<div class=\"main_tab\"><div class=\"insertpoint\"></div><div class=\"clear\"></div></div>").attr("id", tab_name_filtered);
	new_tab.prependTo("#holder");

	// If this is the only tab on the interface, we should move focus into it
	// The "add tab" button counts for 1, so we look for it plus the new tab (hence 2)
	if ($("#tab_menu_tabs li:visible").length <= 2) {
		tab_focus(tab_name_filtered);
	}  

	// apply the classes to make it look focused
	$("#tab_menu_tabs li").removeClass("current");
	$("#menu_"+tab_name_filtered).addClass("current");
	
	// re-assign behaviours
	setup_tabs();
	delete_publish_tab();
	return true;
}

$("#tab_name").keypress(function(e){
	if (e.keyCode=="13") { // return key press
		add_publish_tab();
		return false;
	}
});

// Sidebar starts out closed - kill tab sorting
$(".tab_menu").sortable("destroy");

