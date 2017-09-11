![Dreamstone Logo](https://davidlima.com.br/logo-dreamstone.png)

Development environment (Docker) setup:

```
$ composer install
$ bower install
$ docker-compose up -d
$ php bin/console doctrine:schema:update --force
$ php bin/console doctrine:fixtures:load
```

Running development environment (Docker):
 
 ```
 $ docker-compose up -d
 $ php bin/console server:run
 ```
 
 After this, `localhost:8000` should be available.
 
 Accessing admin panel:
 
 Go to `localhost:8000/dreamstone/`.
 
 Default user data:
 
 ***User***: dreamstone
 
 ***Password***: dreamstone
 