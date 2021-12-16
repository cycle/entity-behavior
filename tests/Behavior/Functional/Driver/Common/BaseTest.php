<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\Database;
use Cycle\Database\DatabaseManager;
use Cycle\Database\Driver\DriverInterface;
use Cycle\Database\Driver\Handler;
use Cycle\ORM\Entity\Behavior\Tests\Traits\Loggable;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    use Loggable;

    public const DRIVER = null;

    public static array $config;
    protected ?DatabaseManager $dbal = null;
    private static array $driverCache = [];

    public function setUp(): void
    {
        if (self::$config['debug'] ?? false) {
            $this->enableProfiling();
        }

        $this->dbal = new DatabaseManager(new DatabaseConfig());
        $this->dbal->addDatabase(
            new Database(
                'default',
                '',
                $this->getDriver()
            )
        );
    }

    public function tearDown(): void
    {
        $this->dropDatabase($this->dbal->database('default'));

        $this->dbal = null;

        if (\function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }

    public function getDriver(): DriverInterface
    {
        if (isset(static::$driverCache[static::DRIVER])) {
            return static::$driverCache[static::DRIVER];
        }

        $config = self::$config[static::DRIVER];
        if (!isset($this->driver)) {
            $this->driver = $config->driver::create($config);
        }

        return static::$driverCache[static::DRIVER] = $this->driver;
    }

    protected function dropDatabase(Database $database = null): void
    {
        if ($database === null) {
            return;
        }

        foreach ($database->getTables() as $table) {
            $schema = $table->getSchema();

            foreach ($schema->getForeignKeys() as $foreign) {
                $schema->dropForeignKey($foreign->getColumns());
            }

            $schema->save(Handler::DROP_FOREIGN_KEYS);
        }

        foreach ($database->getTables() as $table) {
            $schema = $table->getSchema();
            $schema->declareDropped();
            $schema->save();
        }
    }

    protected function getDatabase(): Database
    {
        return $this->dbal->database('default');
    }
}
