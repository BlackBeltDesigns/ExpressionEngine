/*
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2010, EllisLab, Inc.
 * @license		http://expressionengine.com/docs/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
/*
 * ExpressionEngine Content Edit Javascript
 *
 * @package		ExpressionEngine
 * @subpackage	Control Panel
 * @category	Control Panel
 * @author		ExpressionEngine Dev Team
 * @link		http://expressionengine.com
 */
$(document).ready(function() {
	$(".paginationLinks .first").hide();
	$(".paginationLinks .previous").hide();
	
	$(".toggle_all").toggle(
		function(){		
			$("input.toggle").each(function() {
				this.checked = true;
			});
		}, function (){
			var checked_status = this.checked;
			$("input.toggle").each(function() {
				this.checked = false;
			});
		}
	);
	
	
	$("#custom_date_start_span").datepicker({
		dateFormat: "yy-mm-dd",
		prevText: "<<",
		nextText: ">>",
		onSelect: function(date) { 
			$("#custom_date_start").val(date);
			dates_picked();
		} 
	}); 
	$("#custom_date_end_span").datepicker({ 
		dateFormat: "yy-mm-dd",
		prevText: "<<",
		nextText: ">>",
		onSelect: function(date) {
			$("#custom_date_end").val(date);
			dates_picked();
		} 
	});

	$("#custom_date_start, #custom_date_end").focus(function(){
		if ($(this).val() == "yyyy-mm-dd")
		{
			$(this).val("");
		}
	});

	$("#custom_date_start, #custom_date_end").keypress(function(){
		if ($(this).val().length >= 9)
		{
			dates_picked();
		}
	});

	function dates_picked()
	{
		if ($("#custom_date_start").val() != "yyyy-mm-dd" && $("#custom_date_end").val() != "yyyy-mm-dd")
		{
			// populate dropdown box
			focus_number = $("#date_range").children().length;
			$("#date_range").append("<option id=\"custom_date_option\">" + $("#custom_date_start").val() + " to " + $("#custom_date_end").val() + "</option>");
			document.getElementById("date_range").options[focus_number].selected=true;
			
			// hide custom date picker again
			$("#custom_date_picker").slideUp("fast");
		}
	}
	
	
	$("#date_range").change(function(){
		
			if ($('#date_range').val() == 'custom_date')
			{
				// clear any current dates, remove any custom options
				$('#custom_date_start').val('yyyy-mm-dd');
				$('#custom_date_end').val('yyyy-mm-dd');
				$('#custom_date_option').remove();

				// drop it down
				$('#custom_date_picker').slideDown('fast');
			}
			else
			{
				$('#custom_date_picker').hide();
			}
		
	});

	// Require at least one comment checked to submit	
	$("#entries_form").submit(function() {
		if ( ! $("input:checkbox", this).is(":checked")) {
		$.ee_notice(EE.lang.selection_required, {"type" : "error"});
		return false;
		}
	});


	var oCache = {
		iCacheLower: -1
	};

	function fnSetKey( aoData, sKey, mValue )
	{
		for ( var i=0, iLen=aoData.length ; i<iLen ; i++ )
		{
			if ( aoData[i].name == sKey )
			{
				aoData[i].value = mValue;
			}
		}
	}

	function fnGetKey( aoData, sKey )
	{
		for ( var i=0, iLen=aoData.length ; i<iLen ; i++ )
		{
			if ( aoData[i].name == sKey )
			{
				return aoData[i].value;
			}
		}
		return null;
	}

	function fnDataTablesPipeline ( sSource, aoData, fnCallback ) {
		var iPipe = EE.edit.pipe;  /* Ajust the pipe size */

		var bNeedServer = false;
		var sEcho = fnGetKey(aoData, "sEcho");
		var iRequestStart = fnGetKey(aoData, "iDisplayStart");
		var iRequestLength = fnGetKey(aoData, "iDisplayLength");
		var iRequestEnd = iRequestStart + iRequestLength;
		var keywords    = document.getElementById("keywords");
	    var status       = document.getElementById("f_status");
		var channel_id    = document.getElementById("f_channel_id");
	    var cat_id       = document.getElementById("f_cat_id");
	    var search_in  = document.getElementById("f_search_in");
	    var date_range = document.getElementById("date_range");

		var comment_url = "&ajax=true&keywords="+keywords.value+"&channel_id="+channel_id.value;

		if (search_in.value == "comments")
		{
				window.location = EE.BASE+"&C=content_edit&M=view_comments"+comment_url;
		}

		aoData.push( 
			 { "name": "keywords", "value": keywords.value },
	         { "name": "status", "value": status.value },
			 { "name": "channel_id", "value": channel_id.value },
	         { "name": "cat_id", "value": cat_id.value },
	         { "name": "search_in", "value": search_in.value },
	         { "name": "date_range", "value": date_range.value }
		 );

		oCache.iDisplayStart = iRequestStart;

		/* outside pipeline? */
		if ( oCache.iCacheLower < 0 || iRequestStart < oCache.iCacheLower || iRequestEnd > oCache.iCacheUpper )
		{
			bNeedServer = true;
		}

		/* sorting etc changed? */
		if ( oCache.lastRequest && !bNeedServer )
		{
			for( var i=0, iLen=aoData.length ; i<iLen ; i++ )
			{
				if ( aoData[i].name != "iDisplayStart" && aoData[i].name != "iDisplayLength" && aoData[i].name != "sEcho" )
				{
					if ( aoData[i].value != oCache.lastRequest[i].value )
					{
						bNeedServer = true;
						break;
					}
				}
			}
		}

		/* Store the request for checking next time around */
		oCache.lastRequest = aoData.slice();

		if ( bNeedServer )
		{
			if ( iRequestStart < oCache.iCacheLower )
			{
				iRequestStart = iRequestStart - (iRequestLength*(iPipe-1));
				if ( iRequestStart < 0 )
				{
					iRequestStart = 0;
				}
			}

			oCache.iCacheLower = iRequestStart;
			oCache.iCacheUpper = iRequestStart + (iRequestLength * iPipe);
			oCache.iDisplayLength = fnGetKey( aoData, "iDisplayLength" );
			fnSetKey( aoData, "iDisplayStart", iRequestStart );
			fnSetKey( aoData, "iDisplayLength", iRequestLength*iPipe );

					aoData.push(  
			 			{ "name": "keywords", "value": keywords.value },
	         			{ "name": "status", "value": status.value },
			 			{ "name": "channel_id", "value": channel_id.value },
	         			{ "name": "cat_id", "value": cat_id.value },
	         			{ "name": "search_in", "value": search_in.value },
	         			{ "name": "date_range", "value": date_range.value }
		 			);

			$.getJSON( sSource, aoData, function (json) { 
				/* Callback processing */
				oCache.lastJson = jQuery.extend(true, {}, json);

				if ( oCache.iCacheLower != oCache.iDisplayStart )
				{
					json.aaData.splice( 0, oCache.iDisplayStart-oCache.iCacheLower );
				}
				json.aaData.splice( oCache.iDisplayLength, json.aaData.length );

				fnCallback(json)
			} );
		}
		else
		{
			json = jQuery.extend(true, {}, oCache.lastJson);
			json.sEcho = sEcho; /* Update the echo for each response */
			json.aaData.splice( 0, iRequestStart-oCache.iCacheLower );
			json.aaData.splice( iRequestLength, json.aaData.length );
			fnCallback(json);
			return;
		}
	}

		oTable = $("#entries_form .mainTable").dataTable( {	
				"sPaginationType": "full_numbers",
				"bLengthChange": false,
				"aaSorting": [[ 5, "desc" ]],
				"bFilter": false,
				"sWrapper": false,
				"sInfo": false,
				"bAutoWidth": false,
				"iDisplayLength": EE.edit.perPage,  
				"table_columns" : EE.edit.tableColumns,


			"oLanguage": {
				"sZeroRecords": EE.lang.noEntries,

				"oPaginate": {
					"sFirst": "<img src=\""+EE.edit.themeUrl+"images/pagination_first_button.gif\" width=\"13\" height=\"13\" alt=\"&lt; &lt;\" />",
					"sPrevious": "<img src=\""+EE.edit.themeUrl+"images/pagination_prev_button.gif\" width=\"13\" height=\"13\" alt=\"&lt; &lt;\" />",
					"sNext": "<img src=\""+EE.edit.themeUrl+"images/pagination_next_button.gif\" width=\"13\" height=\"13\" alt=\"&lt; &lt;\" />", 
					"sLast": "<img src=\""+EE.edit.themeUrl+"images/pagination_last_button.gif\" width=\"13\" height=\"13\" alt=\"&lt; &lt;\" />"
				}
			},

				"bProcessing": true,
				"bServerSide": true,
				"sAjaxSource": EE.BASE+"&C=content_edit&M=edit_ajax_filter",
				"fnServerData": fnDataTablesPipeline
			});

			$("#keywords").keyup( function () {
			/* Filter on the column (the index) of this element */
				oTable.fnDraw();
			} );

			$("select#f_channel_id").change(function () {
					oTable.fnDraw();
			});	

			$("select#f_cat_id").change(function () {
					oTable.fnDraw();
			});						
			$("select#f_status").change(function () {
					oTable.fnDraw();
			});		
			$("select#f_search_in").change(function () {
					oTable.fnDraw();
			});	
			$("select#date_range").change(function () {
					oTable.fnDraw();
			});

			// The oracle knows everything.  

			var channel_oracle = EE.edit.channelInfo;
			var spaceString = new RegExp('!-!', "g");

			// We prep our magic arrays as soons as we can, basically
			// converting everything into option elements
			(function() {
				jQuery.each(channel_oracle, function(key, details) {

					// Go through each of the individual settings and build a proper dom element
					jQuery.each(details, function(group, values) {
						var newval = new Array();

						// Add the new option fields
						jQuery.each(values, function(a, b) {
							newval.push(new Option(b[1].replace(spaceString, String.fromCharCode(160)), b[0]));
						});

						// Set the new values
						channel_oracle[key][group] = $(newval);
					});
				});

			})();


			// Change the submenus
			// Gets passed the channel id
			function changemenu(index)
			{
				var channels = 'null';

				if (channel_oracle[index] === undefined) {
					index = 0;
				}
				jQuery.each(channel_oracle[index], function(key, val) { 
					switch(key) {
						case 'categories':	$('select[name=cat_id]').empty().append(val);
							break;
						case 'statuses':	$('select[name=status]').empty().append(val);
							break;
					}
				});
			}

			$('select[name=channel_id]').change(function() {
				changemenu(this.value);
			});	
	
});

/*
                                         ,r5GABBBHBM##########Bhi:.                                         
                                       :2#@#MBM#@@@@@@@@@@@@@@@@@Hr                                         
                                    .;2#@@@@BAH#@@@@#MMM#@@@@@@@@@@3;.                                      
                                   :5#@@@#HHBMBHAG925issiXAMBAAM@@@@@MS,                                    
                                 .;9@@@#MHBM#MAXir;;;;;;;;riX&HBM@@@@@@A;.                                  
                                ,5#@@#HAHBBA3ir;::::::;;::;rs2A#@@@@@@@@@5,                                 
                               :9@@@@@MBAXSs;,.,,:::::::::;;;;s9M@@##@@@@#s.                                
                              ,S#@@@@M&Xs;:,,,,,::::::::::::;;;ri2&BMM#@@@&r.                               
                             .sH@@@Bh5r;::,,,:::,,::::,;;;;;;;;;;ri9B#B#@@@Ar.                              
                             ;h@@@#hi;::::::::::,,:;;:;;;;;;;;;;rrri9B##@@@@X;.                             
                            :2#@@@MXs;::::;;;::::;;;;;r;::::;rrsssrrihM@@@@@H2:                             
                           .rH@@@@A2srrr;;;;;;;;;;r;;::,,,:;s5XXX332is5&#@@@#&r                             
                           .sB@@@#A2issrrrrr;;;;;;;:,..,;s222SS2GBM&XS2&#@@@#BS,                            
                           ,S#@@@MA35isrrr;;::::,,,...:ihB&S;:;s529AHHA&B@@#HA9s,                           
                           :X@@@#BAh2Ssr;::,,,..,.  .:s233i;:;s22S59&Xrr3#@HX2X5:                           
                           :3@@@@#B&XSisssiSir;;:..:risrri5X252X25525r;;5B#Gis5S:                           
                           ,iB@@@@@MAGhGAM#Mh55XX22hHAS;:;i25issrrsisr;;s&BGSsSS:                           
                            :2#@@@@#@@#A9XG&S:.r#@@AX33s;;;;rrssrriir;;;s3BMGiii:                           
                         .  .rH@@@@@#BAh33A#Hi:;2&2r;s22SisssiSs;,,:;rsrs2ABAX22;                           
                         .   :SH#@@@MA&AH&922SrrSX2s;ri22XSr:,,,. .,:rsii2X2Ssii:                           
                               ;&@@@@H9X25irrssi29Xs:,,;rrrr:...,,:::;rrsS555SSi:                           
                              .r&@@@#AXSiS2X2SS222i;. .,:;;rr;;;rrr;;;;;rsS2GAA2;                           
                         ,::.   .s#@MG3XX5ir;::riis;::;rsrrssr;;;rrrrr;;;;ri3A&S:                           
                       .;9A3i;.  .9@MhSis;:....:sS5Sisssrrrrrr;;rrr;;;::;;;s2&hi:                           
                       .rh3rrsSSs;:sGBGSr;::::::rsiSSs;:,,:;rrsiSir;:,:;;;;r2ABA3Sr,                        
                        ;3X;,:rXA2;r3BB9irr;rrrrrrrrrr;:::;;;;;s22i;:,:;rrrsXMMG3h9S;.                      
                        ;B@Gr..:r2&A2;:sXSsrrrsissrrrrrr;:.,,,:rssr:::;;rrrSGMAirsX&&Xi;:,                  
                       ;h@@@2:..,sA#3, r&3Ss;;;sS225s;:,,..,:;iis;:;;;;rssi2ABAXr;sG##A93Xi;.               
                      ;#@@H2r;,.,rXH&srhMXss;:,:rS9h2s;;rssrrssr;;;;rrrrssi3AHMBXrrS&G25G#@#hi;,.           
                     ;A@@9;,,::,.;X3r.,X#&ir;;:,,:r2XXXX2ir;:,,,:;rrrrsrsi2332hBM&2S5Srr5H@@@#BG2i;,.       
                    ;h#Ai;,,:;::;9#5   ;M@#5rsr;:::rsis;::,,,,,:;siissisi2h3Ssi9B#A2Sir;;iX&AM###MHh2ir;:.:,
                  .r9AS: ..,;;;sh@@S    ;A@#A2srrrrr;;;;:;;;;;rrrrssiiS52992iii533SrriSs;ri2XGG33h93GAHHH39r
                 :SAAi,...,:;;;5B@Bs      i@@@AX5Sisr;rrsrr;;;;rrrsiS52XX2SisiSSisrrrS92ss2X392sri522223GGAX
               .rH@@Xr:::::;;;:;XHhs,     :B@@@@H9225Sissrr;;:,,:;si52X932issrsssrrrssr;:;i55ir:;SAH9r:;r5G9
             .;G@@@&ir;::,,,,,...,rXh2r,  ,rS2&#@@@MBA2i;::::::;ri2333X2Sssrr;;rsirrsr::;rsiSsr;iXh2i:.,risi
.          ,sH@@@@Gir;r;;::,,..   .:;rrrsis;;rsXA#@@@@MAXir;riXhGAA&322Ssrrr;;;;;;;rss;::;;rsiS525r:,,,:srrr
ir;:.,,,;iB@@@@@BXs;;rrr;;:::,,......,::;rrssssrrrssi2&@@@#MMMBAG32Sissrrrrr;;:::;rsir,.,:;s52225ir:,:;rr:;;
A@@@@#BhXG##Air;;;;;rrr;;::::::,,,,,.....:ri5Ssr;:;rrr;;i3H##AXisrrrrrrrrsrr::,.,;is;. .;SX32irrrrr;;;sis;rr
#@@@@@@@@@Ar,.,::;rrrrr;;::::::,,,::,,..:r29Xs;::r2AH3r::;i2992Siiiiisrrrr;,,,,,:;s;,  :5GhS;,,:ris;,,;is;;;
#MM##@@@@As,.,;;rrrr;;;;;;;:::::,,,,,:,:;s3G2;,:r59&hS;;rii;r5h92SSSsrrssr;,.:;;;;:,..;iXXi;,,;5G9S;..;ii;;:
BAAAAM#HXr;:,:rsrrrr;;;;;::,:::::,,,,,,,:;s2Sr;rsiS25r:.:S5;:rGBG5SSrrrssr:,,;rr;::,,;i5isr::;iXXi;,,:rss:;;
BHBHAABAXr:,,;;rrrrr;;::::::;::::::,,,,,,,;S2isr;;;i2S;..rs;:;G@M325iiisr;;;ris;:;;;;;;;;rsr;:rsr:,:;;rsr;sr
BBMMHAAAhi:,,;;r;;;;::::;;;;:::::::::,,..,rXXir;;;rsiissrrr;,,sMMh3hG2r;;;;riSr;;r;:,,::;sSs;:;sirrrr;rrrrSi
BAHMMHA&3i;::;;;rrr;;;;;;;;:::;;::::::;;ri525ir;;rSSr;;i2iss,  sAM&2Ss;;;;;;rsr;rr:. .:;;rsr::rS2SSir;;;;;ss
MHHM##MHhS;:::;;;rrrrrr;;;;;;;:::::,,:;ri25iiSs;:;S5;,.;iiii;  ,G@Hs::rr;;;;rr;;;r,  ,:;rssr;;rSSiss;:;;;:;r
#MMBMM##H2r:,:;;;rrr;r;;;;;;;;;;;;;::,,,:r522Ss;:;ii;,,;rrrir.  r32r,:;r;;;rrr;:;;,  ,:rriir;:;sirrr;::;r:;;
MBBHAAB#MGs::::;;;;;;;rrr;;;;:::::::::,.:s9&Xsrrrris;:;rrrrsr:   ,;;::;;;;;rsr;:;;, .,:;rssr:,:riiis;::;r;;:
MB##MHHM@#ASr::;;;;;;;;rrr;;;;::::::;;;;s23XSsrr;ris;;;rsrrss;. .:;;;;;;;;;rrr;:;;:..,:;riir;:;sSisr;;:;ir;:
#######@@@@@A5sssssrr;;rrrrrrr;;;r;;;rs522SiSir::;iir;;iSsrrr;,.;i;::;;;;;;;;;;:;r:..,;rrssr;:;iir::;;:;irr;
3sr;rssi2&HBH&39&M#@Mh2S5SSisrrrrr;;;;rS39X222r,,rX9Srrri525i;::;r:,;rr;;;r;;;;;rr:,,:;rrsrr;:;sr:,:rs;;srr;
.            ,rS3B@@@@A325iissrrr;;;;;sXHMA9XS;,:s9A3Sr;r592s;;;;::riSr;:rsrrr;;rr:,,:;rsiis;;rir,.:rir;srsr
                    ,,,,............,rh@@BX5is;:;iX35sr;;siisr;;,,;i2S;,.;rrr;;;r;,..:;riSir;;sir,.,rir;rrss
                                    .:iA#Bh2Sirrri22SisssrsSSir;;;sSSs;::;ssrrrrsr;::;rsiSSsrsSSs;::rsssssiS
*/