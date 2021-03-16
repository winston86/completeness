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

Espo.define('completeness:views/header', 'class-replace!completeness:views/header',
    Dep => Dep.extend({

        setup() {
            if (this.model && !this.model.isNew() && this.getMetadata().get(['scopes', this.model.name, 'advancedFilters']) &&
                !this.baseOverviewFilters.find(filter => filter.name === 'errorsFilter') && this.model.name === 'Product' &&
                this.getAcl().check('ProductAttributeValue', 'read') && this.getAcl().check('Error', 'read')) {
                this.baseOverviewFilters.push({
                    name: 'errorsFilter',
                    view: 'completeness:views/fields/overview-errors-filter'
                });
            }

            Dep.prototype.setup.call(this);
        }

    })
);
