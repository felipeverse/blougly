.PHONY: build clean docker-build

build:
	docker compose -f core/docker-compose.yml run --rm blougly

clean:
	rm -rf public/*.html public/assets public/20*
	touch public/.gitkeep

docker-build:
	docker compose -f core/docker-compose.yml build
