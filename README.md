# blog
Blog api + admin panel based on PHP (Symfony framework)

### Techstack

- PHP 8.4
- Symfony Framework
- Twig
- for local development: docker + docker compose

### Local development

**Requirements:**
- your favourite IDE (PHPStorm/VSCode/Any other...)
- [Docker](https://docs.docker.com/get-started/get-docker/)

**Good to have:**
- prepared git `pre-commit` hook
```bash
cp git/hooks/pre-commit .git/hooks/pre-commit
```

Then run:
```bash
docker compose up --build -d
```
...and visit: https://localhost/api/doc

Before commit:
```bash
make check
```
