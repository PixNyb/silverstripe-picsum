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
        $counter = 0;

        foreach ($images as $image) {
            $filename = $image->FileFilename;
            $path = Director::publicFolder() . '/assets/' . $filename;
            $dir = dirname($path);

            if (!file_exists($path)) {
                if (!file_exists($dir))
                    mkdir($dir, 0777, true);

                $width = rand(500, 1000);
                $height = rand(500, 1000);

                $url = 'https://picsum.photos/' . $width . '/' . $height;
                $data = file_get_contents($url);
                file_put_contents($path, $data);
                $image->setFromLocalFile($path);
                $image->updateFilesystem();

                $counter++;
                echo "Downloaded $filename\n";
            }
        }

        echo "Downloaded $counter images\n";
    }
}
