# Readme

Actualizacion y puesta en marcha (de lo posible) del Gitbook de Jose:
[Gitbook de Jose](https://jose.gitbooks.io/testing-book/content/index.html)

## Pasos para crear la app con PHPUnit y composer

[Cap 3 de Gitbook]

Crear composer.json en root

Ejecutar composer para crear dependencias en vendor/
    $> `composer install`

Division del proyecto

	: src/
	: tests/
	: composer.json
	: phpunit.xml

Creacion de tests/bootstrap.php (requerido por phpunit.xml)
Necesitamos cargar este fichero para que en la ejecución de los tests, nuestras clases sepan resolver la localización de los ficheros necesarios mediante el estándar *PSR-4.

Creacion de src/XString/XString.php y tests/XString/XStringTest.php


## Fixtures (Cap 4)


### Faker

[Doc de Faker PHP](https://github.com/fzaninotto/Faker)

[Blog Faker PHP](http://wern-ancheta.com/blog/2016/01/28/generating-fake-data-in-php-with-faker/)

Instalar en local con composer desde el root de la app:
	`composer require fzaninotto/faker`


### Alice

[Doc de Nelmio/Alice](https://github.com/nelmio/alice)

[Script setObject (objeto formado por la carga del yml)](https://github.com/nelmio/alice/blob/master/src/ObjectSet.php)

Instalacion con composer desde el root de la app:
	`composer require --dev nelmio/alice` - Con `--dev`, se instala solo en entorno de desarrollo.

Fichero de configuracion: tests/fixtures/reviews.yml


## BBDD (Cap 5)

## TDD

[Framework PHPspec](https://jose.gitbooks.io/testing-book/content/TDDyBDD.html)

Implementado en phpspec_josegitbook

## Clase ConectorBD

Por documentar
Clase [PHPUnit\Extension\Database\TestCase]()




