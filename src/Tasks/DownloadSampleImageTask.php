<?php

namespace PixNyb\Picsum\Tasks;

use SilverStripe\Dev\BuildTask;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Control\Director;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Assets\Storage\FileHashingService;
use Exception;
use SilverStripe\Core\Config\Configurable;

class DownloadSampleImageTask extends BuildTask
{
    use Configurable;

    private static $assets_directory = 'assets';

    /**
     * @internal
     */
    private static $segment = 'download-sample-images';

    protected $title = 'Download Sample Images';
    protected $description = 'Download sample images from Lorem Picsum for each image that doesn\'t exist on disk';
    protected $enabled = true;

    public function run($request)
    {
        // Get all images
        $images = File::get()->filter('ClassName', Image::class);
        echo "Found " . $images->count() . " images\n";
        $counter = 0;

        foreach ($images as $image) {
            $filename = $image->getField('FileFilename');
            if (!$filename) {
                echo "Skipping image without filename\n";
                continue;
            }

            $path = Director::publicFolder() . '/' . self::config()->get('assets_directory') . '/' . $filename;
            $dir = dirname($path);

            if (!file_exists($path)) {
                if (!file_exists($dir))
                    mkdir($dir, 0777, true);

                $width = rand(500, 1000);
                $height = rand(500, 1000);

                $url = 'https://picsum.photos/' . $width . '/' . $height;

                $data = file_get_contents($url);
                file_put_contents($path, $data);

                $counter++;
                echo "Downloaded $filename\n";
            }

            try {
                // If the file does not have a hash, compute it
                if (!$image->getField('FileHash')) {
                    $hash = Injector::inst()->get(FileHashingService::class)->computeFromStream(fopen($path, 'r'));
                    $image->setField('FileHash', $hash);
                }

                $image->setFromLocalFile($path);
                $image->updateFilesystem();

                $image->write();

                // If the image is published, publish the file
                if ($image->isPublished())
                    $image->publishSingle();
            } catch (Exception $e) {
                echo "Error updating $filename\n";
            }
        }

        echo "Downloaded $counter images\n";
    }
}
