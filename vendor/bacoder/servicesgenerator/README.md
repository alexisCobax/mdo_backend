## This package automatically generates basic services to work with Laravel models, the way to use it is

on www/html/app/Console/Kernel.php 

add:

use Bacoder\Servicesgenerator\GenerateService;

and

protected $commands = [
    GenerateService::class,
];

execute console composer dump-autoload

then

php artisan generate:service {Model}

done!

www.bacoder.com