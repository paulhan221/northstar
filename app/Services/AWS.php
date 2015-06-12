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
    public function storeImage($folder, $id, $file)
    {
        if (is_string($file)) {
            $data = base64_decode($file);
            $filename = 'uploads/' . $folder . '/' . $id;
            Storage::disk('s3')->put($filename, $data);
        } else {
            $extension = $file->guessExtension();
            $filename = 'uploads/' . $folder . '/' . $id . '.' . $extension;
            Storage::disk('s3')->put($filename, file_get_contents($file));
        }

        return getenv('S3_URL') . $filename;
    }

}
