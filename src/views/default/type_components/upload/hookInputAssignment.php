<?php

if (Request::hasFile($name)) {
    $file = Request::file($name);
    $ext = $file->getClientOriginalExtension();
    $filename = str_slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
    $filesize = $file->getClientSize() / 1024;
    $file_path = 'uploads/'.date('Y-m');

    if ($ro['options']['max_filesize'] && $filesize > $ro['options']['max_filesize']) {
        $redir = redirect()->back()->with([
            'message_type' => 'warning',
            'message' => 'The filesize for '.$name.' is to larger',
        ])->withInput();
        Session::driver()->save();
        $redir->send();
        exit;
    }

    if ($ro['options']['allowed_filetype'] && ! in_array($ext, $ro['options']['allowed_filetype'])) {
        $redir = redirect()->back()->with([
            'message_type' => 'warning',
            'message' => 'The filetype for '.$name.' is not allowed',
        ])->withInput();
        Session::driver()->save();
        $redir->send();
        exit;
    }

    //Create Directory Monthly						
    Storage::makeDirectory($file_path);

    if ($ro['options']['encrypt'] == true) {
        $filename = md5(str_random(5)).'.'.$ext;
    } else {
        $filename = str_slug($filename, '_').'.'.$ext;
    }

    Storage::putFileAs($file_path, $file, $filename);

    $this->arr[$name] = $file_path.'/'.$filename;
}