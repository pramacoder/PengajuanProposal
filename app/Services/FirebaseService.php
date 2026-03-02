<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $firestore = null;
    protected bool $available = false;

    public function __construct()
    {
        try {
            if (!class_exists(\Google\Cloud\Firestore\FirestoreClient::class)) {
                Log::info('Firebase: google/cloud-firestore package not installed. Firestore disabled.');
                return;
            }

            $credentialsPath = base_path(env('FIREBASE_CREDENTIALS', ''));

            if (!$credentialsPath || !file_exists($credentialsPath)) {
                Log::info('Firebase: credentials file not found. Firestore disabled.');
                return;
            }

            $this->firestore = new \Google\Cloud\Firestore\FirestoreClient([
                'projectId' => env('FIREBASE_PROJECT_ID'),
                'keyFilePath' => $credentialsPath,
            ]);
            $this->available = true;
        } catch (\Throwable $e) {
            Log::warning("Firebase init failed: {$e->getMessage()}");
        }
    }

    public function getFirestore()
    {
        if (!$this->available || !$this->firestore) {
            throw new \RuntimeException('Firestore is not available.');
        }

        return $this->firestore;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function healthCheck(): array
    {
        if (!$this->available) {
            return [
                'status' => 'disabled',
                'message' => 'Firestore package not installed or credentials missing.',
                'timestamp' => now()->toISOString(),
            ];
        }

        try {
            $this->firestore->collection('_health_check')->document('ping')->set([
                'timestamp' => now()->toISOString(),
            ]);

            return [
                'status' => 'healthy',
                'project_id' => env('FIREBASE_PROJECT_ID'),
                'timestamp' => now()->toISOString(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ];
        }
    }
}
