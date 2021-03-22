/*
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

Espo.define('completeness-module:views/header', 'class-replace!completeness-module:views/header',
    Dep => Dep.extend({

        setup() {
        
            this.baseOverviewFilters.push({
                name: 'errorsFilter',
                view: 'completeness-module:views/fields/overview-errors-filter'
            });
        
            Dep.prototype.setup.call(this);
        },
        data() {
            var data = Dep.prototype.data.call(this);
            
            if (this.isCompletenessIFace()) {
                data.items.buttons = [];
                data.isHeaderAdditionalSpace = false;
            }
            
            return data;
        },
        isCompletenessIFace() {
            return (this.collection.name === 'CompletenessError');
        }

    })
);
