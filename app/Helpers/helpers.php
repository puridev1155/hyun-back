<?php

if (!function_exists('removeImageLinks')) {
    /**
     * Remove links around images from HTML.
     *
     * @param string $html
     * @return string
     */
    function removeImageLinks($html)
    {
        return preg_replace('/<a[^>]*>(<img[^>]*>)<\/a>/i', '$1', $html);
    }
}