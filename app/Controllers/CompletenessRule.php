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

namespace Completeness\Controllers;

use Espo\Core\Exceptions;
use Slim\Http\Request;

/**
 * Class CompletenessRule
 *
 * @author r.ratsun@treolabs.com
 */
class CompletenessRule extends AbstractController
{
    /**
     * Get count not empty product family attributes
     *
     * @ApiDescription(description="Get products count, linked with product family attribute")
     * @ApiMethod(type="GET")
     * @ApiRoute(name="/ProductFamily/{product_family_id}/productAttributesCount")
     * @ApiParams(name="product_family_id", type="string", is_required=1, description="ProductFamily id")
     * @ApiReturn(sample="'int'")
     *
     * @param array   $params
     * @param array   $data
     * @param Request $request
     *
     * @return int
     *
     * @throws Exceptions\BadRequest
     * @throws Exceptions\Forbidden
     */
    public function actionProductsCount($params, $data, Request $request)
    {
        if (!$request->isGet()) {
            throw new Exceptions\BadRequest();
        }

        if (!$this->getAcl()->check($this->name, 'read')) {
            throw new Exceptions\Forbidden();
        }

        return $this
            ->getEntityManager()
            ->getRepository('Product')
            ->join('completenessRule')
            ->join('completenessError')
            ->where([
                'completenessRule.id' => $request->get('attributeId'), 
                'completenessError.completeness_rule_id' => 'completenessRule.id',
                'product.id' => 'completenessError.product_id',
                'product.deleted' => 0],
            )
            ->count();
    }
}
