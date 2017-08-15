<?php 
$imagesExtension = explode(',',config('crudbooster.IMAGE_EXTENSIONS'));
$ext = pathinfo($value,PATHINFO_EXTENSION);
if(Storage::exists($value)):
	if(in_array($ext, $imagesExtension)):?>
		<a data-lightbox='roadtrip' href='{{asset($value)}}'><img style='max-width:150px' title="Image For {{$form['label']}}" src='{{asset($value)}}'/></a>
	<?php else:?>
		<a href='{{asset($value)}}?download=1' title="File For {{$form['label']}}" target="_blank">{{trans("crudbooster.button_download_file")}}</a>
	<?php endif;?>
<?php endif;?>