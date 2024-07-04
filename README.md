# Silverstripe Picsum
This module provides a simple way to load images for an existing database dump. It uses the [Lorem Picsum](https://picsum.photos/) API to fetch images.

## Installation

```bash
composer require pixnyb/silverstripe-picsum --dev
```

## Usage
After importing your database dump, you can run the following task to fetch images for all instances of the image class. It will store the images in the assets directory in local storage.

```bash
sake dev/tasks/download-sample-images
```

## Configuration
You can configure the assets directory in order to comply with custom configurations, use prefetching and set the prefetching limit.

Below are the default configuration values:

```yaml
PixNyb\Picsum\Tasks\DownloadSampleImageTask:
  assets_directory: 'assets'
  prefetch: true
  prefetch_limit: 10
```

> [!NOTE]
> The prefetching feature will remove the need to download a new image for each instance of the image class. It will download a set number of images and reuse them for all instances. This does mean that the images won't be unique, but it will speed up the task significantly.
