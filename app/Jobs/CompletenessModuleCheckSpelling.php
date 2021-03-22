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

use Mekras\Speller\Hunspell\Hunspell;
use Mekras\Speller\Source\StringSource;

/**
 * Class CompletenessModuleCheckSpelling
 *
 * @author r.ratsun@treolabs.com
 */
class CompletenessModuleCheckSpelling extends Base
{
    /**
     * Run job
     *
     * @return bool
     */
    public function run()
    {
        $inputLanguageList = $this
            ->getEntityManager()
            ->getRepository($this->getEntityType())
            ->getInputLanguageList();

        $multilang_attrs = $this
            ->getEntityManager()
            ->getRepository('Attribute')
            ->join('ProductAttributeValue')
            ->where([
                'ProductAttributeValue.attributeId' => 'Attribute.id',
                'Attribute.isMultilang' => 0
            ])
            ->find()
            ->toArray();

        $onelang_attrs = $this
            ->getEntityManager()
            ->getRepository('Attribute')
            ->join('ProductAttributeValue')
            ->where([
                'ProductAttributeValue.attributeId' => 'Attribute.id',
                'Attribute.isMultilang' => 0
            ])
            ->find()
            ->toArray();

        $speller = new Hunspell();

        foreach ($multilang_attrs as $key => $attr) {
            
            foreach ($inputLanguageList as $key => $lang) {
                $issues = $speller->checkText($this->getProductAttributeValue($attr->id, $lang), [$lang, explode("_", $lang)[0]]);

                if (!empty($issues)) {
                    foreach ($issues as $key => $issue) {
                        $word = $issue->word; 
                        $line = $issue->line; 
                        $offset = $issue->offset; 
                        $suggestions = implode(',', $issue->suggestions);
                    }
                }

                $issue_info =  "{$attr->get('name')} > $lang {$word}:{$line}:$offset {$suggestions}";
                $spell_errors[$lang][$attr->get('product_id')][$attr->get('id')] = $issue_info;
            }

        }

        foreach ($onelang_attrs as $key => $attr) {
            $lang = $this->getConfig()->get('language', []);
            $issues = $speller->checkText($this->getProductAttributeValue($attr->id), [$lang, explode("_", $lang)[0]]);

            if (!empty($issues)) {
                foreach ($issues as $key => $issue) {
                    $word = $issue->word; 
                    $line = $issue->line; 
                    $offset = $issue->offset; 
                    $suggestions = implode(',', $issue->suggestions);
                }
            }

            $issue_info =  "{$attr->get('name')}  {$word}:{$line}:$offset {$suggestions}";
            $spell_errors['---'][$attr->get('product_id')][$attr->get('id')] = $issue_info;
        }

        return true;
    }

    private function addCompletenessModuleErrors($items){
        foreach ($items as $key => $error_fields) {
            $CompletenessModuleErrorEntity = $this->getEntityManager()->getEntity('CompletenessModuleError');
            foreach ($error_fields as $key => $value) {
                $CompletenessModuleErrorEntity->set($key, $value);
            }
            $this->getEntityManager()->saveEntity($CompletenessModuleErrorEntity, ['skipAfterSave' => true]);
        }
    }
}
