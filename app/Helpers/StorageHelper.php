<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StorageHelper
{
    protected static string $disk = 'public';

    public static function getDisk(): string
    {
        return static::$disk;
    }

    public static function store(string $path, $file, ?string $filename = null): ?string
    {
        try {
            $disk = Storage::disk(static::getDisk());

            $result = $filename ? $disk->putFileAs($path, $file, $filename) : $disk->putFile($path, $file);

            return $result === false ? null : $result;
        } catch (\Throwable $e) {
            Log::error("Storage upload failed: {$e->getMessage()}");
            return null;
        }
    }

    public static function url(string $path): string
    {
        return Storage::disk(static::getDisk())->url($path);
    }

    public static function download(string $path): mixed
    {
        return Storage::disk(static::getDisk())->download($path);
    }

    public static function get(string $path): ?string
    {
        try {
            $disk = Storage::disk(static::getDisk());
            return $disk->exists($path) ? $disk->get($path) : null;
        } catch (\Throwable $e) {
            Log::warning("Storage get failed: {$e->getMessage()}");
            return null;
        }
    }

    public static function exists(string $path): bool
    {
        try {
            return Storage::disk(static::getDisk())->exists($path);
        } catch (\Throwable $e) {
            Log::warning("Storage exists check failed: {$e->getMessage()}");
            return false;
        }
    }

    public static function delete(string $path): bool
    {
        try {
            return Storage::disk(static::getDisk())->delete($path);
        } catch (\Throwable $e) {
            Log::warning("Storage delete failed: {$e->getMessage()}");
            return false;
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
