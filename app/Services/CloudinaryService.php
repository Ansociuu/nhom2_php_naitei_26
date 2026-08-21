<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CloudinaryService
{
    protected ?string $cloudName;
    protected ?string $apiKey;
    protected ?string $apiSecret;
    protected ?string $uploadPreset;

    public function __construct()
    {
        $this->cloudName = config('services.cloudinary.cloud_name');
        $this->apiKey = config('services.cloudinary.api_key');
        $this->apiSecret = config('services.cloudinary.api_secret');
        $this->uploadPreset = config('services.cloudinary.upload_preset');
    }

    /**
     * Check if Cloudinary is fully configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->cloudName) && (!empty($this->uploadPreset) || (!empty($this->apiKey) && !empty($this->apiSecret)));
    }

    /**
     * Upload an image to Cloudinary (or fallback to local disk storage).
     *
     * @return array{cloudinary_public_id: string, secure_url: string, format: string, width: int, height: int, bytes: int}
     */
    public function upload(UploadedFile $file, string $folder = 'tours'): array
    {
        if ($this->isConfigured()) {
            try {
                return $this->uploadToCloudinary($file, $folder);
            } catch (\Throwable $e) {
                Log::error('Cloudinary upload failed, falling back to local storage: ' . $e->getMessage());
            }
        }

        return $this->uploadToLocalDisk($file, $folder);
    }

    /**
     * Delete an image from Cloudinary or local storage.
     */
    public function delete(string $publicId): bool
    {
        if ($this->isConfigured() && !Str::startsWith($publicId, $folder = 'tours/local_')) {
            try {
                $timestamp = time();
                $paramsToSign = [
                    'public_id' => $publicId,
                    'timestamp' => $timestamp,
                ];
                ksort($paramsToSign);

                $signatureString = http_build_query($paramsToSign) . $this->apiSecret;
                $signature = sha1(str_replace(['&', '='], ['&', '='], $signatureString));

                $response = Http::asForm()->post("https://api.cloudinary.com/v1_1/{$this->cloudName}/image/destroy", [
                    'public_id' => $publicId,
                    'timestamp' => $timestamp,
                    'api_key' => $this->apiKey,
                    'signature' => $signature,
                ]);

                return $response->successful() && ($response->json('result') === 'ok');
            } catch (\Throwable $e) {
                Log::error('Cloudinary delete failed: ' . $e->getMessage());
            }
        }

        if (Storage::disk('public')->exists($publicId)) {
            return Storage::disk('public')->delete($publicId);
        }

        return true;
    }

    /**
     * Internal helper to upload to Cloudinary API.
     */
    protected function uploadToCloudinary(UploadedFile $file, string $folder): array
    {
        $endpoint = "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload";

        $httpRequest = Http::attach(
            'file',
            file_get_contents($file->getRealPath()),
            $file->getClientOriginalName()
        );

        $payload = [
            'folder' => $folder,
        ];

        if (!empty($this->uploadPreset)) {
            $payload['upload_preset'] = $this->uploadPreset;
        } else {
            $timestamp = time();
            $paramsToSign = [
                'folder' => $folder,
                'timestamp' => $timestamp,
            ];
            ksort($paramsToSign);

            $stringToSign = "folder={$folder}&timestamp={$timestamp}" . $this->apiSecret;
            $signature = sha1($stringToSign);

            $payload['timestamp'] = $timestamp;
            $payload['api_key'] = $this->apiKey;
            $payload['signature'] = $signature;
        }

        $response = $httpRequest->post($endpoint, $payload);

        if (!$response->successful()) {
            throw new \RuntimeException('Cloudinary API returned error: ' . $response->body());
        }

        $data = $response->json();

        return [
            'cloudinary_public_id' => $data['public_id'],
            'secure_url' => $data['secure_url'],
            'format' => $data['format'] ?? $file->getClientOriginalExtension(),
            'width' => (int) ($data['width'] ?? 800),
            'height' => (int) ($data['height'] ?? 600),
            'bytes' => (int) ($data['bytes'] ?? $file->getSize()),
        ];
    }

    /**
     * Internal helper to upload to local storage disk as fallback.
     */
    protected function uploadToLocalDisk(UploadedFile $file, string $folder): array
    {
        $path = $file->store($folder, 'public');

        $imageSize = @getimagesize($file->getRealPath());
        $width = $imageSize ? $imageSize[0] : 800;
        $height = $imageSize ? $imageSize[1] : 600;

        return [
            'cloudinary_public_id' => $path,
            'secure_url' => Storage::url($path),
            'format' => $file->getClientOriginalExtension() ?: 'jpg',
            'width' => (int) $width,
            'height' => (int) $height,
            'bytes' => (int) $file->getSize(),
        ];
    }
}
