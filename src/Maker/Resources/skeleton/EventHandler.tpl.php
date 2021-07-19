<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use VXM\Hasura\Attribute\AsEventHandler;
use VXM\Hasura\Handler\EventHandlerInterface;

#[AsEventHandler(name: <?= "'$event'"; ?>)]
final class <?= $class_name ?> implements EventHandlerInterface
{
    /**
    * {@inheritdoc}
    */
    public function handle(string $name, string $id, array $table, array $event, string $createdAt, array $deliveryInfo): void
    {
        // Handle event logic...
    }
}