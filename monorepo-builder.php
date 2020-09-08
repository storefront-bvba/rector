<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::DIRECTORIES_TO_REPOSITORIES, [
        __DIR__ . '/packages/symfony-php-config' => 'git@github.com:rector/symfony-php-config.git',
    ]);
};
