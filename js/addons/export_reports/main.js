(function($){
	$(function(){
		// Variable
		var $exportForm = $('form[name=report_order_master_manage_layout_form]');

		// Check location
		if(!$exportForm.length)
			return;
		
		var $exportButton = $('#tools_report_order_master a'),
			$saveLayoutButton = $('input[name="dispatch[exim.store_layout.save_as]"]');

		// Click export button when someone hit the enter key on options
		$('body').on('keydown', 'input[name*="export_options"]', function(e){			
			if(e.which === 13){
				e.preventDefault();
				e.stopPropagation();
				$exportButton.click();
			}
		});

		// Click save layout button when someone hit the enter key on layout name input
		$('input[name="layout_data[name]"]').on('keydown', function(e){			
			if(e.which === 13){
				e.preventDefault();
				e.stopPropagation();
				$saveLayoutButton.click();
			}
		})
	});

})($);