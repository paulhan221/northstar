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
  public function storeImage($bucket, $id, $file, $isFile)

  {
    $avatar = 'avatar-' . $id;

    if ($isFile)
    {
        $extension = $file->guessExtension();
        $filename = $bucket . '/' . 'uploads/' . $avatar . '.' . $extension;
        Storage::disk('s3')->put($filename, file_get_contents($file));

    } else {
        // $filename = $bucket . '/' . 'uploads/' . $avatar;
        // Storage::disk('s3')->put($filename, $file);

        // $data = base64_decode($file);
        // $filename = $bucket . '/' . 'uploads/' . $avatar . '.png';
        // $source_img = imagecreatefromstring($data);

        // if ($source_img !== false)
        // {
        //     header('Content-Type: image/png');
        //     imagepng($source_img);
        //     imagedestroy($source_img);
        // } else {
        //     echo 'An error occured.';
        // }
        //     Storage::disk('s3')->put($filename, $source_img);

        $filename = $bucket . '/' . 'uploads/' . $avatar . '.jpg';
        $data = base64_decode($file);
        Storage::disk('s3')->put($filename, $data);

        // $filename = $bucket . '/' . 'uploads/' . $avatar . '.jpg';
        // $image = file_put_contents($filename, $file);
        // Storage::disk('s3')->put($filename, $image);

    }

    return 'https://s3.amazonaws.com/' . $filename;

  }

}
