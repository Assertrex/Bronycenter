<?php

/**
* Used for counting and getting conters of users actions
*
* @since Release 0.1.0
*/

namespace BronyCenter;

use Imagick;
use BronyCenter\Utilities;

class Image
{
    /**
     * Singleton instance of a current class
     *
     * @since Release 0.1.0
     */
    private static $instance = null;

    /**
     * Place for instance of an utilities class
     *
     * @since Release 0.1.0
     */
    private $utilities = null;

    /**
     * Get instances of required classes
     *
     * @since Release 0.1.0
     */
    public function __construct()
    {
        $this->utilities = Utilities::getInstance();
    }

    /**
     * Check if instance of current class is existing and create and/or return it
     *
     * @since Release 0.1.0
     * @var boolean Set as true to reset class instance
     * @return object Instance of a current class
     */
    public static function getInstance($reset = false)
    {
        if (!self::$instance || $reset === true) {
            self::$instance = new Image();
        }

        return self::$instance;
    }

    /**
     * Resize and compress an JPEG image
     *
     * @since Release 0.1.0
     * @var array $file Contains details about posted avatar from $_FILES
     * @var integer $compression Compression amount
     * @var array $resolution Final resolution of an image [width, height]
     * @var object $filter Object of a filter used for resizing
     * @return object Object of an modified image
     */
    public function generateJPEGImage($file, $compression, $resolution, $filter)
    {
        $image = new Imagick($file);
        $image->setBackgroundColor('white');
        $image->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
        $image->setImageFormat('jpeg');
        $image->setImageCompression(Imagick::COMPRESSION_JPEG);
        $image->setImageCompressionQuality($compression);
        $image->resizeImage($resolution[0], $resolution[1], $filter, 1);

        return $image;
    }

    /**
     * Create a few versions of an avatar
     *
     * @since Release 0.1.0
     * @var array $file Contains details about posted avatar from $_FILES
     * @var string $hash Hash used to store new avatar
     * @return boolean Status of this method
     */
    public function createAvatar($file, $hash)
    {
        // Define a resolution for avatars showed up on posts page
        $min_res = 64;

        // Define a resolution for the biggest avatars shown without any action
        $def_res = 128;

        // Define a maximal resolution for avatars shown on profile after click
        $max_res = 1000;

        // Get a maximal resolution of an avatar
        $org_image = new Imagick($file['tmp_name']);
        $org_resolution = $org_image->getImageGeometry();

        // Check if width is longer than height
        $isWidthMain = $org_resolution['width'] >= $org_resolution['height'];

        // Count maximal resolution based on an image resolution
        if ($isWidthMain) {
            if ($max_res > $org_resolution['height']) {
                $max_res = $org_resolution['height'];
            }
        } else {
            if ($max_res > $org_resolution['width']) {
                $max_res = $org_resolution['width'];
            }
        }

        // Try to create three versions of an avatar
        try {
            // Create an avatar with maximal resolution
            $max_image = $this->generateJPEGImage($file['tmp_name'], 90, [$max_res, $max_res], Imagick::FILTER_CATROM);

            // Create an avatar with default/standard resolution
            $def_image = $this->generateJPEGImage($file['tmp_name'], 90, [$def_res, $def_res], Imagick::FILTER_CATROM);

            // Create an avatar with minimal resolution
            $min_image = $this->generateJPEGImage($file['tmp_name'], 70, [$min_res, $min_res], Imagick::FILTER_CATROM);

            // Create a folder for avatars with a generated hash name
            mkdir(__DIR__ . '/../../public/media/avatars/' . $hash . '/');
            chmod(__DIR__ . '/../../public/media/avatars/' . $hash . '/', 0775);

            // Insert avatars into created folder
            file_put_contents(__DIR__ . '/../../public/media/avatars/' . $hash . '/maxres.jpg', $max_image);
            file_put_contents(__DIR__ . '/../../public/media/avatars/' . $hash . '/defres.jpg', $def_image);
            file_put_contents(__DIR__ . '/../../public/media/avatars/' . $hash . '/minres.jpg', $min_image);
        } catch (Exception $e) {
            // Return false if code above has generated any errors
            return false;
        }

        // Return true if avatars have been created successfully
        return true;
    }
}
