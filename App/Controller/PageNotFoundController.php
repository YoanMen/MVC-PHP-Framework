<?php

namespace App\Controller;

class PageNotFoundController extends Controller
{

	public function index()
	{
		$this->show("404");
	}
}
