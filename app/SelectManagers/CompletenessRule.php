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

namespace Completeness\SelectManagers;

use Completeness\Core\SelectManagers\AbstractSelectManager;

/**
 * Class of CompletenessRule
 *
 * @author r.ratsun <r.ratsun@treolabs.com>
 */
class CompletenessRule extends AbstractSelectManager
{

    /**
     * NotLinkedWithAttribute filter
     *
     * @param array $result
     */
    protected function boolFilterNotLinkedWithError(&$result)
    {

        $completenessRuleIds = $this->getEntityManager()
            ->getRepository('CompletenessRule')
            ->select(['id'])
            ->join(['products'])
            ->where([
                'products.Id' => (string)$this->getSelectCondition('notLinkedWithProduct'),
            ])
            ->find()
            ->toArray();

        $result['whereClause'][] = [
            'id!=' => array_column($completenessRuleIds, 'id')
        ];
    }
}