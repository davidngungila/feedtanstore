<?php

namespace App\Observers;

use App\Services\SEO\SitemapService;

class SitemapObserver
{
    /**
     * Handle the "created" event.
     */
    public function created($model): void
    {
        $this->regenerateSitemap();
    }

    /**
     * Handle the "updated" event.
     */
    public function updated($model): void
    {
        $this->regenerateSitemap();
    }

    /**
     * Handle the "deleted" event.
     */
    public function deleted($model): void
    {
        $this->regenerateSitemap();
    }

    /**
     * Handle the "restored" event.
     */
    public function restored($model): void
    {
        $this->regenerateSitemap();
    }

    /**
     * Handle the "force deleted" event.
     */
    public function forceDeleted($model): void
    {
        $this->regenerateSitemap();
    }

    /**
     * Regenerate the sitemap XML file.
     */
    protected function regenerateSitemap(): void
    {
        SitemapService::generateAndSave();
    }
}
