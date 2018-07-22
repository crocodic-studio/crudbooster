<?php

namespace Crocodicstudio\Crudbooster\Helpers;

class CSSBootstrap
{

	public static function label($label,$class='warning') {
		return "<span class='label label-".$class."'>".$label."</span>";
	}
}