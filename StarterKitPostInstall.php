<?php

use App\Providers\ContentPublishingServiceProvider;
use Illuminate\Support\ServiceProvider;

class StarterKitPostInstall
{
    public function handle($console)
    {
        ServiceProvider::addProviderToBootstrapFile(ContentPublishingServiceProvider::class);

        // The free edition allows one user per install, so each copy keeps its own.
        $this->appendOnce(base_path('.gitignore'), '/users/*.yaml',
            "\n# Each install keeps its own user accounts.\n/users/*.yaml\n");

        foreach (['.env.example', '.env'] as $file) {
            $this->appendOnce(base_path($file), 'CONTENT_PUBLISH_ENABLED',
                "\n# Turn on only on the CMS server: Control Panel saves commit and push to git.\nCONTENT_PUBLISH_ENABLED=false\n");
        }

        $console->line('');
        $console->info('Static publishing is installed. Next steps are in the README:');
        $console->line('https://github.com/agedgouda/statamic-static-publishing#setting-up-a-site');

        if (file_exists(base_path('.github/workflows/deploy-cloudflare-pages.yml'))) {
            $console->line('Cloudflare Pages: add CLOUDFLARE_API_TOKEN and CLOUDFLARE_ACCOUNT_ID secrets and CLOUDFLARE_PROJECT and SITE_URL variables to the GitHub repo.');
        } else {
            $console->line('GitHub Pages: set the repo\'s Pages source to "GitHub Actions".');
        }
    }

    protected function appendOnce(string $path, string $marker, string $text): void
    {
        if (! file_exists($path)) {
            return;
        }

        if (! str_contains(file_get_contents($path), $marker)) {
            file_put_contents($path, rtrim(file_get_contents($path))."\n".$text);
        }
    }
}
