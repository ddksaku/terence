<script>
$(document).ready(function(){	

	// Fancybox 
	$(".pop_box").fancybox({
	
		'showCloseButton': false,
			  
		onStart     :   function() {
		  
		  SCROLL_POS = $('html').scrollTop();
		  $("html, body").animate({ scrollTop: 0 }, 0);
		  
		},
		
		onClosed    :   function() {
		  
		  $("html, body").animate({ scrollTop: SCROLL_POS }, "slow");
		  
		}
		
	 });
	 
	 

var cols = [
    null,
    null,
    null
];
var targets = [0,2];

	
	$('.data_table3').dataTable({
	  "sDom": "<'row-fluid tb-head'<'span6'f><'span6'<'pull-right'Cl>>r>t<'row-fluid tb-foot'<'span4'i><'span8'p>>",
	  "bJQueryUI": false,
	  "iDisplayLength": 10,
	  "sPaginationType": "bootstrap",
	  "oLanguage": {
		  "sLengthMenu": "_MENU_",
		  "sSearch": "Search"
	  },
	  "aoColumnDefs": [
          { "bSortable": false, "aTargets": targets }
       ],
	   "aoColumns": cols
	});
	
	
	// Select boxes
	$("select").not("select.chzn-select,select[multiple],select#box1Storage,select#box2Storage").selectBox(); 
});

	
	
	
</script>

                              <form class="contact-holder">
                                <table class="table table-bordered table-striped  data_table3" id="data_table3">
                                <thead>
                                  <tr align="center">
                                    <th class="child_3">Company Name</th>
                                    <th class="child_4">Email Address</th>
                                    <th class="child_10">Edit</th>
                                  </tr>
                                </thead>
                                <tbody align="center">
                                  <tr>
                                      <td>
                                          <?php echo $contact->contact_name; ?>
                                      </td>
                                      
                                      <td>
                                          <?php echo $contact->contact_email; ?>
                                      </td>

                                      <td>
                                          <a
                                                href="contactapi/edit"
                                                data-id="<?php echo $contact->contact_id; ?>"
                                                class="synergy-modal"
                                                >
                                              <img src="images/icon/gray_18/pencil.png" alt="Edit">
                                          </a>
                                      </td>
                                  </tr>
                            </tbody>
                          </table>
                          </form>
                              
