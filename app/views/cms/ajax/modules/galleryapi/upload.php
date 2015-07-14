<script src="components/blueimp/js/tmpl.min.js"></script>
<script src="components/blueimp/js/jquery.fileupload-ui.js"></script>

<script type="text/javascript" >

var upload_url = '<?php echo $upload_script; ?>';

$(document).ready(function() {
        
        window.tmpl.cache = {};

	//Clear Uploads
	$('#reset-gallery-uploads').die('click');
	$('#reset-gallery-uploads').live('click', function(){

	$('#upload-start-btn').unbind('click');

	//Remove upload
	$('.template-upload').remove();

	//Count uploads and then hide buttons if none left
	if(($('.template-upload').length - 1) < 1){

	//Turn buttons off
	$('.togglers').addClass('disable');
	$('#reset-gallery-uploads').removeClass('special');
	$('#upload-start-btn').removeClass('confirm');

	}//if

	});

	//Cancel Upload
	$('.cancelUpload').die('click');
	$('.cancelUpload').live('click', function(){

	$('#upload-start-btn').unbind('click');

	$(this).closest('tr').remove();
	//$('.cancelUpload').click();

	//Count uploads and then hide buttons if none left
	if($('.template-upload').length < 1){

	//Turn buttons off
	$('.togglers').addClass('disable');
	$('#reset-gallery-uploads').removeClass('special');
	$('#upload-start-btn').removeClass('confirm');

	}//if

	});

	//Send additional data
	$('#fileupload').bind('fileuploadsubmit', function (e, data) {
	var inputs = data.context.find(':input');
	if (inputs.filter('[required][value=""]').first().focus().length) {
	return false;
	}
	data.formData = inputs.serializeArray();
	});

	// Initialize the jQuery File Upload widget:
	$('#fileupload').fileupload({
        sequentialUploads: true,
	// Uncomment the following to send cross-domain cookies:
	//xhrFields: {withCredentials: true},
	url: upload_url
			});

	// Enable iframe cross-domain access via redirect option:
	$('#fileupload').fileupload(
	'option',
	'redirect',
	window.location.href.replace(
	/\/[^\/]*$/,
	'components/blueimp/cors/result.html?%s'
	)
	);
        
        var fileQueue = new Array();

	// Initialize the jQuery File Upload widget:
	$('#fileupload').fileupload({
	// Uncomment the following to send cross-domain cookies:
	//xhrFields: {withCredentials: true},
	url: upload_url,
	dropZone: $('#custom-queue'),
	acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
	formData: {example: 'test'}

	}).on('fileuploadadd', function (e, data) {
            // Queue file.
            fileQueue.push(data);

            //Upload Button
            $("#upload-start-btn").on('click', function () {
                //Set loading image
                $('#uploading').show();

                //Hide upload button
                $('.cancelUpload').hide();

                //Disable buttons
                $('#reset-gallery-uploads').toggleClass('disable');

                $('#upload-start-btn').toggleClass('disable');

                //Submit files
                var file;
                while((file = fileQueue.pop())) {
                    file.submit();
                }
                //data.submit();
            });

            //Turn buttons off
            $('.togglers').removeClass('disable');
            $('#reset-gallery-uploads').addClass('special');
            $('#upload-start-btn').addClass('confirm');

	}).on('fileuploadprocessalways', function (e, data) {

	var index = data.index;
	var file = data.files[index];
	//var node = $(data.context.children()[index]);

	//Show error
	if (file.error) {
	//Show error

	}else{

	//Show error

	}

	}).on('fileuploadprogressall', function (e, data) {

	var progress = parseInt(data.loaded / data.total * 100, 10);
	$('#progress .bar').css(
	'width',
	progress + '%'
	);

	if(progress > 99){

	}//if

	}).on('fileuploaddone', function (e, data) {

	/*
	$.each(data.result.files, function (index, file) {

	});
	*/

	}).on('fileuploadstop', function(e){

	//All uploads were completed
	//console.log(e);
	//Turn buttons off
	$('.togglers').addClass('disable');
	$('#reset-gallery-uploads').removeClass('special');
	$('#upload-start-btn').removeClass('confirm');

	//Set loading image
	$('#uploading').hide();

	//show upload button
	$('.cancelUpload').show();

	//Enable buttons
	$('#reset-gallery-uploads').toggleClass('disable');
	$('#upload-start-btn').toggleClass('disable');

	synergyModuleGalleryAlbumRefresh();
        synergyModuleGalleryPicturesRefresh(<?php echo $album->album_id; ?>);

                
                // 
                
		//showSuccess('uploadComplete '+ data.filesUploaded  +' File',7000);
		alertMessage('success','Image(s) Added to Album');
		setTimeout('$.fancybox.close()',500);  // uploadmodal with close  ;

		}).on('fileuploadfail', function (e, data) {

		/*
		$.each(data.result.files, function (index, file) {

		});
		*/
		});

		var ua = navigator.userAgent.toLowerCase();

		if (ua.indexOf('safari')!=-1){
		if(ua.indexOf('chrome')  > -1){

		}else{

		if (navigator.appVersion.indexOf("Win")!=-1){

		//Check version of safari
		var version = ua.match(/version\/([0-9]{1}\.[0-9]{1})/);
		vaersion = parseFloat(version);

		if(version[1] == 5.1){

		//Remove multiple
		$('#add-files-btn').removeAttr('multiple');

		}//if

		}//if

		}
		}

		});
</script>

<!--[if lt IE 7 ]> <style type="text/css"> .progress{ display:none !important;} </style> <![endif]-->
<!--[if IE 7 ]>    <style type="text/css"> .progress{ display:none !important;} </style> <![endif]-->
<!--[if IE 8 ]>    <style type="text/css"> .progress{ display:none !important;} </style> <![endif]-->
<!--[if IE 9 ]>    <style type="text/css"> .progress{ display:none !important;} </style> <![endif]-->

<div class="modal_dialog">
	<div class="header">
		<span>UPLOAD IMAGES</span>
		<div class="close_me pull-right">
			<a href="javascript:void(0)" id="close_windows" class="butAcc"><img src="images/icon/closeme.png" /></a>
		</div>
	</div>
	<div class="content">

		<!-- The file upload form used as target for the file upload widget -->
		<form id="fileupload" method="POST" enctype="multipart/form-data">

			<div class="demo-box">
				<div style="border:#f4f4f4 20px solid; border-bottom:13px solid #f4f4f4">
					<div id="custom-queue" class="custom-queue dialog uploadifyQueue">

						<!-- The table listing the files available for upload/download -->
						<table role="presentation" class="table ">
							<tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody>
						</table>

					</div>
				</div>
			</div>

			<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->

			<div class="upload-group">

				<ul class="nav-btns">

					<li>
						<a class="btn btn-success fileinput-button" id="add-files-dummy">Add Image(s)
						<input id="add-files-btn" type="file" name="files[]" multiple>
						</a>
					</li>
					<li>
						<a  class="btn btn-warning disable togglers" id="reset-gallery-uploads">Clear All Images</a>
					</li>
					<li>
						<a class="btn btn-primary disable start togglers" id="upload-start-btn">Upload</a>
					</li>
                    <li>
                    	<a class="btn btn-danger butAcc" id="close_windows">Cancel</a>
					</li>
                    <li id="uploading" style="display:none;">
						Uploading <img src="images/loader/loader_green.gif" width="16" height="11"/>
					</li>
				</ul>

				<!-- The loading indicator is shown during file processing -->
				<!--span class="fileupload-loading"></span-->
			</div>

			<!-- The global progress information -->
			<div class="fileupload-progress fade" style="display:none;">
				<!-- The global progress bar -->
				<div class="progress progress-success" role="progressbar" aria-valuemin="0" aria-valuemax="100">
					<div class="bar" style="width:0%"></div>
				</div>
				<!-- The extended global progress information -->
				<div class="progress-extended">
					&nbsp;
				</div>
			</div>

		</form>

	</div>
</div>

<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
	{% for (var i=0, file; file=o.files[i]; i++) { %}
	<tr class="template-upload fade">

	<td>

	<div class="upload-gallery-float-holder">
	<div class="upload-row-holder">

	<p class="name">{%=file.name%} ({%=o.formatFileSize(file.size)%})</p>
	{% if (file.error) { %}
	<div><span class="label label-important">Error</span> {%=file.error%}</div>
	{% } %}

	</div>

	<div class="close-holder">

	{% if (!o.files.error && !i && !o.options.autoUpload) { %}
	<button class="btn btn-primary start" style="display:none;">
	<i class="icon-upload icon-white"></i>
	<span>Start</span>
	</button>

	{% } %}
	{% if (!i) { %}

	<a class="cancelUpload">
	<img border="0" src="components/uploadify/cancel.png">
	</a>
	{% } %}

	</div>
	</div>

	<div class="progress-row">

	{% if (!o.files.error) { %}
	<div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
	{% } %}

	</div>

	</td>

	<td class="post" style="display:none;"><input type="hidden" name="albumid" value="<?php echo $album->album_id; ?>" required></td>

	</tr>
	{% } %}
</script>

<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
	{% for (var i=0, file; file=o.files[i]; i++) { %}
	<tr class="template-download fade" style="display:none;">

	<td>
	<p class="name">
	<a href="{%=file.url%}" title="{%=file.name%}" data-gallery="{%=file.thumbnail_url&&'gallery'%}" download="{%=file.name%}">{%=file.name%} ({%=o.formatFileSize(file.size)%})</a>
	</p>
	{% if (file.error) { %}
	<div><span class="label label-important">Error</span> {%=file.error%}</div>
	{% } %}
	</td>

	</tr>
	{% } %}
</script>