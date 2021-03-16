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

namespace Completeness;

use Espo\Core\Utils\Json;
use Treo\Core\ModuleManager\AbstractModule;
use Treo\Core\Utils\Config;
use Treo\Core\Utils\Util;

/**
 * Class Module
 *
 * @author r.ratsun <r.ratsun@treolabs.com>
 */
class Module extends AbstractModule
{
    /**
     * @var array
     */
    public static $multiLangTypes
        = [
            'bool',
            'enum',
            'multiEnum',
            'varchar',
            'text',
            'wysiwyg'
        ];

    /**
     * @inheritdoc
     */
    public static function getLoadOrder(): int
    {
        return 5120;
    }

    /**
     * @inheritDoc
     */
    public function loadMetadata(\stdClass &$data)
    {
        parent::loadMetadata($data);

        $this->setLocalesToChannels($data);

        // prepare result
        $result = Json::decode(Json::encode($data), true);

        // prepare attribute scope
        $result = $this->attributeScope($result);

        // set data
        $data = Json::decode(Json::encode($result));
    }

    /**
     * @param array $result
     *
     * @return array
     */
    protected function attributeScope(array $result): array
    {
       

        return $result??null;
    }

    /**
     * @return array
     */
    protected function getInputLanguageList(): array
    {
        $result = [];

        /** @var Config $config */
        $config = $this->container->get('config');

        if ($config->get('isMultilangActive', false)) {
            foreach ($config->get('inputLanguageList', []) as $locale) {
                $result[$locale] = ucfirst(Util::toCamelCase(strtolower($locale)));
            }
        }

        return $result;
    }

    /**
     * @param \stdClass $metadata
     */
    protected function setLocalesToChannels(\stdClass &$metadata)
    {
        // prepare result
        $data = Json::decode(Json::encode($metadata), true);

        /** @var Config $config */
        $config = $this->container->get('config');

        if ($config->get('isMultilangActive', false)) {
            $data['entityDefs']['Channel']['fields']['locales']['options'] = $config->get('inputLanguageList', []);
        }

        // set data
        $metadata = Json::decode(Json::encode($data));
    }
}
