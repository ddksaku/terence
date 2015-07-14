<script>
$(document).ready(function(){	

    $('#tab2 .reorder').die();
    $('#tab2 .reorder').live('click', function()
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
                    'portfolio',
                    'post',
                    'category_order',
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
                            setTimeout("alertMessage('success','Category Reordered!')", 1000);  
                        }

                        $.fancybox.close();

                        synergyModulePortfolioRefresh();
                    }
                );
            }
        }

        return false;
    });

        $('.category-change-status').change(function()
        {
            ajaxCallAPI(
                'portfolio',
                'post',
                'categorystatus',
                null,
                {
                    id: $(this).data('category-id'),
                    status: $(this).attr('checked') ? 1 : 0
                },
                function(data)
                {
                    if (data.error == "1") {
                        alertMessage('error','There was an error!');  
                    } else {
                        alertMessage("success", "Category status updated");
                    }

                    $.fancybox.close();
                }
            );
        });

	//The requested content cann
	
	var SCROLL_POS = 0;
	
	// Fancybox 
	$("#tab2 .pop_box").fancybox({ 
	
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
	
	
	
	
	//Set current page
	var current_page = TAB4_PAGE;
	
	if(current_page > 0){
	
	    current_page = current_page * 10;
	
	}
	
	
	
	
	var oTable = $('.data_table4').dataTable({
	 "sDom": "<'row-fluid tb-head'<'span6'f><'span6'<'pull-right'Cl>>r>t<'row-fluid tb-foot'<'span4'i><'span8'p>>",
	  "bJQueryUI": false,
	  "iDisplayLength": 10,
	  "sPaginationType": "bootstrap",
	  "oLanguage": {
		  "sLengthMenu": "_MENU_",
		  "sSearch": "Search"
	  },
	  "iDisplayStart": current_page,
		"aoColumnDefs": [
          { "bSortable": false, "aTargets": [ 0,2,4,5 ] }
       ],
	   "aoColumns": [
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
	
	
	
	
	  // Delete categories  
	  $("#deletecategories").live('click',function(e) {
		         
				 e.preventDefault();
				 
				 var name = $(this).attr("name");
				 Deletecategories($(this).attr("rel"), name);
	  });
	  
          function synergyDeleteCategory(category_id)
          {
              if (category_id instanceof Array) {
                  category_id = category_id.join(',');
              }

              ajaxCallAPI(
                    'portfolio',
                    'post',
                    'deletecategory',
                    null,
                    {
                        id: category_id
                    },
                    function(data)
                    {
                        if (data.error == "1") { 
                            alertMessage("error","You don't have permission to delete this category!"); 

                        }	else {
                              alertMessage('success','Success');

                              synergyModulePortfolioRefresh();
                        }

                        $.fancybox.close();

                        setTimeout('unloading()',900);						

                        return false;
                    }
                );
          }
	  
	  function Deletecategories(datavalue, name){
			  $.confirm({
			  'title': 'DELETE PAGE ('+name+')','message': "Do you want to delete this category?",'buttons': {'Yes': {'class': 'btn btn-success',
			  'action': function(){
				loading('Deleting',1);

                                synergyDeleteCategory(datavalue);
				}},'No'	: {'class'	: 'btn btn-danger'}}});
	  }
	  
	  function Deleteall(datavalue){
			  					$.ajax({
									url: "categories/delete_categories.php",
									data: datavalue,
									cache: false,
									type: "GET",
									dataType: 'json'
								});
				
	  }
	  
	  
	 function Deletejob(){
                                var delete_ids = new Array();
				loading('Deleting',1);
				$('input.delete-category:checked').each(function(){
                                    var the_id = $(this).val();
                                    delete_ids.push(the_id);
				});

                    synergyDeleteCategory(delete_ids);
	  }

});
</script>
                    <div class="btn-group pull-top-right btn-square">
                                <a class="btn btn-large synergy-modal" href="portfolioapi/editcategory"><i class="icon-plus"></i> Add Category</a>
                              </div>
                              <form class="cats_table_holder">
                                <table class="table table-bordered table-striped data_table4" id="data_table4">
                                <thead>
                                  <tr align="center">
                                    <th class="child_1">Image</th>
                                    <th class="child_2">Title</th>
                                    <th class="child_3">Order</th>
                                    <th class="child_4">Active</th>
                                    <th class="child_5">Edit</th>
                                    <th class="child_6">Delete</th>
                                  </tr>
                                </thead>
                                <tbody align="center">
				<?php
                                
                                $previous = 0;
                                foreach ($categories as $category) {
									
                                ?>
                                  <tr
                                      class="reorder-row"
                                      data-id="<?php echo $category->category_id; ?>"
                                      <?php if ($categories->hasNext()): ?>
                                        data-next-id="<?php echo $categories->getInnerIterator()->current()->category_id; ?>"
                                      <?php endif; ?>
                                      <?php if ($previous): ?>
                                        data-prev-id="<?php echo $previous; ?>"
                                      <?php endif; ?>
                                      >

                                    <td>
                                    <?php if($category->category_image): ?>
                                        <a
                                            href="portfolioapi/editcategory"
                                            data-id="<?php echo $category->category_id; ?>"
                                            class="synergy-modal"
                                            >
                                                <img
                                                    src="/uploads/images/square/<?php echo $category->category_image; ?>?date=<?php echo time(); ?>"
                                                    data-src="/uploads/square/<?php echo $category->category_image; ?>"
                                                    alt="<?php echo $category->category_image_alt; ?>"
                                                    class="table-thumb"
                                                    >
                                        </a>
                                    <?php endif;?>
                                    </td>

                                    <td><?php echo $category->category_title; ?></td>

                                    <td>
                                            <div
                                                class="reorder"
                                                id="<?php echo $category->category_id; ?>"
                                                pos="<?php echo $category->category_order; ?>"
                                                name="up"
                                                >
                                                <a>
                                                    <img src="images/icon/up.png" width="20" height="20">
                                                </a>
                                            </div>
                                        
                                            <div
                                                class="reorder"
                                                id="<?php echo $category->category_id; ?>"
                                                pos="<?php echo $category->category_order; ?>"
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
                                                    <? if($category->category_active): ?>
                                                        checked
                                                    <?php endif; ?>
                                                    class="category-change-status"
                                                    data-category-id="<?php echo $category->category_id; ?>"
                                                    >
                                                <label for="checkslide"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <a
                                            href="portfolioapi/editcategory"
                                            class="synergy-modal"
                                            data-id="<?php echo $category->category_id; ?>"
                                            >
                                            <img src="images/icon/gray_18/pencil.png" alt="Edit">
                                        </a>
                                    </td>
                                    
                                    
                                    
                                    <td>
                                        <div
                                            id="deletecategories"
                                            rel="<?php echo $category->category_id; ?>"
                                            name="<?php echo $category->category_title; ?>"
                                            >
                                            <a href="#">
                                                <img src="images/icon/gray_18/trash_can.png" alt="Delete">
                                            </a>
                                        </div>
                                    </td>
                                  </tr>
                                    <?php
                                    
                                    $previous = $category->category_id;
                                }
                                
                                ?>
                                </tbody>
                              </table>
                              </form>