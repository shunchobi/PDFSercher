<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        foreach ($request->file as $file) {
            $file_name = $file->getClientOriginalName();
            $file->storeAs('public/files', $file_name);
        }
    }
}
