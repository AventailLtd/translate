# Translate

## Features

- Import Google Spreadsheet stored language keys to `.json` files.

## Basic usage

### Import from Google Spreadsheet
1. Create `keys` folder and add Google credentials json file.
2. Create `export` folder (This folder will contain the language json files.)
3. Customize and Run the following script:
```
docker run -u 1000 --rm --tty \
-e "GOOGLE_CREDENTIALS_FILENAME=GoogleCredentials.json" \
-e "APP_ENV=prod" \
-v /path/to/keys:/app/var/keys \
-v /path/to/export:/app/var/export \
aventailltd/translate:20230227 php cli.php import:google-spreadsheet "1a3bVZkaGq5R631LTDx7hOGsdIA4LuxlHVl-pWNFE1J4" "A3:F" "hu,en,de,ro,ru" --separate
```

## Install Dev (Docker)

1. Set environments with te following examples files
```bash
cp _env/php.example.env _env/php.env
```

2. Install dependencies
```bash
docker-compose run --rm php composer install
```

3. Start containers

```bash 
docker-compose up -d
```

4. Create keys folder and copy keys

```bash
mkdir -p var/keys

# Google Credentials json if you want to import language keys from Google Spreadsheets.
cp /path/to/YourGoogleCredentials.json var/keys/YourGoogleCredentials.json
chmod 600 var/keys/YourGoogleCredentials.json
```

5. Try import from Google Spreadsheet

```bash
docker-compose run --rm -u 1000 php php cli.php import:google-spreadsheet "1a3bVZkaGq5R631LTDx7hOGsdIA4LuxlHVl-pWNFE1J4" "A3:F" "hu,en,de,ro,ru"
```
## PHP CS Fixer

```bash
docker-compose exec php bash
PHP_CS_FIXER_IGNORE_ENV=8.2.3 php ./vendor/bin/php-cs-fixer fix --diff --dry-run --config .php-cs-fixer.php --verbose
```

## Build prod image

```bash
docker buildx build -t <your_registry>/translate:<version> . --platform=linux/arm64,linux/amd64 -f _docker/php/prod/Dockerfile --push
```
