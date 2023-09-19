<?php declare(strict_types=1);

namespace Elio\TestPlugin\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1694699142FastOrderItems extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1694699142;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
            CREATE TABLE IF NOT EXISTS `elio_fast_order_line_item` (
                `id` BINARY(16) NOT NULL,
                `dateTime` DATETIME(3) NULL,
                `sessionId` VARCHAR(255) NULL,
                `quantity` INT(11) NULL,
                `comment` LONGTEXT DEFAULT NULL,
                `product_id` BINARY(16) NOT NULL,
                `product_version_id` BINARY(16) NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `fk.elio_fast_order_line_item.product_id` 
                    FOREIGN KEY (`product_id`,`product_version_id`) REFERENCES `product` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        $sql = <<<SQL
            DROP TABLE IF EXISTS `elio_fast_order_line_item`;
SQL;
        $connection->executeStatement($sql);
    }
}