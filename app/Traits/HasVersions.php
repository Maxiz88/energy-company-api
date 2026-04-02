<?php

namespace App\Traits;

use App\Models\Version;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasVersions
{
    public static function bootHasVersions(): void
    {
        static::saved(function ($model) {
            if ($model->wasRecentlyCreated || $model->wasChanged()) {
                $model->createVersion();
            }
        });
    }

    public function versions(): MorphMany
    {
        return $this->morphMany(Version::class, 'versionable');
    }

    public function createVersion(): Model
    {
        $currentVersion = $this->versions()->max('version_number') ?? 0;

        return $this->versions()->create([
            'version_number' => $currentVersion + 1,
            'data' => $this->getAttributes(),
        ]);
    }

    public function getCurrentVersionNumber(): int
    {
        return $this->versions()->max('version_number') ?? 1;
    }
}
