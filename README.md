![Dreamstone Logo](https://davidlima.com.br/logo-dreamstone.png)

Dreamstone is a simple, highly customizable CMS based on Symfony 4 framework.


Development environment (Docker) setup:

```
$ composer install
$ cp .env.dist .env # Default environment variables
$ npm install
$ yarn run encore dev # Will build assets
$ docker-compose up -d
$ php bin/console dreamstone:install
```

Running development environment (Docker):
 
 ```
 $ docker-compose up -d
 ```
 
 After this, Dreamstone should be available on
 `localhost` (and `dreamstone.cms`).
 
 Accessing admin panel:
 
 Go to `localhost/dreamstone/`.
 
 Default user data:
 
 ***User***: dreamstone
 
 ***Password***: dreamstone
 