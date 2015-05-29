<?php namespace Northstar\Services;

use Storage;

class AWS
{
  /**
   * Store an image in S3.
   *
   * @param string $bucket
   * @param File $file
   */
  public function storeImage($bucket, $id, $file)

  {
    $avatar = 'avatar-' . $id;

    $extension = $file->guessExtension();

    //Use some method to generate your filename here. Here we are just using the User ID.
    $filename = 'uploads/' . $bucket. '/' . $avatar . '.' . $extension;

    //Push file to S3
    Storage::disk('s3')->put($filename, file_get_contents($file));

    // return 's3.amazon.com/uploads/{bucket}/{id}.{extension}'
    return $filename;
  }

}
