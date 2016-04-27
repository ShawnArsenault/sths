<?php
function jquery_roster_editor_draggable($jsfunction){
	?>
	<script>
	$(function() {
	    $("#sortProDress, #sortProScratch, #sortFarmDress, #sortFarmScratch").sortable({
        	items: ".playerrow",
         	items: "li:not(.injury)",
        	forcePlaceholderSize: true,
        	connectWith: ".connectedSortable",
        	update: function(event, ui) {<?= $jsfunction ?>}
	    }).disableSelection();
	    
	    $(".playerrow").disableSelection();
	    $('#sortable').draggable();
	});
	</script>
	<?php
}


?>