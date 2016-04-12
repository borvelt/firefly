<?php

namespace Respect\Validation\Rules;

class TricksImage extends AbstractRule
{

    public function __construct () {}

    public function validate($input)
    {
        $extension = end(explode('.', $_FILES['image']['name']));
        $valid_extensions = [str_replace('image/', '', $input[1]), str_replace('image/', '', $input[2]), str_replace('image/', '', $input[3]), str_replace('image/', '', $input[4])];
        if (!$_FILES[$input[0]] || !in_array($extension, $valid_extensions) || $_FILES[$input[0]]['size'] > 1000000) {
          return false;
        }
        // $file = new \Upload\File($input[0], null);
        // $file->addValidations([
        //     new \Upload\Validation\Mimetype([$input[1], $input[2], $input[3], $input[4]]),
        //     new \Upload\Validation\Size($input[5]),
        // ]);
		/* $data = array(
			'name'       => $file->getNameWithExtension(),
			'extension'  => $file->getExtension(),
			'mime'       => $file->getMimetype(),
			'size'       => $file->getSize(),
			'md5'        => $file->getMd5(),
			'dimensions' => $file->getDimensions()
		);
		$error = reset($file->getErrors());
		if($error == "") {
			return true;
		}
		return false; */
        return 	true ;//$file->validate();
    }
}
