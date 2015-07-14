<script>
$(document).ready(function(){	

    $('.reorder').die();
    $('.reorder').live('click', function()
    {
        var reorder_row = $(this).parents('.reorder-row');

        if (reorder_row.length) {
            var direction = ($(this).attr('name') == 'up')
                                ? 1
                                : 0;

            var relative_id = direction
                                ? reorder_row.data('prev-id')
                                : reorder_row.data('next-id');

            if (relative_id) {
                ajaxCallAPI(
                    'pages',
                    'post',
                    'order',
                    null,
                    {
                        id: reorder_row.data('id'),
                        relative: relative_id,
                        direction: direction
                    },
                    function(data)
                    {
                        if (data.error == "1") {
                            setTimeout("alertMessage('error','There was an error on ReOrder !')", 1000);  
                        } else {
                            setTimeout("alertMessage('success','Page Reordered!')", 1000);  
                        }

                        $.fancybox.close();

                        synergyModulePagesRefresh();
                    }
                );
            }
        }

        return false;
    });

        $('.page-change-status').change(function()
        {
            ajaxCallAPI(
                'pages',
                'post',
                'status',
                null,
                {
                    id: $(this).data('page-id'),
                    status: $(this).attr('checked') ? 1 : 0
                },
                function(data)
                {
                    if (data.error == "1") {
                        alertMessage('error','There was an error!');  
                    } else {
                        alertMessage("success", "Page status updated");
                    }

                    $.fancybox.close();
                }
            );
        });

	//The requested content cann
	
	var SCROLL_POS = 0;
	
	// Fancybox 
	$("#tab1 .pop_box").fancybox({ 
	
		'showCloseButton': false, 
		'onComplete':function(){
		    
		    
			$("#editor").cleditor()[0].disable(false).refresh();
			
			
		},
		
		onStart     :   function() {
			
			SCROLL_POS = $('html').scrollTop();
			$("html, body").animate({ scrollTop: 0 }, 0);
			
		},
		
		onClosed    :   function() {
			
			$("html, body").animate({ scrollTop: SCROLL_POS }, "slow");
			
		}
	
	});
	
	
	
	
	$('#tab1 .data_table3').dataTable({
	  "sDom": "<'row-fluid tb-head'<'span6'f><'span6'<'pull-right'Cl>>r>t<'row-fluid tb-foot'<'span4'i><'span8'p>>",
	  "bJQueryUI": false,
	  "iDisplayLength": 10,
	  "sPaginationType": "bootstrap",
	  "oLanguage": {
		  "sLengthMenu": "_MENU_",
		  "sSearch": "Search"
	  },
	  "aoColumnDefs": [
          { "bSortable": false, "aTargets": [ 0,1,5,7,8] }
       ],
	   "aoColumns": [
			null,
			null,
			null,
			null,
			null,
			null,
			{ "sSortDataType": "dom-checkbox" },
			null,
			null
		]
	});
	
	
	
	
	// Select boxes
	$("select").not("select.chzn-select,select[multiple],select#box1Storage,select#box2Storage").selectBox()
	
	
	
	
	  // Delete pages  
	  $("#deletepages").live('click',function(e) {
		         
				 e.preventDefault();
				 
				 var name = $(this).attr("name");
				 Deletepages($(this).attr("rel"), name);
	  });
	  
          function synergyDeletePage(page_id)
          {
              if (page_id instanceof Array) {
                  page_id = page_id.join(',');
              }

              ajaxCallAPI(
                    'pages',
                    'post',
                    'delete',
                    null,
                    {
                        id: page_id
                    },
                    function(data)
                    {
                        if (data.error == "1") { 
                            alertMessage("error","You don't have permission to delete this page!"); 

                        }	else {
                              alertMessage('success','Success');

                              synergyModulePagesRefresh();
                        }

                        $.fancybox.close();

                        setTimeout('unloading()',900);						

                        return false;
                    }
                );
          }
	  
	  function Deletepages(datavalue, name){
			  $.confirm({
			  'title': 'DELETE PAGE ('+name+')','message': "Do you want to delete this page?",'buttons': {'Yes': {'class': 'btn btn-success',
			  'action': function(){
				loading('Deleting',1);

                                synergyDeletePage(datavalue);
				}},'No'	: {'class'	: 'btn btn-danger'}}});
	  }
	  
	  function Deleteall(datavalue){
			  					$.ajax({
									url: "pages/delete_pages.php",
									data: datavalue,
									cache: false,
									type: "GET",
									dataType: 'json'
								});
				
	  }
	  
	  
	 function Deletejob(){
                                var delete_ids = new Array();
				loading('Deleting',1);
				$('input.delete-page:checked').each(function(){
                                    var the_id = $(this).val();
                                    delete_ids.push(the_id);
				});

                    synergyDeletePage(delete_ids);
	  }
	  
	  $('.DeleteAll').live('click',function() {
		   
		   var checked = $('.checksquared').find('input.delete-page:checked').length;
		   
		   if(checked > 0){
			   
			   
			   
			   $.confirm({
				  'title': 'DELETE ALL','message': "Do you want to delete all selected pages?",'buttons': {'Yes': {'class': 'btn btn-success',
				  'action': function(){ 
				  Deletejob();
				  }},'No'	: {'class'	: 'btn btn-danger'}}});
				  
			  
			  
			}else{
		   
		       //Nothing was selected
		      alertMessage('error','Please select a page to delete')
	   
		   }

      });

	  
});
</script>
                    <div class="btn-group pull-top-right btn-square">
                    			<?php if($user->hasPermission('make_pages')): ?>
                                <a class="btn btn-large synergy-modal" href="pagesapi/edit"><i class="icon-plus"></i> Add Page</a>
                                <?php endif; ?>
                                <?php if($user->hasPermission('edit_pages_settings')): ?>
                                <a class="btn btn-large synergy-modal" href="pagesapi/settings"><i class="icon-cogs"></i> Settings</a>
                                <?php endif; ?>
                                <a class="btn btn-large btn-danger DeleteAll white"><i class="icon-trash"></i> Delete Selected</a>
                              </div>
                              <form class="pages_table_holder">
                                <table class="table table-bordered table-striped data_table3" id="data_table3">
                                <thead>
                                  <tr align="center">
                                    <th class="child_1">
										<div class="checksquared"><input type="checkbox" class="checkAll" /><label></label></div>
									</th>
                                    <th class="child_2">Image</th>
                                    <th class="child_3">Title</th>
                                    <th class="child_4">Parent</th>
                                    <th class="child_5">Added</th>
                                    <th class="child_6">Order</th>
                                    <th class="child_7">Active</th>
                                    <th class="child_8">Edit</th>
                                    <th class="child_9">Delete</th>
                                  </tr>
                                </thead>
                                <tbody align="center">
				<?php
                                
                                $previous = 0;
                                
                                foreach ($pages as $page) {
                                    ?>
                                  <tr
                                      class="reorder-row"
                                      data-id="<?php echo $page->page_id; ?>"
                                      <?php if ($pages->hasNext()): ?>
                                        data-next-id="<?php echo $pages->getInnerIterator()->current()->page_id; ?>"
                                      <?php endif; ?>
                                      <?php if ($previous): ?>
                                        data-prev-id="<?php echo $previous; ?>"
                                      <?php endif; ?>
                                      >
                                    <td>
                                        <div class="checksquared">
                                            <input
                                                type="checkbox"
                                                name="id"
                                                value="<?php echo $page->page_id; ?>"
                                                class="delete-page"
                                                >
                                            <label></label>
                                        </div>
                                    </td>
                                    
                                    
                                    <td>
                                    <?php if($page->page_image): ?>
                                        <a
                                            href="pagesapi/edit"
                                            data-id="<?php echo $page->page_id; ?>"
                                            class="synergy-modal"
                                            >
                                                <img
                                                    src="/uploads/images/square/<?php echo $page->page_image; ?>?date=<?php echo time(); ?>"
                                                    data-src="/uploads/square/<?php echo $page->page_image; ?>"
                                                    alt="<?php echo $page->page_image_alt; ?>"
                                                    class="table-thumb"
                                                    >
                                        </a>
                                    <?php endif;?>
                                    </td>

                                    <td>
                                        <?php echo $page->page_title; ?>
                                        <?php if ($page->module): ?>
                                            (<?php echo $page->module->module_name; ?> module)
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 

                                        if ($page->parent) {
                                                echo $page->parent->page_title;
                                        } else {
                                                echo '&ndash;';
                                        }
                                        
					?>
                                    </td>
                                    <td>
                                        <?php echo $page->page_created->format('j M Y'); ?>
                                    </td>
                                    
                                    <td>
                                            <div
                                                class="reorder"
                                                id="<?php echo $page->page_id; ?>"
                                                pos="<?php echo $page->page_order; ?>"
                                                name="up"
                                                >
                                                <a>
                                                    <img src="images/icon/up.png" width="20" height="20">
                                                </a>
                                            </div>
                                        
                                            <div
                                                class="reorder"
                                                id="<?php echo $page->page_id; ?>"
                                                pos="<?php echo $page->page_order; ?>"
                                                name="down"
                                                >
                                                <a>
                                                    <img src="images/icon/down.png" width="20" height="20">
                                                </a>
                                            </div>
                                    </td>
                                    
                                    <td>
                                        <div class="checkslide">
                                                <input
                                                    type="checkbox"
                                                    id="status"
                                                    name="status"
                                                    value="1"
                                                    <? if($page->page_active): ?>
                                                        checked
                                                    <?php endif; ?>
                                                    class="page-change-status"
                                                    data-page-id="<?php echo $page->page_id; ?>"
                                                    >
                                                <label for="checkslide"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <a
                                            href="pagesapi/edit"
                                            class="synergy-modal"
                                            data-id="<?php echo $page->page_id; ?>"
                                            >
                                            <img src="images/icon/gray_18/pencil.png" alt="Edit">
                                        </a>
                                    </td>
                                    
                                    
                                    
                                    <td>
                                        <div
                                            id="deletepages"
                                            rel="<?php echo $page->page_id; ?>"
                                            name="<?php echo $page->page_title; ?>"
                                            >
                                            <a href="#">
                                                <img src="images/icon/gray_18/trash_can.png" alt="Delete">
                                            </a>
                                        </div>
                                    </td>
                                  </tr>
                                    <?
                                    $previous = $page->page_id;
                                }
                                
                                ?>
                                </tbody>
                              </table>
                              </form>