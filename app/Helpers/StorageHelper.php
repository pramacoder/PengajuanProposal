<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StorageHelper
{
    protected static string $disk = 'supabase';

    public static function getDisk(): string
    {
        // If the configured key is a JWT (starts with 'ey'), it's invalid for S3 and will timeout.
        // Fallback to 'public' to prevent slow loading times.
        $key = config('filesystems.disks.supabase.key', '');
        if (str_starts_with($key, 'ey')) {
            return 'public';
        }
        
        return static::$disk;
    }

    public static function store(string $path, $file, ?string $filename = null): ?string
    {
        try {
            $disk = Storage::disk(static::getDisk());

            $result = $filename ? $disk->putFileAs($path, $file, $filename) : $disk->putFile($path, $file);

            if ($result === false) {
                throw new \Exception("Supabase storage returned false.");
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error("Supabase Storage upload failed, falling back to public disk: {$e->getMessage()}");
            $fallbackResult = Storage::disk('public')->putFile($path, $file);
            return $fallbackResult === false ? null : $fallbackResult;
        }
    }

    public static function url(string $path): string
    {
        try {
            $disk = Storage::disk(static::getDisk());
            return $disk->temporaryUrl($path, now()->addMinutes(60));
        } catch (\Throwable $e) {
            Log::warning("Supabase temporary URL failed, trying public disk: {$e->getMessage()}");
            return Storage::disk('public')->url($path);
        }
    }

    public static function download(string $path): mixed
    {
        try {
            $disk = Storage::disk(static::getDisk());
            if ($disk->exists($path)) {
                return $disk->download($path);
            }
        } catch (\Throwable $e) {
            Log::warning("Supabase download failed, trying public disk: {$e->getMessage()}");
        }

        return Storage::disk('public')->download($path);
    }

    public static function get(string $path): ?string
    {
        try {
            $disk = Storage::disk(static::getDisk());
            if ($disk->exists($path)) {
                return $disk->get($path);
            }
        } catch (\Throwable $e) {
            Log::warning("Supabase get failed, trying public disk: {$e->getMessage()}");
        }

        return Storage::disk('public')->get($path);
    }

    public static function exists(string $path): bool
    {
        try {
            if (Storage::disk(static::getDisk())->exists($path)) {
                return true;
            }
        } catch (\Throwable $e) {
            Log::warning("Supabase exists check failed: {$e->getMessage()}");
        }

        return Storage::disk('public')->exists($path);
    }

    public static function delete(string $path): bool
    {
        try {
            Storage::disk(static::getDisk())->delete($path);
            return true;
        } catch (\Throwable $e) {
            Log::warning("Supabase delete failed: {$e->getMessage()}");
            return Storage::disk('public')->delete($path);
        }
    }

    public static function response(string $path, ?string $filename = null): mixed
    {
        $content = static::get($path);
        if ($content === null) {
            abort(404, 'File not found');
        }

        $mimeType = static::getMimeType($path);
        $headers = ['Content-Type' => $mimeType];

        if ($filename) {
            $headers['Content-Disposition'] = "inline; filename=\"{$filename}\"";
        }

        return response($content, 200, $headers);
    }

    protected static function getMimeType(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
}
