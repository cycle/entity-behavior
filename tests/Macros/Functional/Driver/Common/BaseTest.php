<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Macros\Tests\Functional\Driver\Common;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\Config\DriverConfig;
use Cycle\Database\Database;
use Cycle\Database\DatabaseManager;
use Cycle\Database\Driver\DriverInterface;
use Cycle\Database\Driver\Handler;
use Cycle\ORM\Entity\Macros\Tests\Traits\Loggable;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\ORM;
use Cycle\ORM\Transaction;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    use Loggable;

    public const DRIVER = null;

    public static array $config;
    protected ?DatabaseManager $dbal = null;
    protected ?ORM $orm = null;
    private static array $memoizedDrivers = [];

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

        $this->orm = null;
        $this->dbal = null;

        if (\function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }

    /**
     * @param array{readonly: bool} $options
     *
     * @return DriverInterface
     */
    private function getDriver(array $options = []): DriverInterface
    {
        $hash = \hash('crc32', static::DRIVER . ':' . \json_encode($options));

        if (! isset(self::$memoizedDrivers[$hash])) {
            /** @var DriverConfig $config */
            $config = clone self::$config[static::DRIVER];

            // Add readonly options support
            if (isset($options['readonly']) && $options['readonly'] === true) {
                $config->readonly = true;
            }

            $driver = $config->driver::create($config);

            $this->setUpLogger($driver);

            self::$memoizedDrivers[$hash] = $driver;
        }

        return self::$memoizedDrivers[$hash];
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

    public function withSchema(SchemaInterface $schema): ORM
    {
        $this->orm = $this->orm->withSchema($schema);

        return $this->orm;
    }

    protected function getDatabase(): Database
    {
        return $this->dbal->database('default');
    }

    protected function save(object ...$entities): void
    {
        $tr = new Transaction($this->orm);
        foreach ($entities as $entity) {
            $tr->persist($entity);
        }
        $tr->run();
    }
}
