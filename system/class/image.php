<?php

/**
 * Class used for image manipulation with ImageMagick.
 *
 * @copyright 2017 BronyCenter
 * @author Assertrex <norbert.gotowczyc@gmail.com>
 * @since 0.1.0
 */
class Image
{
    /**
     * Create profile avatar with 3 resolutions.
     *
     * @since 0.1.0
     * @var string $file Generated temp file of uploaded avatar.
     * @var string $hash Generated hash of avatar.
     * @return boolean Result of this method.
     */
    public function createAvatar($file, $hash) {
        try {
            // Create an avatar with resolution of 256x256.
            $o_image_256 = new Imagick($file);
            $o_image_256->setBackgroundColor('white');
            $o_image_256->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
            $o_image_256->setImageFormat('jpeg');
            $o_image_256->setImageCompression(Imagick::COMPRESSION_JPEG);
            $o_image_256->setImageCompressionQuality(80);
            $o_image_256->resizeImage(256, 256, Imagick::FILTER_CATROM, 1);

            // Create an avatar with resolution of 128x128.
            $o_image_128 = new Imagick($file);
            $o_image_128->setBackgroundColor('white');
            $o_image_128->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
            $o_image_128->setImageFormat('jpeg');
            $o_image_128->setImageCompression(Imagick::COMPRESSION_JPEG);
            $o_image_128->setImageCompressionQuality(70);
            $o_image_128->resizeImage(128, 128, Imagick::FILTER_CATROM, 1);

            // Create an avatar with resolution of 64x64.
            $o_image_64 = new Imagick($file);
            $o_image_64->setBackgroundColor('white');
            $o_image_64->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
            $o_image_64->setImageFormat('jpeg');
            $o_image_64->setImageCompression(Imagick::COMPRESSION_JPEG);
            $o_image_64->setImageCompressionQuality(55);
            $o_image_64->resizeImage(64, 64, Imagick::FILTER_CATROM, 1);

            // Create a folder for avatars with generated hash name.
            mkdir('../media/avatars/' . $hash . '/');
            chmod('../media/avatars/' . $hash . '/', 0775);

            // Insert avatars into a created folder.
            file_put_contents('../media/avatars/' . $hash . '/256.jpg', $o_image_256);
            file_put_contents('../media/avatars/' . $hash . '/128.jpg', $o_image_128);
            file_put_contents('../media/avatars/' . $hash . '/64.jpg', $o_image_64);
        } catch (Exception $e) {
            // Return false if code above has generated any errors.
            return false;
        }

        // Return true if avatars has been created successfully.
        return true;
    }
}
