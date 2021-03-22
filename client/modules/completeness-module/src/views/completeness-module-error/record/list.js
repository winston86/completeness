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

Espo.define('completeness-module:views/completeness-module-error/record/list', 'completeness-module:views/record/list',
    Dep => Dep.extend({

        rowActionsView: 'completeness-module:views/completeness-module-error/record/row-actions/optioned-remove',

        massActionRemove() {
            if (!this.allResultIsChecked && this.checkedList && this.checkedList.length) {
                let isSystemInSelected = this.checkedList.some(item => {
                    let model = this.collection.get(item);
                    return model && model.get('isSystem');
                });
                if (isSystemInSelected) {
                    Espo.Ui.warning(this.translate("Can't remove Completeness Error", 'messages', this.scope));
                    return;
                }
            }

            Dep.prototype.massActionRemove.call(this);
        }

    })
);