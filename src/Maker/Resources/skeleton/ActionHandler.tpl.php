<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use Hasura\Attribute\AsActionHandler;
use Hasura\Handler\ActionHandlerInterface;

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