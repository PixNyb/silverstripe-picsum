<?php

namespace PixNyb\Picsum\Tasks;

use SilverStripe\Dev\BuildTask;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Control\Director;
use SilverStripe\Core\Path;

class DownloadSampleImageTask extends BuildTask
{
    private static $segment = 'download-sample-images';

    protected $title = 'Download Sample Images';
    protected $description = 'Download sample images from Lorem Picsum for each image that doesn\'t exist on disk';
    protected $enabled = true;

    public function run($request)
    {
        // Get all images
        $images = File::get()->filter('ClassName', Image::class);

        // Check if the file exists on disk
        $root = Director::publicFolder();

        foreach ($images as $image) {
            $filename = $image->getFileFilename();
            $path = Path::join($root, $filename);
            echo $path;

            // if (!file_exists($path)) {
            //     $image->setFromAbsoluteURL($image->getURL());
            //     $image->write();
            // }
        }
    }
}
