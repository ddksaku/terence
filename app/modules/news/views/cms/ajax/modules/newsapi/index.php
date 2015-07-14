<script>
$(document).ready(function(){	

        $('.news-change-status').change(function()
        {
            ajaxCallAPI(
                'news',
                'post',
                'status',
                null,
                {
                    id: $(this).data('news-id'),
                    status: $(this).attr('checked') ? 1 : 0
                },
                function(data)
                {
                    if (data.error == "1") {
                        alertMessage('error','There was an error!');  
                    } else {
                        alertMessage("success", "News status updated");
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
			"sDom" : "<'row-fluid tb-head'<'span6'f><'span6'<'pull-right'Cl>>r>t<'row-fluid tb-foot'<'span4'i><'span8'p>>",
			"bJQueryUI" : false,
			"iDisplayLength" : 10,
			"sPaginationType" : "bootstrap",
			"oLanguage" : {
				"sLengthMenu" : "_MENU_",
				"sSearch" : "Search"
			},
			"aoColumnDefs" : [{
				"bSortable" : false,
				"aTargets" : [0, 1, 6, 7, 8]
			}],
			"aoColumns" : [null, null, null, null, null, {
				"sSortDataType" : "dom-checkbox"
			}, null, null, null]
		});
	
	
	
	
	// Select boxes
	$("select").not("select.chzn-select,select[multiple],select#box1Storage,select#box2Storage").selectBox()
	
	
	
	
	  // Delete news  
	  $("#deletenews").live('click',function(e) {
		         
				 e.preventDefault();
				 
				 var name = $(this).attr("name");
				 Deletenews($(this).attr("rel"), name);
	  });
	  
          function synergyDeleteNews(news_id)
          {
              if (news_id instanceof Array) {
                  news_id = news_id.join(',');
              }

              ajaxCallAPI(
                    'news',
                    'post',
                    'delete',
                    null,
                    {
                        id: news_id
                    },
                    function(data)
                    {
                        if (data.error == "1") { 
                            alertMessage("error","You don't have permission to delete this news!"); 

                        }	else {
                              alertMessage('success','Success');

                              synergyModuleNewsRefresh();
                        }

                        $.fancybox.close();

                        setTimeout('unloading()',900);						

                        return false;
                    }
                );
          }
	  
	  function Deletenews(datavalue, name){
			  $.confirm({
			  'title': 'DELETE PAGE ('+name+')','message': "Do you want to delete this news?",'buttons': {'Yes': {'class': 'btn btn-success',
			  'action': function(){
				loading('Deleting',1);

                                synergyDeleteNews(datavalue);
				}},'No'	: {'class'	: 'btn btn-danger'}}});
	  }
	  
	  function Deleteall(datavalue){
			  					$.ajax({
									url: "news/delete_news.php",
									data: datavalue,
									cache: false,
									type: "GET",
									dataType: 'json'
								});
				
	  }
	  
	  
	 function Deletejob(){
                                var delete_ids = new Array();
				loading('Deleting',1);
				$('input.delete-news:checked').each(function(){
                                    var the_id = $(this).val();
                                    delete_ids.push(the_id);
				});

                    synergyDeleteNews(delete_ids);
	  }
	  
	  $('.DeleteAll').live('click',function() {
		   
		   var checked = $('.checksquared').find('input.delete-news:checked').length;
		   
		   if(checked > 0){
			   
			   
			   
			   $.confirm({
				  'title': 'DELETE ALL','message': "Do you want to delete all selected news?",'buttons': {'Yes': {'class': 'btn btn-success',
				  'action': function(){ 
				  Deletejob();
				  }},'No'	: {'class'	: 'btn btn-danger'}}});
				  
			  
			  
			}else{
		   
		       //Nothing was selected
		      alertMessage('error','Please select a news to delete')
	   
		   }

      });


        //Copy News Rows
        $('.copy-row').die();
        $('.copy-row').live('click', function() {

                ajaxCallAPI(
                    'news',
                    'post',
                    'copy',
                    null,
                    {
                        id: $(this).data('id')
                    },
                    function(data)
                    {
                        if (data.success == 1) {
                            alertMessage('success','News item copied above original item!');
                        } else {
                            alertMessage('error','An error occurred');
                        }
                        
                        synergyModuleNewsRefresh();
                    }
                );
        });
	  
});
</script>
                    <div class="btn-group pull-top-right btn-square">
                                <a class="btn btn-large synergy-modal" href="newsapi/edit"><i class="icon-plus"></i> Add News</a>
                                <?php if($user->hasPermission('edit_news_settings')): ?>
                                <a class="btn btn-large synergy-modal" href="newsapi/settings"><i class="icon-cogs"></i> Settings</a>
                                <?php endif; ?>
                                <a class="btn btn-large btn-danger DeleteAll white"><i class="icon-trash"></i> Delete Selected</a>
                              </div>
                              <form class="news_table_holder">
                                <table class="table table-bordered table-striped data_table3" id="data_table3">
                                <thead>
                                  <tr align="center">
                                    <th class="child_1">
										<div class="checksquared"><input type="checkbox" class="checkAll" /><label></label></div>
									</th>
                                    <th class="child_2">Image</th>
                                    <th class="child_3">Title</th>
                                    <th class="child_4">Category</th>
                                    <th class="child_5">Publish</th>
                                    <th class="child_6">Active</th>
                                    <th class="child_7">Copy</th>
                                    <th class="child_8">Edit</th>
                                    <th class="child_9">Delete</th>
                                  </tr>
                                </thead>
                                <tbody align="center">
				<?php
                                
                                foreach ($news as $news) {

                                ?>
                                  <tr>
                                    <td>
                                        <div class="checksquared">
                                            <input
                                                type="checkbox"
                                                name="id"
                                                value="<?php echo $news->news_id; ?>"
                                                class="delete-news"
                                                >
                                            <label></label>
                                        </div>
                                    </td>
                                    
                                    
                                    <td>
                                    <?php if($news->news_image): ?>
                                        <a
                                            href="newsapi/edit"
                                            data-id="<?php echo $news->news_id; ?>"
                                            class="synergy-modal"
                                            >
                                                <img
                                                    src="/uploads/images/square/<?php echo $news->news_image; ?>?date=<?php echo time(); ?>"
                                                    data-src="/uploads/square/<?php echo $news->news_image; ?>"
                                                    alt="<?php echo $news->news_image_alt; ?>"
                                                    class="table-thumb"
                                                    >
                                        </a>
                                    <?php endif;?>
                                    </td>

                                    <td><?php echo $news->news_title; ?></td>
                                    <td>
                                        <?php 

                                        if (count($news->categories) > 0) {
                                            echo implode(', ', $news->categories->lists('category_title'));
                                        } else {
                                            ?> &ndash; <?php
                                        }
                                        
					?>
                                    </td>
                                    <td>
                                        <?php
                                        
                                        if ($news->hasPublishDate()) {
                                            echo date('j M Y', $news->news_publish_date);
                                        }
                                        
                                        ?>
                                    </td>

                                    
                                    <td>
                                        <div class="checkslide">
                                                <input
                                                    type="checkbox"
                                                    id="status"
                                                    name="status"
                                                    value="1"
                                                    <? if($news->news_active): ?>
                                                        checked
                                                    <?php endif; ?>
                                                    class="news-change-status"
                                                    data-news-id="<?php echo $news->news_id; ?>"
                                                    >
                                                <label for="checkslide"></label>
                                        </div>
                                    </td>

                                    <td>
                                        <a class="copy-row" data-id="<?php echo $news->news_id; ?>">
                                            <img src="images/icon/gray_18/copy.png" alt="Copy">
                                        </a>
                                    </td>
                                    
                                    <td>
                                        <a
                                            href="newsapi/edit"
                                            class="synergy-modal"
                                            data-id="<?php echo $news->news_id; ?>"
                                            >
                                            <img src="images/icon/gray_18/pencil.png" alt="Edit">
                                        </a>
                                    </td>
                                    
                                    
                                    
                                    <td>
                                        <div
                                            id="deletenews"
                                            rel="<?php echo $news->news_id; ?>"
                                            name="<?php echo $news->news_title; ?>"
                                            >
                                            <a href="#">
                                                <img src="images/icon/gray_18/trash_can.png" alt="Delete">
                                            </a>
                                        </div>
                                    </td>
                                  </tr>
                                    <?
                                }
                                
                                ?>
                                </tbody>
                              </table>
                              </form>