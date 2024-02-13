<?php
namespace App\Controller;

class Controller
{

	public function show($name, $data = [])
	{
		$filename = "../App/View/" . $name . ".php";
		if (file_exists($filename)) {
			$data;
			require_once $filename;

		} else {

			$filename = "../App/View/404.php";
			require_once $filename;
		}
	}
}