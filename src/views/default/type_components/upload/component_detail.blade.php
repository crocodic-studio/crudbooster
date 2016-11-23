<?php 
@$ext         = strtolower(end(explode('.',$value)));
$images_type = array('jpg','png','gif','jpeg','bmp','tiff');
$file        = str_replace('uploads/','',$value);
if(Storage::exists($file)):
	if(in_array($ext, $images_type)):?>
		<a class='fancybox' href='{{asset($value)}}'><img style='max-width:150px' title="Image For {{$form['label']}}" src='{{asset($value)}}'/></a>
	<?php else:?>
		<a href='{{asset($value)}}?download=1' target="_blank">{{trans("crudbooster.button_download_file")}}</a>
	<?php endif;?>
<?php endif;?>