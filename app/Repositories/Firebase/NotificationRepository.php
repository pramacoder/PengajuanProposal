<?php

namespace App\Repositories\Firebase;

class NotificationRepository extends BaseFirestoreRepository
{
    protected string $collection = 'notifications';

    public function getForUser(string $userIdentifier, string $userType, int $limit = 20): array
    {
        $query = $this->getCollection()
            ->where('user_identifier', '=', $userIdentifier)
            ->where('user_type', '=', $userType)
            ->orderBy('created_at', 'DESC')
            ->limit($limit);

        $results = [];
        foreach ($query->documents() as $doc) {
            if ($doc->exists()) {
                $results[] = array_merge(['id' => $doc->id()], $doc->data());
            }
        }

        return $results;
    }

    public function getUnreadForUser(string $userIdentifier, string $userType, int $limit = 20): array
    {
        $query = $this->getCollection()
            ->where('user_identifier', '=', $userIdentifier)
            ->where('user_type', '=', $userType)
            ->where('read_at', '=', null)
            ->orderBy('created_at', 'DESC')
            ->limit($limit);

        $results = [];
        foreach ($query->documents() as $doc) {
            if ($doc->exists()) {
                $results[] = array_merge(['id' => $doc->id()], $doc->data());
            }
        }

        return $results;
    }

    public function getUnreadCount(string $userIdentifier, string $userType): int
    {
        $query = $this->getCollection()
            ->where('user_identifier', '=', $userIdentifier)
            ->where('user_type', '=', $userType)
            ->where('read_at', '=', null);

        $count = 0;
        foreach ($query->documents() as $doc) {
            if ($doc->exists()) $count++;
        }

        return $count;
    }

    public function markAsRead(string $documentId): bool
    {
        return $this->update($documentId, [
            'read_at' => now()->toISOString(),
        ]);
    }

    public function markAllAsRead(string $userIdentifier, string $userType): int
    {
        $unread = $this->getUnreadForUser($userIdentifier, $userType, 100);
        $count = 0;

        foreach ($unread as $notification) {
            if ($this->markAsRead($notification['id'])) {
                $count++;
            }
        }

        return $count;
    }

    public function createNotification(array $data): string
    {
        $notification = [
            'user_identifier' => $data['user_identifier'],
            'user_type' => $data['user_type'],
            'title' => $data['title'],
            'message' => $data['message'],
            'type' => $data['type'] ?? 'info',
            'data' => $data['data'] ?? [],
            'proposal_id' => $data['proposal_id'] ?? null,
            'read_at' => null,
        ];

        return $this->create($notification);
    }

    public function deleteOlderThan(int $days = 90): int
    {
        $cutoff = now()->subDays($days)->toISOString();
        $query = $this->getCollection()
            ->where('created_at', '<', $cutoff);

        $count = 0;
        foreach ($query->documents() as $doc) {
            if ($doc->exists()) {
                $doc->reference()->delete();
                $count++;
            }
        }

        return $count;
    }
}
