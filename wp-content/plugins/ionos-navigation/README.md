# IONOS Help Center Plugin

## Build locally: Build assets & dependencies

- Build/Update the _*.po_ languages files
- Include the Composer library

```
$ npm run grunt build
```

## Xdebug
- Add server in Settings | PHP | Servers with `localhost` as Host, `8000` as port and `Xdebug` as Debugger.
- Enable path mappings and set `/var/www/html` as server path for the `wp` directory and `/var/www/html/wp-content/mu-plugins/ionos-navigation` as the `ionos-navigation` server path.
- Hit the `Start Listening for PHP Debug Connections` in the Editor toolbar (the phone-like icon on the left of the Git icons group).
- Add a breakpoint in your code and open the website.