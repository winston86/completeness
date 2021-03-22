<?php
declare(strict_types=1);

namespace CompletenessModule\Migrations;

use Treo\Core\Migration\Base;

/**
 * Migration class for version 3.22.4
 *
 * @author r.ratsun@treolabs.com
 */
class V1Dot0Dot1 extends Base
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        //create jobs
        $this->getPDO()->exec("INSERT INTO scheduled_job (id, name, job, status, scheduling, is_internal) VALUES ('CompletenessModuleCheckCategorySet','CompletenessModule/Check Category Setup','CompletenessModuleCheckCategorySet','Active','0 0 * * *',0)");
        $this->getPDO()->exec("INSERT INTO scheduled_job (id, name, job, status, scheduling, is_internal) VALUES ('CompletenessModuleCheckChannelSet','CompletenessModule/Check Channel Setup','CompletenessModuleCheckChannelSet','Active','0 0 * * *',0)");
        $this->getPDO()->exec("INSERT INTO scheduled_job (id, name, job, status, scheduling, is_internal) VALUES ('CompletenessModuleCheckDuplicatedMPN','CompletenessModule/Check Duplicated MPN\'s','CompletenessModuleCheckDuplicatedMPN','Active','0 0 * * *',0)");
        $this->getPDO()->exec("INSERT INTO scheduled_job (id, name, job, status, scheduling, is_internal) VALUES ('CompletenessModuleCheckDuplicatedSKU','CompletenessModule/Check Duplicated SKU\'s','CompletenessModuleCheckDuplicatedSKU','Active','0 0 * * *',0)");
        $this->getPDO()->exec("INSERT INTO scheduled_job (id, name, job, status, scheduling, is_internal) VALUES ('CompletenessModuleCheckDuplicatedTitle','CompletenessModule/Check Duplicated Title\'s','CompletenessModuleCheckDuplicatedTitle','Active','0 0 * * *',0)");
        $this->getPDO()->exec("INSERT INTO scheduled_job (id, name, job, status, scheduling, is_internal) VALUES ('CompletenessModuleCheckEmpty','CompletenessModule/Check Required Attributes (is empty)','CompletenessModuleCheckEmpty','Active','0 0 * * *',0)");
        $this->getPDO()->exec("INSERT INTO scheduled_job (id, name, job, status, scheduling, is_internal) VALUES ('CompletenessModuleCheckImage','CompletenessModule/Check Image','CompletenessModuleCheckImage','Active','0 0 * * *',0)");
        $this->getPDO()->exec("INSERT INTO scheduled_job (id, name, job, status, scheduling, is_internal) VALUES ('CompletenessModuleCheckMissedMPN','CompletenessModule/Check Missed MPN\'s,'CompletenessModuleCheckMissedMPN','Active','0 0 * * *',0)");
        $this->getPDO()->exec("INSERT INTO scheduled_job (id, name, job, status, scheduling, is_internal) VALUES ('CompletenessModuleCheckMissedSKU','CompletenessModule/Check Missed SKU\'s,'CompletenessModuleCheckMissedSKU','Active','0 0 * * *',0)");
        $this->getPDO()->exec("INSERT INTO scheduled_job (id, name, job, status, scheduling, is_internal) VALUES ('CompletenessModuleCheckSpelling','CompletenessModule/Spelling Check','Active','0 0 * * *',0)");
    }

    /**
     * @inheritDoc
     */
    public function down(): void
    {
        // delete jobs
        $this->getPDO()->exec("delete from scheduled_job where job like '%CompletenessModule'");
    }
}