<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class SafeImageRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile) {
            $fail('The :attribute must be a valid file.');
            return;
        }

        // Check if file is actually an image
        if (!$value->isValid()) {
            $fail('The :attribute is not a valid file.');
            return;
        }

        // Get file info
        $filePath = $value->getPathname();
        $fileSize = $value->getSize();
        $mimeType = $value->getMimeType();

        // Validate MIME type
        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!in_array($mimeType, $allowedMimes)) {
            $fail('The :attribute must be a valid image file (JPEG, PNG, or WebP).');
            return;
        }

        // Check file size (max 2MB)
        if ($fileSize > 2 * 1024 * 1024) {
            $fail('The :attribute must not be larger than 2MB.');
            return;
        }

        // Check for potential malicious content
        $fileContent = file_get_contents($filePath);
        
        // Check for PHP tags or other executable content
        if (preg_match('/<\?php|<\?=|<\?/i', $fileContent)) {
            $fail('The :attribute contains potentially malicious content.');
            return;
        }

        // Check for JavaScript content
        if (preg_match('/<script|javascript:/i', $fileContent)) {
            $fail('The :attribute contains potentially malicious content.');
            return;
        }

        // Validate image dimensions using getimagesize
        $imageInfo = @getimagesize($filePath);
        if ($imageInfo === false) {
            $fail('The :attribute is not a valid image file.');
            return;
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];

        // Check dimensions
        if ($width > 2048 || $height > 2048) {
            $fail('The :attribute dimensions must not exceed 2048x2048 pixels.');
            return;
        }

        // Check for extremely small images (potential steganography)
        if ($width < 10 || $height < 10) {
            $fail('The :attribute dimensions are too small.');
            return;
        }
    }
} 