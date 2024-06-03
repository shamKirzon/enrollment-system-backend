<?php
function get_root_dir(): string {
  return dirname(__FILE__);
}

function get_server_url(): string {
  // Replace this with wherever you're running this app
  return "http://localhost/php/enrollment";
}

function upload_file(string $raw_data, string $storage_dir, string $student_id, string $label) {
  $image_info = getimagesizefromstring($raw_data);

  if(!$image_info) {
    throw new Exception("Failed to get image info.", 500);
  }

  $mime_type = $image_info['mime'];

  $filename = 'STUDENT' . '_' . $student_id . '_' . $label . '.jpg';
  // The path from the root directory 
  $path = get_root_dir() . $storage_dir . '/' . $filename;

  if(file_put_contents($path, $raw_data) === false) {
    throw new Exception("Failed to upload file.", 500);
  }

  // The 'URL' that doesn't contain the root directory
  $file_url = $storage_dir . '/' . $filename;
  return $file_url;
}
?>
