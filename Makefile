.DEFAULT_GOAL := help
.PHONY: help build clean docker-build serve check-owner

PORT ?= 8080

help: ## List the available targets
	@grep -hE '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  %-14s %s\n", $$1, $$2}'

build: ## Build the site inside the container
	docker compose run --rm blougly

clean: ## Remove the generated public/ output
	rm -rf public/*

docker-build: ## Rebuild the container image
	docker compose build

serve: ## Serve the site locally
	docker compose run --rm -p $(PORT):$(PORT) blougly php -S 0.0.0.0:$(PORT) -t /blougly/public /blougly/bin/serve.php

check-owner: ## Check for files owned by another user, e.g. root
	@if [ "$$(id -u)" = 0 ]; then \
		echo "check-owner: running as root, every file matches trivially"; \
		exit 1; \
	fi; \
	foreign="$$(find . ! -user $$(id -un) 2>/dev/null)"; \
	if [ -z "$$foreign" ]; then \
		echo "check-owner: ok, everything is owned by $$(id -un)"; \
	else \
		echo "check-owner: $$(printf '%s\n' "$$foreign" | wc -l) files are not owned by $$(id -un)"; \
		printf '%s\n' "$$foreign" | head -5 | sed 's/^/  /'; \
		echo "  repair: sudo chown -R $$(id -un):$$(id -gn) ."; \
		exit 1; \
	fi

