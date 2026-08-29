# IONOS Help Center Plugin

## End to end tests
You can run the end to end tests with the following commands:

```bash
npm install
npm run test:e2e
```

If you get an error that the `@wp-dev/ionos-wp-e2e-playwright` package can not be found on npmjs.org, you need to run the `npm-config.sh` from here https://gitlab.git-wp.server.lan/wp-dev/onboarding.

The `tests/e2e/config.json` file is set as the plugin config, so you can make changes here if you want to test config changes locally.

## Xdebug
- Add server in Settings | PHP | Servers with `localhost` as Host, `8076` as port and `Xdebug` as Debugger.
- Enable path mappings and set `/var/www/html` as server path for the `wp` directory and `/var/www/html/wp-content/plugins/ionos-help-center` as the `ionos-help-center` server path.
- Hit the `Start Listening for PHP Debug Connections` in the Editor toolbar (the phone-like icon on the left of the Git icons group).
- Add a breakpoint in your code and open the website.

### Build locally: Build assets & dependencies

- Build/Update the _*.po_ languages files
- Include the Composer library

```
$ npm run grunt build
```