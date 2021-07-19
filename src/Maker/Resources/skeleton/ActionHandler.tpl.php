<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use VXM\Hasura\Attribute\AsActionHandler;
use VXM\Hasura\Handler\ActionHandlerInterface;

#[AsActionHandler(<?= sprintf("name: '%s'%s", $action, $io ? ', inputClass: Input::class' : ''); ?>)]
final class <?= $class_name ?> implements ActionHandlerInterface
{
    /**
    * {@inheritdoc}
    */
    public function handle(string $action, array | object $input, array $sessionVariables): array | object
    {
        // Handle action logic...
    }
}