# Assistant Blueprint Feature

This plugin provides functionality to generate blueprint-files. 
It comes in two flavors: one for internal use and one to publish on wp.org.
The difference takes place while versioning the plugin. To publish the plugin, [see instructions below](#Publish).

## Installation
```
composer install
npm install
npm run compile
```

## Publish 
To publish this plugin to wp.org, we do not use our libraries.
Therefore use composer-public.json like this
```
env COMPOSER=composer-public.json composer install
```
Then compile scss with
```
npm install
npm run compile
```

The following command packs this plugin. It respects the _.distignore_-file.
```
wp package install wp-cli/dist-archive-command
cd wp-content/plugins/
wp dist-archive blueprints
```

## Contribute
First install this feature. Then run
```
npm run watch
```
to get the scss constantly compiled.
### i18n
wp i18n make-pot inc  languages/ionos-blueprint.pot  --domain=ionos-blueprint

## Test
- Maybe you have to run ```sudo npx playwright install-deps``` once.
- Create .env-file with your local credentials from .envSAMPLE

To run tests, type
```
npx playwright test --project=chromium
```
