MYSQL_PASS := $(shell pwgen 32 1)
THIS_FILE := $(lastword $(MAKEFILE_LIST))
.SILENT:

run:
	echo "Mysql Pass:" $(MYSQL_PASS)
	MYSQL_PASS="$(MYSQL_PASS)" docker-compose up -d
	docker exec -t -i opentickets_webapp_1 sh -c 'php /data/vendor/bin/doctrine-module orm:schema-tool:create'
	docker exec -t -i opentickets_webapp_1 sh -c 'php /data/vendor/bin/cli cqrs:rebuild-projection -a'
	$(MAKE) -f $(THIS_FILE) port

stop:
	MYSQL_PASS="$(MYSQL_PASS)" docker-compose stop

start:
	MYSQL_PASS="$(MYSQL_PASS)" docker-compose start

clean:
	MYSQL_PASS="$(MYSQL_PASS)" docker-compose rm

port:
	echo -n "App running at http://localhost:"
	docker port opentickets_webapp_1 | cut -f2 -d:


