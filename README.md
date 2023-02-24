# Translate

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

## PHP CS Fixer

```bash
docker-compose exec php bash
PHP_CS_FIXER_IGNORE_ENV=8.2.3 php ./vendor/bin/php-cs-fixer fix --diff --dry-run --config .php-cs-fixer.php --verbose
```

## Build prod image

```bash
docker buildx build -t <your_registry>/translate:<version> . --platform=linux/arm64,linux/amd64 -f _docker/php/prod/Dockerfile --push
```
