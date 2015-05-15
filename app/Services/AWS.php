<?php namespace Northstar\Services;

class AWS 
{
  /**
   * Store an image in S3.
   *
   * @param string $bucket
   * @param File $file
   */
  public function storeImage($bucket, $file)
  { 
    // @TODO: How do we figure out a unique $id??
    $id = '?????';

    $extension = $file->guessExtension();
      
    //Use some method to generate your filename here. Here we are just using the ID of the image
    
    $filename = 'uploads/' . $bucket. '/' . $id . '.' . $extension;
   
    //Push file to S3
    Storage::disk('s3')->put($filename, file_get_contents($file));
   
    // return 's3.amazon.com/uploads/{bucket}/{id}.{extension}'
    return $filename;
  }

}