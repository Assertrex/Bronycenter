<?php

/**
 * Class used for image manipulation with ImageMagick
 *
 * @since 0.1.0
 */
class Image
{
    public function __construct()
	{

	}

    /**
     * Create avatar with 3 resolutions
     *
     * @since 0.1.0
     * @var string $file Generated temp file of uploaded image
     * @var string $hash Generated hash of image
     */
    public function createAvatar($file, $hash) {
        try {
            $o_image = new Imagick($file);
            $o_image->setBackgroundColor('white');
            $o_image->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
            $o_image->setImageFormat('jpeg');
            $o_image->setImageCompression(Imagick::COMPRESSION_JPEG);
            $o_image->setImageCompressionQuality(80);
            $o_image->resizeImage(256, 256, Imagick::FILTER_CATROM, 1);
            mkdir('../media/avatars/' . $hash . '/');
            file_put_contents('../media/avatars/' . $hash . '/256.jpg', $o_image);

            $o_image = new Imagick($file);
            $o_image->setBackgroundColor('white');
            $o_image->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
            $o_image->setImageFormat('jpeg');
            $o_image->setImageCompression(Imagick::COMPRESSION_JPEG);
            $o_image->setImageCompressionQuality(70);
            $o_image->resizeImage(128, 128, Imagick::FILTER_CATROM, 1);
            file_put_contents('../media/avatars/' . $hash . '/128.jpg', $o_image);

            $o_image = new Imagick($file);
            $o_image->setBackgroundColor('white');
            $o_image->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
            $o_image->setImageFormat('jpeg');
            $o_image->setImageCompression(Imagick::COMPRESSION_JPEG);
            $o_image->setImageCompressionQuality(55);
            $o_image->resizeImage(64, 64, Imagick::FILTER_CATROM, 1);
            file_put_contents('../media/avatars/' . $hash . '/64.jpg', $o_image);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}
