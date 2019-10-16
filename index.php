<?php

function http_error (int $status_code) {

	$message = [
		401 => 'Bad Request',
		403 => 'Forbidden',
		404 => 'Not Found',
		416 => 'Requested Range Not Satisfiable',
		500 => 'Internal Server Error'
	];

	header_remove ();

	header ("Status: $status_code");
	http_response_code ($status_code);

	echo $message [$status_code];

	exit;
}

function send_file (string $file_path) {

	$file_name = basename ($file_path);
	$file_size = filesize ($file_path);

	$file_info = finfo_open (FILEINFO_MIME_TYPE);
	$file_type = finfo_file ($file_info, $file_path);
	finfo_close ($file_info);

	$bytes_send = 0;
	$file = @fopen ($file_path, 'rb');

	header ("Content-Type: $file_type");
	header ("Content-Disposition: attachment; filename=\"$file_name\"");
	header ("Accept-Ranges: bytes");

	header ("Pragma: public");
	header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");

	if (isset ($_SERVER ['HTTP_RANGE'])) {

		list ($unit, $range) = explode ("=", $_SERVER ['HTTP_RANGE'], 2);

		if ($unit != 'bytes')

		list ($range) = explode (",", $range, 2);
		list ($range, $range_end) = explode ("-", $range);

		$range = intval ($range);
		$range_end = ! $range_end ? $file_size - 1 : intval ($range_end);
		$new_length = $range_end - $range + 1;

		fseek ($file, $range);

		header ("HTTP/1.1 206 Partial Content");
		header ("Content-Length: $new_length");
		header ("Content-Range: bytes $range-$range_end/$file_size");

	}
	else {

		$new_length = $file_size;
		header ("Content-Length: " . $file_size);
	}

	while ( ! feof ($file) and ( ! connection_aborted ()) and ($bytes_send < $new_length) ) {

		$buffer = fread ($file, 8192);
		echo ($buffer);

		flush ();

		$bytes_send += strlen ($buffer);
	}

	fclose ($file);
}


$url_path = parse_url ($_SERVER ['REQUEST_URI'], PHP_URL_PATH);

if ($url_path === FALSE or $url_path === NULL)

	http_error (400);

else if (stripos ($url_path, '/files/') !== 0 or $url_path == '/files/')

	http_error (403);

else if ( ! file_exists ($file_path = __DIR__ . DIRECTORY_SEPARATOR . str_replace ('/files/', '', $url_path)))

	http_error (404);


while (ob_get_level ())

	ob_end_clean ();

send_file ($file_path);
