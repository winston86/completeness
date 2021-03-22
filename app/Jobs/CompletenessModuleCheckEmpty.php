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

declare (strict_types = 1);

namespace CompletenessModule\Jobs;

use Espo\Core\Jobs\Base;

/**
 * Class CompletenessModuleCheckEmpty
 *
 * @author r.ratsun@treolabs.com
 */
class CompletenessModuleCheckEmpty extends Base
{

    static $error_type_required = 'EmptyRequired';
    static $error_type_optional = 'EmptyOptional';
    static $severity_required   = 'High';
    static $severity_optional   = 'Low';

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
            ->where([
                'deleted' => 0,
            ])
            ->find()
            ->toArray();

        $missed_required = [];
        $missed_optional = [];

        foreach ($products as $key => $product) {

            $inputLanguageList = $product->getInputLanguageList();

            $multilang_attrs = $product
                ->getRepository('Attribute')
                ->join('ProductAttributeValue')
                ->where([
                    'ProductAttributeValue.productId'   => $product->get('id'),
                    'ProductAttributeValue.attributeId' => 'Attribute.id',
                    'Attribute.isMultilang'             => 1,
                ])
                ->find()
                ->toArray();

            $onelang_attrs = $product
                ->getRepository('Attribute')
                ->join('ProductAttributeValue')
                ->where([
                    'ProductAttributeValue.productId'   => $this->get('id'),
                    'ProductAttributeValue.attributeId' => 'Attribute.id',
                    'Attribute.isMultilang'             => 0,
                ])
                ->find()
                ->toArray();

            foreach ($multilang_attrs as $key => $attr) {
                $tpm = $this->checkOneLangAttr($product, $attr);
                $tpm = $this->checkMultiLangAttr($product, $attr, $tmp, $inputLanguageList);
                $this->fillMissed($missed_required, $missed_optional, $attr, $tmp);
            }

            foreach ($onelang_attrs as $key => $attr) {
                $tpm = $this->checkOneLangAttr($product, $attr);
                $this->fillMissed($missed_required, $missed_optional, $attr, $tmp);

            }

        }

        foreach (array_merge($missed_optional, $missed_required) as $key => $error_fields) {
            $CompletenessModuleErrorEntity = $this->getEntityManager()->getEntity('CompletenessModuleError');
            foreach ($error_fields as $key => $value) {
                $CompletenessModuleErrorEntity->set($key, $value);
            }
            $this->getEntityManager()->saveEntity($CompletenessModuleErrorEntity, ['skipAfterSave' => true]);
        }

        return true;
    }

    private function checkMultiLangAttr($product, $attr, &$tmp, $inputLanguageList)
    {
        foreach ($inputLanguageList as $key => $lang) {
            $value = $product->getProductAttributeValue($attr->id, $lang);
            if (empty($value) || $value == '[]') {
                $tmp[] = [
                    'message' => sprintf(
                        $attr->isRequired
                        ?
                        $this->getInjection('language')->translate('Missed required attribute %s > %s', 'messages', 'CompletenessModuleError')
                        :
                        $this->getInjection('language')->translate('Missed attribute %s > %s', 'messages', 'CompletenessModuleError'),

                        $attr->get('name'), $lang),
                ];
            }
        }
    }

    private function fillMissed(&$missed_required, &$missed_optional, $attr, $tmp)
    {
        if (!empty($attr->isRequired)) {
            array_walk($tmp, function (&$item) {
                $item['severity']   = $this->severity_required;
                $item['name']       = $this->getInjection('language')->translate('Missed required attribute', 'names', 'CompletenessModuleError');
                $item['product_id'] = $product->get('id');
                $item['error']      = $this->error_type_required;
            });
            $missed_required = array_merge($tmp, $missed_required);
        } else {
            array_walk($tmp, function (&$item) {
                $item['severity']   = $this->severity_optional;
                $item['name']       = $this->getInjection('language')->translate('Missed attribute', 'names', 'CompletenessModuleError');
                $item['product_id'] = $product->get('id');
                $item['error']      = $this->error_type_optional;
            });
            $missed_optional = array_merge($tmp, $missed_optional);
        }
    }

    private function checkOneLangAttr($product, $attr)
    {
        $tmp   = [];
        $value = $product->getProductAttributeValue($attr->id);
        if (empty($value) || $value == '[]') {
            $tmp[] = [
                'message' => sprintf(
                    $attr->isRequired
                    ?
                    $this->getInjection('language')->translate('Missed required attribute %s > %s', 'messages', 'CompletenessModuleError')
                    :
                    $this->getInjection('language')->translate('Missed attribute %s > %s', 'messages', 'CompletenessModuleError'),

                    $attr->get('name'), '---'),
            ];
        }

        return $tmp;
    }

}
