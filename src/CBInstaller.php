<?php 
namespace crudbooster;

class CBInstaller {

	public function gettingReady() {
		echo("Getting Ready CRUDBooster.....\n");	

		$service_providers = [
			'Barryvdh\DomPDF\ServiceProvider::class',
			'Collective\Bus\BusServiceProvider::class',
			'Maatwebsite\Excel\ExcelServiceProvider::class',
			'Unisharp\Laravelfilemanager\LaravelFilemanagerServiceProvider::class',
			'Intervention\Image\ImageServiceProvider::class',
			'crocodicstudio\crudbooster\CRUDBoosterServiceProvider::class'
		];

		$facades = [
			"'PDF' => Barryvdh\DomPDF\Facade::class",
			"'Excel' => Maatwebsite\Excel\Facades\Excel::class",
			"'Image' => Intervention\Image\Facades\Image::class"
		];

		$appfile = file_get_contents(config_path('app.php'));


		$str_sp = "App\Providers\RouteServiceProvider::class,";
		$loc_sp = strpos($appfile, $str_sp);

		if($loc_sp!==FALSE) {
			echo("Location Application Service Providers : [V]\n");		

			foreach($service_providers as $sp) {
				if(strpos($appfile, $sp)!==FALSE) {
					echo('Already exists Service Provider '.$sp."\n");
				}else{
					echo("Added Service Provider $sp\n");
					$appfile = str_replace($str_sp, $str_sp."\n".$sp.",", $appfile);					
				}				
			}			


			$str_fc = "'View' => Illuminate\Support\Facades\View::class,";
			$loc_fc = strpos($appfile,$str_fc);
			if($loc_fc !== FALSE) {
				foreach($facades as $sp) {
					if(strpos($appfile, $sp)!==FALSE) {
						echo('Already exists Facades '.$sp."\n");
					}else{
						echo("Added Facades $sp\n");
						$appfile = str_replace($str_fc, $str_fc."\n".$sp.",", $appfile);					
					}				
				}
			}else{
				echo("View Facade Not Found !\n");
			}

		}else{
			echo("Location Application Service Providers : [X]\n");					
		}

		echo "Saving configuration app.php\n";
		if(file_put_contents(config_path('app.php'), $appfile)) {
			echo "app.php saved !\n";
		}else{
			echo "app.php fail to saved !\n";
		}
	}
}