<?php

namespace App\Observers;

use App\Models\Intern;
use App\Services\GroupCleanupService;

class InternObserver
{
    public function __construct(protected GroupCleanupService $cleanup)
    {
    }

    public function updated(Intern $intern): void
    {
        $originalGroup = $intern->getOriginal('group_id');
        $currentGroup = $intern->group_id;

        // If the intern was moved to another group, check the previous group.
        if ($originalGroup && $originalGroup !== $currentGroup) {
            $this->cleanup->deleteTasksIfEmpty($originalGroup);
        }
    }

    public function deleted(Intern $intern): void
    {
        $groupId = $intern->group_id;
        if ($groupId) {
            $this->cleanup->deleteTasksIfEmpty($groupId);
        }
    }

    public function saved(Intern $intern): void
    {
        // Internship finished or status change: still trigger cleanup for safety.
        $this->cleanup->deleteTasksIfEmpty($intern->group_id);
    }
}
