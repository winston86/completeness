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
        //create jobs
        $this->getPDO()->exec("INSERT INTO scheduled_job (id, name, job, status, scheduling, is_internal) VALUES ('CompletenessCheckCategorySet','Completeness/Check Category Setup','CompletenessCheckCategorySet','Active','0 0 * * *',0)");
        $this->getPDO()->exec("INSERT INTO scheduled_job (id, name, job, status, scheduling, is_internal) VALUES ('CompletenessCheckChannelSet','Completeness/Check Channel Setup','CompletenessCheckChannelSet','Active','0 0 * * *',0)");
        $this->getPDO()->exec("INSERT INTO scheduled_job (id, name, job, status, scheduling, is_internal) VALUES ('CompletenessCheckDuplicatedMPN','Completeness/Check Duplicated MPN\'s','CompletenessCheckDuplicatedMPN','Active','0 0 * * *',0)");
        $this->getPDO()->exec("INSERT INTO scheduled_job (id, name, job, status, scheduling, is_internal) VALUES ('CompletenessCheckDuplicatedSKU','Completeness/Check Duplicated SKU\'s','CompletenessCheckDuplicatedSKU','Active','0 0 * * *',0)");
        $this->getPDO()->exec("INSERT INTO scheduled_job (id, name, job, status, scheduling, is_internal) VALUES ('CompletenessCheckDuplicatedTitle','Completeness/Check Duplicated Title\'s','CompletenessCheckDuplicatedTitle','Active','0 0 * * *',0)");
        $this->getPDO()->exec("INSERT INTO scheduled_job (id, name, job, status, scheduling, is_internal) VALUES ('CompletenessCheckEmpty','Completeness/Check Required Attributes (is empty)','CompletenessCheckEmpty','Active','0 0 * * *',0)");
        $this->getPDO()->exec("INSERT INTO scheduled_job (id, name, job, status, scheduling, is_internal) VALUES ('CompletenessCheckImage','Completeness/Check Image','CompletenessCheckImage','Active','0 0 * * *',0)");
        $this->getPDO()->exec("INSERT INTO scheduled_job (id, name, job, status, scheduling, is_internal) VALUES ('CompletenessCheckMissedMPN','Completeness/Check Missed MPN\'s,'CompletenessCheckMissedMPN','Active','0 0 * * *',0)");
        $this->getPDO()->exec("INSERT INTO scheduled_job (id, name, job, status, scheduling, is_internal) VALUES ('CompletenessCheckMissedSKU','Completeness/Check Missed SKU\'s,'CompletenessCheckMissedSKU','Active','0 0 * * *',0)");
        $this->getPDO()->exec("INSERT INTO scheduled_job (id, name, job, status, scheduling, is_internal) VALUES ('CompletenessCheckSpelling','Completeness/Spelling Check','Active','0 0 * * *',0)");
    }

    /**
     * @inheritDoc
     */
    public function down(): void
    {
        // delete jobs
        $this->getPDO()->exec("delete from scheduled_job where job='CompletenessCheckCategorySet'");
        $this->getPDO()->exec("delete from scheduled_job where job='CompletenessCheckChannelSet'");
        $this->getPDO()->exec("delete from scheduled_job where job='CompletenessCheckDuplicatedMPN'");
        $this->getPDO()->exec("delete from scheduled_job where job='CompletenessCheckDuplicatedSKU'");
        $this->getPDO()->exec("delete from scheduled_job where job='CompletenessCheckDuplicatedTitle'");
        $this->getPDO()->exec("delete from scheduled_job where job='CompletenessCheckEmpty'");
        $this->getPDO()->exec("delete from scheduled_job where job='CompletenessCheckImage'");
        $this->getPDO()->exec("delete from scheduled_job where job='CompletenessCheckMissedMPN'");
        $this->getPDO()->exec("delete from scheduled_job where job='CompletenessCheckMissedSKU'");
        $this->getPDO()->exec("delete from scheduled_job where job='CompletenessCheckSpelling'");
    }
}