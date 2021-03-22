<?php
/**
 * Pim
 * Free Extension
 * Copyright (c) TreoLabs GmbH
 * Copyright (c) Kenner Soft Service GmbH
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace CompletenessModule\Repositories;

use Espo\Core\Exceptions\BadRequest;
use Espo\ORM\Entity;
use Treo\Core\Utils\Util;

/**
 * Class CompletenessModuleError
 *
 * @author r.ratsun@treolabs.com
 */
class CompletenessModuleError extends AbstractRepository
{
    /**
     * @var array
     */
    private $sqlItems = [];


    /**
     * @return array
     */
    public function getInputLanguageList(): array
    {
        return $this->getConfig()->get('inputLanguageList', []);
    }

    /**
     * @inheritDoc
     *
     * @throws BadRequest
     */
    public function beforeSave(Entity $entity, array $options = [])
    {
        parent::beforeSave($entity, $options);

        // exit
        if (!empty($options['skipValidation'])) {
            return true;
        }

        // is valid
        $this->isValid($entity);

        // clearing channels ids
        if ($entity->get('scope') == 'Global') {
            $entity->set('channelsIds', []);
        }
    }

    /**
     * @inheritDoc
     */
    public function afterSave(Entity $entity, array $options = [])
    {
        // update product attribute values
        $this->updateProductAttributeValues($entity);

        parent::afterSave($entity, $options);
    }

    /**
     * @inheritDoc
     */
    public function afterRemove(Entity $entity, array $options = [])
    {
        $this
            ->getEntityManager()
            ->getRepository('ProductAttributeValue')
            ->removeCollectionByProductFamilyAttribute($entity->get('id'));

        parent::afterRemove($entity, $options);
    }

    /**
     * @param Entity $entity
     *
     * @throws BadRequest
     */
    protected function isUnique(Entity $entity, string $field = ''): void
    {
        if (!$this->isUnique($entity, $field)) {
            throw new BadRequest($this->exception('Such record already exists'));
        }
    }

    /**
     * @inheritDoc
     */
    protected function init()
    {
        $this->addDependency('language');
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function exception(string $key): string
    {
        return $this->getInjection('language')->translate($key, 'exceptions', 'CompletenessModuleError');
    }

    /**
     * @param string $sql
     */
    private function pushSql(string $sql): void
    {
        $this->sqlItems[] = $sql;

        if (count($this->sqlItems) > 3000) {
            $this->executeSqlItems();
            $this->sqlItems = [];
        }
    }

    /**
     * Execute SQL items
     */
    private function executeSqlItems(): void
    {
        if (!empty($this->sqlItems)) {
            $this->getEntityManager()->nativeQuery(implode(';', $this->sqlItems));
        }
    }
}
