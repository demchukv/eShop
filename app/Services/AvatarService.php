<?php

namespace App\Services;

class AvatarService
{
    public function generateAvatar($email, $size = 200)
    {
        $hash = md5(strtolower(trim($email)));
        $colors = ['1abc9c', '2ecc71', '3498db', '9b59b6', '34495e', '16a085', '27ae60', '2980b9', '8e44ad', '2c3e50'];
        $color = $colors[hexdec(substr($hash, 0, 1)) % count($colors)];

        $initials = strtoupper(substr($email, 0, 2));

        return "data:image/svg+xml;base64," . base64_encode(
            '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 ' . $size . ' ' . $size . '" xmlns="http://www.w3.org/2000/svg">' .
                '<rect width="100%" height="100%" fill="#' . $color . '"/>' .
                '<text x="50%" y="50%" dy=".1em" fill="#ffffff" text-anchor="middle" dominant-baseline="middle" font-family="Arial" font-size="' . ($size / 2) . '">' .
                $initials .
                '</text>' .
                '</svg>'
        );
    }
}
