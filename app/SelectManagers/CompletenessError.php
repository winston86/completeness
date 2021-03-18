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

namespace Completeness\SelectManagers;

use Completeness\Core\SelectManagers\AbstractSelectManager;

/**
 * Class ProductFamilyAttribute
 *
 * @author r.zablodskiy@treolabs.com
 */
class CompletenessError extends AbstractSelectManager
{
   protected function boolFilterNotLinkedWithProduct(&$result)
    {

        $completenessErrorIds = $this->getEntityManager()
            ->getRepository('CompletenessError')
            ->select(['id'])
            ->join(['products'])
            ->where([
                'products.Id' => (string)$this->getSelectCondition('notLinkedWithProduct'),
            ])
            ->find()
            ->toArray();

        $result['whereClause'][] = [
            'id!=' => array_column($completenessErrorIds, 'id')
        ];
    }
}
