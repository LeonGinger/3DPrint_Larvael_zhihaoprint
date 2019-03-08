<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class STLUploadController extends Controller
{
    public function ShowStlMod(){
		return view('stlupload.showstlmod');
	}
}
