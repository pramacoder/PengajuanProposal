<?php

namespace App\Repositories\Firebase;

class ReviewDetailRepository extends BaseFirestoreRepository
{
    protected string $collection = 'review_details';

    public function getByReview(string $reviewType, int $reviewId): ?array
    {
        $query = $this->getCollection()
            ->where('review_type', '=', $reviewType)
            ->where('review_id', '=', $reviewId)
            ->limit(1);

        foreach ($query->documents() as $doc) {
            if ($doc->exists()) {
                return array_merge(['id' => $doc->id()], $doc->data());
            }
        }

        return null;
    }

    public function createReviewDetail(string $reviewType, int $reviewId, array $data): string
    {
        $detail = [
            'review_type' => $reviewType,
            'review_id' => $reviewId,
            'checklist_selected' => $data['checklist_selected'] ?? [],
            'skor_per_kriteria' => $data['skor_per_kriteria'] ?? [],
            'catatan' => $data['catatan'] ?? null,
        ];

        return $this->create($detail);
    }

    public function getByProposal(int $proposalId, ?string $reviewType = null): array
    {
        $query = $this->getCollection()
            ->where('proposal_id', '=', $proposalId);

        if ($reviewType) {
            $query = $query->where('review_type', '=', $reviewType);
        }

        $results = [];
        foreach ($query->documents() as $doc) {
            if ($doc->exists()) {
                $results[] = array_merge(['id' => $doc->id()], $doc->data());
            }
        }

        return $results;
    }
}
