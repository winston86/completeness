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

namespace Completeness\Services;

use Espo\Core\Templates\Repositories\Base as BaseRepository;
use Espo\Core\Services\Base;
use Treo\Services\DashletInterface;

/**
 * Class AbstractDashletService
 *
 * @author r.ratsun <r.ratsun@treolabs.com>
 */
abstract class AbstractDashletService extends Base implements DashletInterface
{
    /**
     * Get PDO
     *
     * @return \PDO
     */
    protected function getPDO(): \PDO
    {
        return $this->getEntityManager()->getPDO();
    }

    /**
     * Get Repository
     *
     * @param $entityType
     *
     * @return BaseRepository
     */
    protected function getRepository(string $entityType): BaseRepository
    {
        return $this->getEntityManager()->getRepository($entityType);
    }
}
