<?php
declare(strict_types=1);

namespace Completeness\Migrations;

use Treo\Core\Migration\Base;

/**
 * Migration class for version 3.22.4
 *
 * @author r.ratsun@treolabs.com
 */
class V1Dot0Dot0 extends Base
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->getPDO()->exec("create table completeness_rule (
            `id` varchar(24) NOT NULL,
            `name` varchar(255),
            `delete` tinyint(1),
            `description` mediumtext,
            `is_active` tinyint(1) NOT NULL,
            `created_at` datetime,
            `modified_at` datetime,
            `created_by_id` varchar(24),
            `modified_by_id` varchar(24),
            `name_en_us` varchar(255),
            `description_en_us` mediumtext,
            `name_de_de` varchar(255),
            `description_de_de` mediumtext,
            `regexp` varchar(255),
            `owner_user_id` varchar(24),
            `assigned_user_id` varchar(24),
            PRIMARY KEY (id)
        )");
        $this->getPDO()->exec("create table completeness_error (
            `id` varchar(24) NOT NULL,
            `name` varchar(255),
            `name_en_us` varchar(255),
            `name_de_de` varchar(255),
            `completeness_rule_id` varchar(24),
            `product_id`  varchar(24),
            `delete` tinyint(1),
            `created_at` datetime,
            PRIMARY KEY (id)
        )");
    }

    /**
     * @inheritDoc
     */
    public function down(): void
    {
        // delete CoreUpgrade job
        $this->getPDO()->exec("drop table if exists completeness_rule");
        $this->getPDO()->exec("drop table if exists completeness_error");
    }
}
