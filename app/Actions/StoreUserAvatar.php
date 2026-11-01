<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Profile photo: image ko 256x256 square mein badalta hai aur dubara encode karta hai.
 * Dubara encode karne se EXIF (GPS location jaisi private info) apne aap hat jati hai.
 */
class StoreUserAvatar
{
    private const SIZE = 256;
    private const MAX_PIXELS = 40_000_000; // bahut bade (decompression bomb) images se bachav

    public function handle(User $user, UploadedFile $file): void
    {
        $path = $file->getRealPath();
        $info = $path ? @getimagesize($path) : false;

        if (! $info || ! in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
            $this->fail('Please upload a JPG, PNG or WebP image.');
        }

        if ($info[0] * $info[1] > self::MAX_PIXELS) {
            $this->fail('This image is too large. Please choose a smaller one.');
        }

        $source = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
        };

        if (! $source) {
            $this->fail('We could not read this image. Please try another one.');
        }

        $source = $this->fixOrientation($source, $info[2], $path);

        $width = imagesx($source);
        $height = imagesy($source);
        $side = min($width, $height);

        $canvas = imagecreatetruecolor(self::SIZE, self::SIZE);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled(
            $canvas,
            $source,
            0,
            0,
            intdiv($width - $side, 2),
            intdiv($height - $side, 2),
            self::SIZE,
            self::SIZE,
            $side,
            $side
        );

        $useWebp = function_exists('imagewebp');
        ob_start();
        $useWebp ? imagewebp($canvas, null, 85) : imagejpeg($canvas, null, 88);
        $binary = (string) ob_get_clean();

        $newPath = 'avatars/' . $user->id . '-' . Str::random(16) . ($useWebp ? '.webp' : '.jpg');
        Storage::disk('public')->put($newPath, $binary);

        $old = $user->avatar;
        $user->forceFill(['avatar' => $newPath])->save();

        if ($old) {
            Storage::disk('public')->delete($old);
        }
    }

    public function remove(User $user): void
    {
        $old = $user->avatar;
        $user->forceFill(['avatar' => null])->save();

        if ($old) {
            Storage::disk('public')->delete($old);
        }
    }

    private function fixOrientation(\GdImage $image, int $type, string $path): \GdImage
    {
        if ($type !== IMAGETYPE_JPEG || ! function_exists('exif_read_data')) {
            return $image;
        }

        $orientation = (int) (@exif_read_data($path)['Orientation'] ?? 1);

        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };

        return $rotated ?: $image;
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['avatar' => $message]);
    }
}
