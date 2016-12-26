MYSQL_PASS = "h7yKl90fc6AA5*j"
THIS_FILE := $(lastword $(MAKEFILE_LIST))
.SILENT:

run:
	MYSQL_PASS="$(MYSQL_PASS)" docker-compose up -d
	sleep 5
	docker exec -t -i opentickets_db_1 mysql -u super -p$(MYSQL_PASS) -e 'create database opentickets'
	docker exec -t -i opentickets_webapp_1 php /data/vendor/bin/doctrine-module orm:schema-tool:create
	docker exec -t -i opentickets_webapp_1 php /data/vendor/carnage/cqrs/bin/cqrs cqrs:rebuild-projection -a
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


