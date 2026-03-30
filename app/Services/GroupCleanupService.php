<?php

namespace App\Services;

use App\Models\Group;

class GroupCleanupService
{
    /**
     * Remove tasks for groups that no longer have active interns.
     *
     * @param  int|string|\App\Models\Group|null  $group
     */
    public function deleteTasksIfEmpty($group): void
    {
        if (! $group) {
            return;
        }

        $group = $group instanceof Group ? $group : Group::find($group);
        if (! $group) {
            return;
        }

        $hasActiveInterns = $group->activeInterns()->exists();

        if (! $hasActiveInterns) {
            // Remove all tasks for the empty group; related TaskUserStatus rows
            // cascade via the FK on task_id.
            $group->tasks()->delete();
        }
    }

    /**
     * Bulk helper: check multiple groups and delete tasks for those with no active interns.
     *
     * @param iterable<int|string> $groupIds
     */
    public function deleteTasksForGroups(iterable $groupIds): void
    {
        collect($groupIds)
            ->filter()
            ->unique()
            ->each(function ($groupId) {
                $this->deleteTasksIfEmpty($groupId);
            });
    }

    /**
     * Scan all groups and remove tasks where there are no active interns.
     */
    public function pruneEmptyGroupTasks(): void
    {
        Group::whereDoesntHave('activeInterns')
            ->chunkById(100, function ($groups) {
                foreach ($groups as $group) {
                    $group->tasks()->delete();
                }
            });
    }
}
