// JavaScript Document
"use strict";
jQuery( document ).ready(function( $ ) {
	
	var count=1;

	var pw_from_date_dashboard=$("#pwr_from_date_dashboard").val();
	var pw_to_date_dashboard=$("#pwr_to_date_dashboard").val();
	
	function cb(start, end) {

		$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
		
		$("#pwr_from_date_dashboard").val(start.format('YYYY-MM-DD'));
		$("#pwr_to_date_dashboard").val(end.format('YYYY-MM-DD'));
		
		if(count++>1)
		{
			var url = document.URL;
			var pw_from_date=$("#pwr_from_date_dashboard").val();
			var pw_to_date=$("#pwr_to_date_dashboard").val();
			
			//window.location=url+"&pw_from_date="+pw_from_date+"&pw_to_date="+pw_to_date;
		}
			
	}
	function pad(s) { return (s < 10) ? '0' + s : s; }
	function convertDate(inputFormat) {
	  var d = new Date(inputFormat);
	  return [pad(d.getMonth()+1), pad(d.getDate()), d.getFullYear()].join('/');
	}
	
	//default change
	//cb(moment().subtract(29, 'days'), moment());
	
	cb(moment(new Date(pw_from_date_dashboard)),moment(new Date(pw_to_date_dashboard)));
		
	$('#reportrange').daterangepicker({
		"startDate": convertDate(pw_from_date_dashboard),
  		"endDate": convertDate(pw_to_date_dashboard),
		ranges: {
		   'Today': [moment(), moment()],
		   'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
		   'Last 7 Days': [moment().subtract(6, 'days'), moment()],
		   'Last 30 Days': [moment().subtract(29, 'days'), moment()],
		   'This Month': [moment().startOf('month'), moment().endOf('month')],
		   'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		}
	},cb);
	
	
	$('#reportrange').on('apply.daterangepicker', function(ev, picker) {
		$("#pwr_from_date_dashboard").val(picker.startDate.format('YYYY-MM-DD'));
		
		$("#pwr_to_date_dashboard").val(picker.endDate.format('YYYY-MM-DD'));
	});
	
	//change the selected date range of that picker
	
	
	
});