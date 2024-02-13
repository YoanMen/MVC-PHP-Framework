<?php
namespace App\Controller;

class HomeController extends Controller
{
	public function index()
	{

		$this->show('home');

	}

	public function withParams($data)
	{

		$this->show('homeWithParams', $data);

	}

}
