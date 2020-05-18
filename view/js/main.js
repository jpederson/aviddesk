$(document).ready(function() {
		
	$(".update-price").keyup(function(){
		// Update the subtotal for this row.

		var id=$(this).attr('rel');
		var subtotal=$("#invoice_item_qty_"+id).val()*$("#invoice_item_cost_"+id).val();
		var text="";
		if ( subtotal>0 ) {
			text="$"+subtotal.toFixed(2);
		} else {
			text="-";
		}
		$( "#invoice_item_"+id ).html( text );
		
		// Update the total
		var total1=$("#invoice_item_qty_1").val()*$("#invoice_item_cost_1").val();
		var total2=$("#invoice_item_qty_2").val()*$("#invoice_item_cost_2").val();
		var total3=$("#invoice_item_qty_3").val()*$("#invoice_item_cost_3").val();
		var total4=$("#invoice_item_qty_4").val()*$("#invoice_item_cost_4").val();
		var total5=$("#invoice_item_qty_5").val()*$("#invoice_item_cost_5").val();
		var full_total=total1+total2+total3+total4+total5;
		var total_text="";
		if ( full_total>0 ) {
			total_text="$"+full_total.toFixed(2);
		} else {
			total_text="-";
		}
		$( "#new-invoice-total" ).html( total_text );
	});
	
	$("#company-report").change(function(){
		location.href="report-company.php?id="+$("#company-report").val();
	});

	$("#year-report").change(function(){
		location.href="report-year.php?year="+$("#year-report").val();
	});

	$(".success").click(function(){
		$(this).slideUp("fast");
	});
	$(".error").click(function(){
		$(this).slideUp("fast");
	});

});