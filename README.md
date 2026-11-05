# Statamic Static Publishing

A Statamic starter kit for small sites where:

- the editor uses the Statamic Control Panel on a small PHP server (for example one managed by Forge), and
- the public site is a static build on **GitHub Pages** or **Cloudflare Pages** (you choose during install).

Every Control Panel save commits the changed content and uploaded assets as the editor and pushes them. The push runs a GitHub Actions workflow that rebuilds and publishes the static site. Your code changes reach the CMS server through its deploy script.

## Installing

New site:

```
statamic new my-site agedgouda/statamic-static-publishing
```

Existing site:

```
php please starter-kit:install agedgouda/statamic-static-publishing
```

The installer asks where the public site should be hosted. It also:

- adds `statamic/ssg` and a static-build config that copies everything in `public/`
- registers `App\Providers\ContentPublishingServiceProvider` (publishing, and a `/404` page for the static build)
- stops tracking `users/*.yaml`: the free edition allows one user per install, so your laptop and the CMS server each keep their own
- adds `CONTENT_PUBLISH_ENABLED=false` to `.env` and `.env.example`

## Setting up a site

### 1. Public host

**GitHub Pages**: in the repo's **Settings → Pages**, set the source to **GitHub Actions**. Add a custom domain there if you have one.

**Cloudflare Pages**: create a Pages project with **Direct Upload**, then add to the GitHub repo under **Settings → Secrets and variables → Actions**:

| Kind | Name | Value |
|---|---|---|
| Secret | `CLOUDFLARE_API_TOKEN` | API token with *Cloudflare Pages: Edit* |
| Secret | `CLOUDFLARE_ACCOUNT_ID` | Your account ID |
| Variable | `CLOUDFLARE_PROJECT` | The Pages project name |
| Variable | `SITE_URL` | The public URL, such as `https://example.com` |

`public/_headers` sets long cache times for built assets.

### 2. CMS server (Forge)

1. Create the site with a **deploy key**, and before installing the repository, add the key in GitHub under **Settings → Deploy keys** with **Allow write access** ticked.
2. For the first deploy, keep Forge's default deploy script. Afterwards change its `git pull origin $FORGE_SITE_BRANCH` line to:
   ```
   $FORGE_PHP artisan content:update
   ```
   This pulls under the same lock as editor saves, so a deploy never collides with one.
3. In the site's environment, set `CONTENT_PUBLISH_ENABLED=true`, then deploy again (Forge caches config).
4. On the server, create the editor's login: `php please make:user`.
5. Turn on **Quick Deploy**.

PHP-FPM with `pm = ondemand` keeps the site near zero memory when nobody is editing.

## Day to day

- **You:** `git pull` before working locally, push when done. Keep code changes and content changes apart where you can.
- **Editor:** saves go live in a minute or two.
- **Conflicts:** if you and the editor change the same field, the server keeps the editor's commit, pushes nothing, and logs the error. Resolve it on the server, then run `php artisan content:publish`.

Publishing is off unless `CONTENT_PUBLISH_ENABLED=true`, so saving in the Control Panel on your laptop never pushes.
