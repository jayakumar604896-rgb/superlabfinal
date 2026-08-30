<?php

use App\Models\Service;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $groups = Service::withTrashed()
            ->with('category:id,name')
            ->get()
            ->groupBy(fn (Service $service) => $service->category_id . '|' . strtolower(trim($service->title)));

        foreach ($groups as $group) {
            if ($group->count() <= 1) {
                continue;
            }

            $keeper = null;
            $bestScore = PHP_INT_MAX;

            foreach ($group as $service) {
                $categoryName = $service->category?->name ?? '';
                $canonicalSlug = Str::slug($categoryName . '-' . $service->title);
                $score = ($service->slug === $canonicalSlug ? 0 : 1_000_000) + $service->id;

                if ($score < $bestScore) {
                    $bestScore = $score;
                    $keeper = $service;
                }
            }

            if (! $keeper) {
                continue;
            }

            foreach ($group as $service) {
                if ($service->id !== $keeper->id) {
                    $service->forceDelete();
                }
            }
        }
    }

    public function down(): void
    {
        // Irreversible cleanup migration.
    }
};
