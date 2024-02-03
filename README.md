# Spices

Application provides an API to manage the spices storage.

## Installation

To build the application follow the steps below:

- clone the source code from GitHub:

```bash
git clone git@github.com:vdLubart/spices.git
```

- install composer dependencies:

```bash
composer install
```

- create sqlite3 database file in the root project with the `spices.sqlite` name.
- rename `.env.example` into `.env`.
- generate the `APP_SECRET` key and store it in the `.env` file
```bash
php -r 'echo bin2hex(random_bytes(16)), PHP_EOL;'
```
- generate the `OAUTH_PASSPHRASE` key and store it in the `.env` file
```bash
php -r 'echo bin2hex(random_bytes(16)), PHP_EOL;'
```
- build web tokens at `config/jwt` directory. Use previously generated 
`OAUTH_PASSPHRASE` key as a `_passphrase_`.
```bash
cd config/jwt
openssl genrsa -aes128 -passout pass:_passphrase_ -out private.pem 2048
openssl rsa -in private.pem -passin pass:_passphrase_ -pubout -out public.pem
```
- generate `OAUTH_ENCRYPTION_KEY` key and store in in the `.env` file:
```bash
php -r 'echo base64_encode(random_bytes(32)), PHP_EOL;'
```
- create oAuth2 client for the Spices App:
```bash
bin/console league:oauth2-server:create-client spiceStore --scope spice --grant-type password --grant-type refresh_token
```
- create Spices application user:
```bash
bin/console spices:create-user <username> <password> 
```
  
