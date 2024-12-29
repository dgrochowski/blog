# blog
Blog api + admin panel based on PHP (Symfony framework)

### Techstack

- PHP 8.4
- Symfony Framework 7.2
- Twig, Serializer, Swagger (NelmioApiDocBundle), stof/doctrine-extensions-bundle
- EasyAdmin (admin panel)
- CQRS
- for local development: docker + docker compose

### Local development

**Requirements:**
- your favourite IDE (PHPStorm/VSCode/Any other...)
- [Docker](https://docs.docker.com/get-started/get-docker/)

### How to run

Run once:
```bash
make setup
```

...then:
```bash
make start
make migrate
```
visit: https://localhost/api/doc
