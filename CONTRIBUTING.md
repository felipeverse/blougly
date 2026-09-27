# Contributing to Blougly

Thank you for considering a contribution!

## Commit convention

Blougly follows [Conventional Commits 1.0.0](https://www.conventionalcommits.org/).

Format:

```
type(scope): subject
```

- **Types:** `feat`, `fix`, `refactor`, `perf`, `test`, `docs`, `chore`, `build`, `ci`, `style`
- **Scopes (optional)** mirror the architecture layers — `domain`, `ports`, `application`, `adapters`, `cli` — plus `docker` and `ci` for infrastructure
- **Subject:** imperative mood, lowercase after the prefix, no trailing period, 72 characters max, in English
- **Breaking changes:** `!` before the colon (`feat(domain)!:`) plus a `BREAKING CHANGE:` footer describing the migration

Examples:

```
feat(adapters): add twig template renderer
fix(domain): reject empty slugs in explicit mode
chore(docker): pin php version in the dev image
```

CI validates the commit message format on pull requests.

## Development setup

Requirements: PHP 8.3+ and Composer — or Docker.

```bash
git clone https://github.com/felipeverse/blougly.git
cd blougly
composer install
```

Run the test suite:

```bash
vendor/bin/pest
```

Prefer a containerized environment? The repo ships a Docker Compose setup and a devcontainer:

```bash
docker compose build
```

Or open the repository in VS Code — the `.devcontainer/` configuration prepares PHP, Xdebug and the recommended extensions.
