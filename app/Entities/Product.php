<?php
/**
 * Pim
 * Free Extension
 * Copyright (c) TreoLabs GmbH
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

namespace CompletenessModule\Entities;

use Espo\Core\Exceptions\Error;
use Espo\Core\Utils\Util;
use Pim\Entities\Product as PimProduct;
use Espo\ORM\EntityCollection;

use Mekras\Speller\Hunspell\Hunspell;
use Mekras\Speller\Source\StringSource;

/**
 * Product entity
 *
 * @author r.ratsun@treolabs.com
 */
class Product extends PimProduct
{
    /**
     * @var array
     */
    public $totalQuality;
    public $completeAttribute;

    public static $errors_check_types = [
        
    ];

    function __construct(){
        parent::__construct($defs = array(), EntityManager $entityManager = null);

        // (calculated as following: (all attributes) - 4 (multilanguage
        // attributes, for example) + 4(multilanguage attributes)*2
        // (Count of language) +0 count of image*6(type
        // errors for image)+1(Missed image)+1 (Product
        // assigned to Category) + 1 (Product assigned to
        // Channel) = 47

        $totalAttrsValues = 0;
        $missed_required = 0;
        $missed_optional = 0;
        $spell_check_mistakes = [];
        $image_is_miss = [];
        $completeAttribute = 0;

        $inputLanguageList = $this
            ->getEntityManager()
            ->getRepository($this->getEntityType())
            ->getInputLanguageList();

        $lang_count = count($inputLanguageList);

        $multilang_attrs = $this
            ->getEntityManager()
            ->getRepository('Attribute')
            ->join('ProductAttributeValue')
            ->where([
                'ProductAttributeValue.productId' => $this->get('id'),
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
                'ProductAttributeValue.productId' => $this->get('id'),
                'ProductAttributeValue.attributeId' => 'Attribute.id',
                'Attribute.isMultilang' => 0
            ])
            ->find()
            ->toArray();

        $totalAttrsValues += count($multilang_attrs) * $lang_count + count($onelang_attrs);

        $image_count = count($this->get('pimImages')) * 6;
        
        // image_count + 1(Missed image)+1 (Product  assigned to Category) 
        // + 1 (Product assigned to Channel)
        $totalAttrsValues += $image_count + 3;

        foreach ($multilang_attrs as $key => $attr) {
            $tmp_count = 0;
            foreach ($inputLanguageList as $key => $lang) {
                $value = $this->getProductAttributeValue($attr->id, $lang);
                if (empty($value) || $value == '[]') {
                    $tmp_count++;
                }
            }
            if (!empty($attr->isRequired)) {
                $missed_required += $tmp_count;
            }else{
                $missed_optional += $tmp_count;
            }
        }

        foreach ($onelang_attrs as $key => $attr) {
            $value = $this->getProductAttributeValue($attr->id);
            if (empty($value) || $value == '[]') {
                $missed= true;
            }
            if ($attr->isRequired && $missed) {
                $missed_required++;
            }elseif($missed){
                $missed_optional++;
            }else{
                $speller = new Hunspell();
                $issues = $speller->checkText($source, ['en_GB', 'en']);

                if (!empty($issues)) {
                    foreach ($issues as $key => $issue) {
                        $word = $issue->word; 
                        $line = $issue->line; 
                        $offset = $issue->offset; 
                        $suggestions = implode(',', $issue->suggestions);
                    }
                }

                $issue_info =  "{$word}:{$line}:$offset {$suggestions}";
                $spell_errors[] = $issue_info;
            }
        }

        $totalQuality = ;
        $completeAttribute =

        $this->set('totalQuality', $totalQuality);
        $this->set('completeAttribute', $completeAttribute);
    }

}
