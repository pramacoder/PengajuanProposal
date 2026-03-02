<?php

namespace App\Repositories\Firebase;

use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;

abstract class BaseFirestoreRepository
{
    protected $firestore = null;
    protected string $collection;
    protected bool $available = false;

    public function __construct()
    {
        try {
            $service = app(FirebaseService::class);
            if ($service->isAvailable()) {
                $this->firestore = $service->getFirestore();
                $this->available = true;
            }
        } catch (\Throwable $e) {
            Log::info("Firestore repository [{$this->collection}] disabled: {$e->getMessage()}");
        }
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    protected function getCollection()
    {
        if (!$this->available) {
            throw new \RuntimeException('Firestore not available');
        }
        return $this->firestore->collection($this->collection);
    }

    public function create(array $data): string
    {
        if (!$this->available) {
            Log::info("Firestore write skipped [{$this->collection}]: not available");
            return 'firestore-disabled';
        }

        try {
            $data['created_at'] = now()->toISOString();
            $data['updated_at'] = now()->toISOString();
            $docRef = $this->getCollection()->add($data);
            return $docRef->id();
        } catch (\Throwable $e) {
            Log::warning("Firestore create failed [{$this->collection}]: {$e->getMessage()}");
            return 'firestore-error';
        }
    }

    public function find(string $documentId): ?array
    {
        if (!$this->available) return null;

        try {
            $doc = $this->getCollection()->document($documentId)->snapshot();
            if (!$doc->exists()) return null;
            return array_merge(['id' => $doc->id()], $doc->data());
        } catch (\Throwable $e) {
            Log::warning("Firestore find failed [{$this->collection}]: {$e->getMessage()}");
            return null;
        }
    }

    public function update(string $documentId, array $data): bool
    {
        if (!$this->available) return false;

        try {
            $data['updated_at'] = now()->toISOString();
            $this->getCollection()->document($documentId)->set($data, ['merge' => true]);
            return true;
        } catch (\Throwable $e) {
            Log::warning("Firestore update failed [{$this->collection}]: {$e->getMessage()}");
            return false;
        }
    }

    public function delete(string $documentId): bool
    {
        if (!$this->available) return false;

        try {
            $this->getCollection()->document($documentId)->delete();
            return true;
        } catch (\Throwable $e) {
            Log::warning("Firestore delete failed [{$this->collection}]: {$e->getMessage()}");
            return false;
        }
    }

    public function where(string $field, string $operator, $value): array
    {
        if (!$this->available) return [];

        try {
            $query = $this->getCollection()->where($field, $operator, $value);
            $documents = $query->documents();
            $results = [];
            foreach ($documents as $doc) {
                if ($doc->exists()) {
                    $results[] = array_merge(['id' => $doc->id()], $doc->data());
                }
            }
            return $results;
        } catch (\Throwable $e) {
            Log::warning("Firestore where failed [{$this->collection}]: {$e->getMessage()}");
            return [];
        }
    }

    public function all(int $limit = 100): array
    {
        if (!$this->available) return [];

        try {
            $documents = $this->getCollection()->limit($limit)->documents();
            $results = [];
            foreach ($documents as $doc) {
                if ($doc->exists()) {
                    $results[] = array_merge(['id' => $doc->id()], $doc->data());
                }
            }
            return $results;
        } catch (\Throwable $e) {
            Log::warning("Firestore all failed [{$this->collection}]: {$e->getMessage()}");
            return [];
        }
    }
}
