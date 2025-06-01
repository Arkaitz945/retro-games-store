<?php
class ImageHelper
{
    /**
     * Converts a server file path to a web-accessible URL
     * 
     * @param string $imagePath The image path from database
     * @return string The web-accessible URL or default image if path is invalid/empty
     */
    public static function getImageUrl($imagePath)
    {
        // For debugging purposes
        // error_log("Original image path: " . $imagePath);

        // If path is empty, NULL, or invalid string return default image
        if (empty($imagePath) || $imagePath === 'NULL' || !is_string($imagePath)) {
            return 'css/img/no-image.jpg';
        }

        // Extract just the filename from the full path
        $filename = basename($imagePath);

        // Determine image type (consolas, videojuegos, etc.) from the path
        $imageType = '';
        if (stripos($imagePath, 'consolas') !== false) {
            $imageType = 'consolas';
        } elseif (stripos($imagePath, 'videojuegos') !== false) {
            $imageType = 'videojuegos';
        } elseif (stripos($imagePath, 'revistas') !== false) {
            $imageType = 'revistas';
        } elseif (stripos($imagePath, 'accesorios') !== false) {
            $imageType = 'accesorios';
        }

        // Create the web-accessible path
        if (!empty($imageType)) {
            return "css/img/{$imageType}/{$filename}";
        }

        // If we can't determine the type, try to extract just the relative path from retro-games-store
        if (stripos($imagePath, 'retro-games-store') !== false) {
            $parts = explode('retro-games-store', $imagePath);
            if (count($parts) > 1) {
                // Remove any leading slashes or backslashes
                $relativePath = ltrim($parts[1], '/\\');
                return str_replace('\\', '/', $relativePath);
            }
        }

        // If it's a direct relative path
        if (strpos($imagePath, 'css/') === 0) {
            return $imagePath;
        }

        // Last resort: just use the filename in a generic img directory
        return "css/img/{$filename}";
    }

    /**
     * Checks if a file exists in the specified location, if not creates directories as needed
     * This is for admin file uploads
     * 
     * @param string $targetDir Directory where file should be stored
     * @return bool Success of directory creation
     */
    public static function ensureDirectoryExists($targetDir)
    {
        if (!file_exists($targetDir)) {
            return mkdir($targetDir, 0777, true);
        }
        return true;
    }
}
