<?php

namespace App\Controller;

use App\Core\Exception\FileException;
use App\Core\UploadFile;
use Exception;

class UploadController extends Controller
{

  public function index()
  {
    $this->show("upload");
  }

  public function uploadFile()
  {

    try {
      $file = UploadFile::upload();

    } catch (Exception $e) {
      throw new FileException("Error with file Upload : " . $e->getMessage(), $e->getCode(), $e);
    }




  }
}
