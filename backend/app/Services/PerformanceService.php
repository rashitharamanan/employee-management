<?php

namespace App\Services;

use App\Repositories\PerformanceRepository;
use PDO;

class PerformanceService
{
    public function __construct(
        private PerformanceRepository $repository,
        private PDO $db
    ) {
    }

    public function getReviews(array $filters = []): array
    {
        return $this->repository->getReviews($filters);
    }

    public function getReview(int $id): array
    {
        $review = $this->repository->getReviewById($id);
        if (!$review) {
            throw new \Exception('Performance review not found.');
        }
        return $review;
    }

    public function createReview(array $data): array
    {
        $this->validateReviewData($data);

        $reviewId = $this->repository->createReview([
            'employee_id' => (int) $data['employee_id'],
            'reviewer_id' => isset($data['reviewer_id']) ? (int) $data['reviewer_id'] : null,
            'review_period_start' => $data['review_period_start'],
            'review_period_end' => $data['review_period_end'],
            'review_date' => $data['review_date'] ?? null,
            'overall_rating' => isset($data['overall_rating']) ? (float) $data['overall_rating'] : null,
            'overall_comments' => $data['overall_comments'] ?? null,
            'employee_comments' => $data['employee_comments'] ?? null,
            'status' => $data['status'] ?? 'DRAFT',
        ]);

        if (!empty($data['ratings']) && is_array($data['ratings'])) {
            foreach ($data['ratings'] as $rating) {
                $this->repository->addRating($reviewId, [
                    'criteria' => $rating['criteria'],
                    'rating' => (int) $rating['rating'],
                    'weight' => isset($rating['weight']) ? (float) $rating['weight'] : 1.00,
                    'comments' => $rating['comments'] ?? null,
                ]);
            }
        }

        return $this->repository->getReviewById($reviewId);
    }

    public function updateReview(int $id, array $data): array
    {
        $review = $this->repository->getReviewById($id);
        if (!$review) {
            throw new \Exception('Performance review not found.');
        }

        $updateData = [];
        $allowed = ['reviewer_id', 'review_period_start', 'review_period_end', 'review_date', 'overall_rating', 'overall_comments', 'employee_comments', 'status'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if (!empty($updateData)) {
            $this->repository->updateReview($id, $updateData);
        }

        if (isset($data['ratings']) && is_array($data['ratings'])) {
            if (!empty($review['ratings'])) {
                foreach ($review['ratings'] as $existingRating) {
                    $this->repository->deleteRating((int) $existingRating['id']);
                }
            }
            foreach ($data['ratings'] as $rating) {
                $this->repository->addRating($id, [
                    'criteria' => $rating['criteria'],
                    'rating' => (int) $rating['rating'],
                    'weight' => isset($rating['weight']) ? (float) $rating['weight'] : 1.00,
                    'comments' => $rating['comments'] ?? null,
                ]);
            }
        }

        return $this->repository->getReviewById($id);
    }

    public function deleteReview(int $id): void
    {
        $review = $this->repository->getReviewById($id);
        if (!$review) {
            throw new \Exception('Performance review not found.');
        }
        $this->repository->deleteReview($id);
    }

    public function getGoals(array $filters = []): array
    {
        return $this->repository->getGoals($filters);
    }

    public function getGoal(int $id): array
    {
        $goal = $this->repository->getGoalById($id);
        if (!$goal) {
            throw new \Exception('Goal not found.');
        }
        return $goal;
    }

    public function createGoal(array $data): array
    {
        $this->validateGoalData($data);

        $goalId = $this->repository->createGoal([
            'employee_id' => (int) $data['employee_id'],
            'review_id' => isset($data['review_id']) ? (int) $data['review_id'] : null,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'target_date' => $data['target_date'] ?? null,
            'progress' => isset($data['progress']) ? (int) $data['progress'] : 0,
            'status' => $data['status'] ?? 'ACTIVE',
            'priority' => $data['priority'] ?? 'MEDIUM',
        ]);

        return $this->repository->getGoalById($goalId);
    }

    public function updateGoal(int $id, array $data): array
    {
        $goal = $this->repository->getGoalById($id);
        if (!$goal) {
            throw new \Exception('Goal not found.');
        }

        $updateData = [];
        $allowed = ['review_id', 'title', 'description', 'target_date', 'progress', 'status', 'priority'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if (!empty($updateData)) {
            $this->repository->updateGoal($id, $updateData);
        }

        return $this->repository->getGoalById($id);
    }

    public function deleteGoal(int $id): void
    {
        $goal = $this->repository->getGoalById($id);
        if (!$goal) {
            throw new \Exception('Goal not found.');
        }
        $this->repository->deleteGoal($id);
    }

    public function getSummary(array $filters = []): array
    {
        return $this->repository->getPerformanceSummary($filters);
    }

    private function validateReviewData(array $data): void
    {
        if (empty($data['employee_id'])) {
            throw new \Exception('Employee ID is required.');
        }
        if (empty($data['review_period_start'])) {
            throw new \Exception('Review period start date is required.');
        }
        if (empty($data['review_period_end'])) {
            throw new \Exception('Review period end date is required.');
        }
        if (isset($data['overall_rating']) && ($data['overall_rating'] < 0 || $data['overall_rating'] > 5)) {
            throw new \Exception('Overall rating must be between 0 and 5.');
        }
    }

    private function validateGoalData(array $data): void
    {
        if (empty($data['employee_id'])) {
            throw new \Exception('Employee ID is required.');
        }
        if (empty($data['title'])) {
            throw new \Exception('Goal title is required.');
        }
        if (isset($data['progress']) && ($data['progress'] < 0 || $data['progress'] > 100)) {
            throw new \Exception('Progress must be between 0 and 100.');
        }
    }

}
