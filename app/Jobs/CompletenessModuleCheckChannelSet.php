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

namespace CompletenessModule\Jobs;

use Espo\Core\Jobs\Base;

/**
 * Class CompletenessModuleCheckChannelSet
 *
 * @author r.ratsun@treolabs.com
 */
class CompletenessModuleCheckChannelSet extends Base
{
    static $error_type = 'ChannelNotSet';
    static $severity  = 'Low';

    /**
     * Run job
     *
     * @return bool
     */
    public function run()
    {
        $products = $this
            ->getEntityManager()
            ->getRepository('Product')
            ->leftJoin('ProductChannel')
            ->where([
                'Product.deleted' => 0,
                'ProductChannel.product_id' => 'Product.id',
                'ProductChannel.id' => null
            ])
            ->find()
            ->toArray();

        $items = [];

        foreach ($products as $key => $product) {

           $item['severity']   = $this->severity;
           $item['name']       = $this->getInjection('language')->translate('Missed Channel', 'names', 'CompletenessModuleError');
           $item['product_id'] = $product->get('id');
           $item['error']      = $this->error_type;
           $item['message'] = sprintf($this->getInjection('language')->translate('Missed Channel in %s', 'messages', 'CompletenessModuleError'), $product->get('id'));

           $items[] = $item;

        }

        $this->addCompletenessModuleErrors($items);

        return true;
    }

    private function addCompletenessModuleErrors($items)
    {
        foreach ($items as $key => $error_fields) {
            $CompletenessModuleErrorEntity = $this->getEntityManager()->getEntity('CompletenessModuleError');
            foreach ($error_fields as $key => $value) {
                $CompletenessModuleErrorEntity->set($key, $value);
            }
            $this->getEntityManager()->saveEntity($CompletenessModuleErrorEntity, ['skipAfterSave' => true]);
        }
    }

}
