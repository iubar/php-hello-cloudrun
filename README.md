[![CircleCI](https://dl.circleci.com/status-badge/img/gh/iubar/php-hello-cloudrun/tree/main.svg?style=svg)](https://dl.circleci.com/status-badge/redirect/gh/iubar/php-hello-cloudrun/tree/main)

# php-hello-cloudrun
Google Cloud Run hello project

## Links

* https://console.cloud.google.com/sql/instances
* https://console.cloud.google.com/cloud-build/builds
* https://console.cloud.google.com/artifacts
* https://console.cloud.google.com/run
* https://console.cloud.google.com/net-services/nat/

## Prettier

  prettier --check ".\*.{js,css,html,php}" --plugin "%APPDATA%/npm/node_modules/@prettier/plugin-php/src/index.mjs"
  prettier --write ".\*.php" --plugin "%APPDATA%/npm/node_modules/@prettier/plugin-php/src/index.mjs"
