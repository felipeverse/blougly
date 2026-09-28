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

Run `make help` to list the available targets.

The Compose service and the devcontainer both run as a non-root user carrying your host `UID`/`GID`, so anything they write stays writable on the host. If yours are not `1000`, put them in a `.env` in the repository root — it is git-ignored — and rebuild:

```env
UID=1001
GID=1001
```

```bash
make docker-build
```

If `git` fails with `insufficient permission for adding an object to repository database`, a container left root-owned files behind. `make check-owner` finds them and prints the repair command.
