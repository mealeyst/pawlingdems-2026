<p align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="wp-content/themes/pawling-democrats/assets/images/PawlingDemWithText-white.svg">
    <img src="wp-content/themes/pawling-democrats/assets/images/PawlingDemWithText.svg" alt="Pawling Democrats logo" width="360">
  </picture>
</p>

# Pawling Town Democratic Committee — Website

The website for the [Pawling Town Democratic Committee](https://pawlingdems.org/), covering Pawling, New York. It's a self-hosted **WordPress** site running a **custom theme**, `pawling-democrats`, built specifically for the committee rather than a stock/purchased theme.

## What's in this repo

This is the full WordPress install as pulled from production — core, third-party plugins, and legacy themes included — plus the custom theme itself:

```
wp-content/
  themes/pawling-democrats/     the actual site design — start here
  mu-plugins/                   always-on plugins, including a toggleable
                                 maintenance-mode splash page
  plugins/                      third-party plugins (Contact Form 7, Akismet,
                                 Jetpack, hosting-provider integration
                                 plugins, etc.)
  themes/                       legacy themes from the site's history,
                                 kept for reference; pawling-democrats is
                                 the one actually in use
```

**Not** included (gitignored — see `.gitignore`): `wp-config.php` (database credentials) and any `*.sql` database dumps. Both contain live data and are handled outside of version control.

## The `pawling-democrats` theme

A classic PHP theme (not full-site-editing) with five real WordPress Pages — Home, About Us, Get Involved, Contact, Blog — plus a blog where committee members post updates. Page content stays editable through the normal block editor in wp-admin; the theme supplies structure, navigation, and branding around it.

- **Brand system**: navy-and-gold palette carried over from the committee's earlier Contentful-based site, paired with Inter (body) and Roboto Slab (headings)
- **Photography**: real, CC BY-SA–licensed photos of Pawling from Wikimedia Commons, with on-page attribution (see `wp-content/themes/pawling-democrats/assets/images/photos/credits.txt` for sources)
- **Contact form**: Contact Form 7 (already active on the site)

## Local development

1. PHP 8.4, MariaDB, and [WP-CLI](https://wp-cli.org/) locally (this was built against Homebrew's `php@8.4` on macOS)
2. Import a database dump into a fresh local database, and point a local `wp-config.php` at it (not included in this repo — set one up with your own local DB credentials)
3. Serve with PHP's built-in server against a small router script that mimics `.htaccess` rewrites for pretty permalinks:
   ```
   php -S localhost:8888 router.php
   ```
   (`router.php` itself is gitignored — it's a local dev convenience, not part of the site)

## Deploying

Production is shared hosting reached over SFTP/SSH. There's no WP-CLI preinstalled on the host, and its default `php` binary is a CGI wrapper rather than a true CLI — use the version-suffixed binary directly, e.g. `/usr/bin/php8.4-cli`, when running WP-CLI there.

## Maintenance mode

`wp-content/mu-plugins/pawlingdems-maintenance.php` shows a branded "under construction" page to logged-out visitors while leaving the real site visible to anyone logged in, so changes can be reviewed live before going public. Toggle it in `wp-config.php`:

```php
define( 'PAWLINGDEMS_MAINTENANCE_MODE', true );
```
