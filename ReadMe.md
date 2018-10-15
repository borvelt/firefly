# Firefly mixin PHP Framework

![Firefly Image](./_firefly.png "Firefly")

_Ridiculously fast and exceedingly scalable framework that you can use it as multipurpose framework._

## What is Firefly?

Firefly is a mixin framework, this means every part of framework, work with strong library, I have configured and assemble projects to work with each other.

- [Slim](https://github.com/slimphp/Slim) is a micro framework that just handle http requests and it's amazing for make a fast rest api.

- [Eloquent](https://github.com/illuminate/database) is one of the greatest ORM in php, I used it without [Laravel](https://github.com/laravel/laravel) in this project.

- [Twig](https://github.com/twigphp/Twig) Absolutely flexible fast and secure template engine that has been developed by [Symfony](https://github.com/symfony/symfony),i love it.

- [Pimple](https://github.com/silexphp/Pimple) a dependency injector, thanks for [Fabien Potencier](http://fabien.potencier.org/)

- [Respect/Validation](https://github.com/Respect/Validation) is one hundred percent is most power full php validator.

- [Staple](https://github.com/CodeSleeve/stapler) used for database models that contain attachments, this library will connect to ORM and handle model attachments.

- [Faker](https://github.com/fzaninotto/Faker) will produce Fake data to seed database.

## Requirements

This framework need [php5.6](http://php.net/) or later and [composer](https://getcomposer.org/).
Please before use prepare this requirements.

## Installation

Clone or download from github repository:

```bash
git clone https://github.com/borvelt/firefly.git [your-project-name]
cd [your-project-name]
```

This directory contain `composer.json`, this file contain project requirements.

If you are looking in composer.json maybe ask why some versions seems old? I have made this project on March 2016 and be sure all of projects are stable and you can fork framework and upgrade with some changes.
This will create vendor directory and install required libraries:

```bash
composer install
```

## Project Structure

- **app**: Your code should place here, I will describe it.

- **bin**: phpmig for migrations and seeder for database seeding is here.

- **library**: Main codes of Firefly is here be careful.

- **web**: You should place your assets, views, and your uploads and attachments should be here.

## Usage

You can serve this project with apache or every web server you want. Starting point is index.php in root directory.
You can simply run php dev server:

```bash
php -S localhost:8000
```

### Configurations

`app/configs/Bootstrap.php` will config whole project, twig, stapler, eloquent. Your job is done with this.

`app/configs/Config.php` this file contain views, controllers and other parts directory address and other firefly configurations. In most cases you don't need this.

`app/configs/Database.php` Set your database configurations here.
