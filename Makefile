.PHONY: build clean docker-build serve

HOST ?= localhost                                                                                                                                                  
PORT ?= 8080

build:
	docker compose -f docker-compose.yml run --rm blougly

clean:
	rm -rf public/*

docker-build:
	docker compose -f docker-compose.yml build

serve:
	docker compose -f docker-compose.yml run --rm -p $(PORT):$(PORT) blougly php -S 0.0.0.0:$(PORT) -t /blougly/public /blougly/bin/serve.php                                                                                                          
    
