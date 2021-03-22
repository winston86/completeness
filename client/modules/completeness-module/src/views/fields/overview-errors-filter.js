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

Espo.define('completeness-module:views/fields/overview-errors-filter', 'treo-core:views/fields/dropdown-enum',
    Dep => Dep.extend({

        errors: [],

        optionsList: [
            {
                name: '',
                selectable: true
            },
            {
                name: 'onlyGlobalScope',
                selectable: true
            }
        ],

        setup() {
            this.baseOptionList = Espo.Utils.cloneDeep(this.optionsList);
            this.wait(true);
            this.updateErrors(() => this.wait(false));

            this.listenTo(this.model, 'after:relate after:unrelate', link => {
                if (link === 'errors') {
                    this.updateErrors(() => this.reRender());
                }
            });

            Dep.prototype.setup.call(this);
        },

        updateErrors(callback) {
            this.errors = [];
            this.optionsList = Espo.Utils.cloneDeep(this.baseOptionList);
            const collectionParams = this.getMetadata().get(['entityDefs', 'Error', 'collection']) || {};
            const sortBy = collectionParams.sortBy || 'createdAt';
            const asc = collectionParams.asc || false;
            this.getFullEntityList(`Product/${this.model.id}/errors`, {
                sortBy: sortBy, asc: asc, select: 'name'
            }, list => {
                this.setErrorsFromList(list);
                this.prepareOptionsList();
                this.updateSelected();
                this.modelKey = this.options.modelKey || this.modelKey;
                this.setDataToModel({[this.name]: this.selected});
                callback();
            });
        },

        updateSelected() {
            if (this.storageKey) {
                let selected = ((this.getStorage().get(this.storageKey, this.scope) || {})[this.name] || {}).selected;
                if (this.optionsList.find(option => option.name === selected)) {
                    this.selected = selected;
                }
            }
            this.selected = this.selected || (this.optionsList.find(option => option.selectable) || {}).name;
        },

        getFullEntityList(url, params, callback, container) {
            if (url) {
                container = container || [];

                let options = params || {};
                options.maxSize = options.maxSize || 200;
                options.offset = options.offset || 0;

                this.ajaxGetRequest(url, options).then(response => {
                    container = container.concat(response.list || []);
                    options.offset = container.length;
                    if (response.total > container.length || response.total === -1) {
                        this.getFullEntity(url, options, callback, container);
                    } else {
                        callback(container);
                    }
                });
            }
        },

        setErrorsFromList(list) {
            list.forEach(item => {
                if (!this.errors.find(error => error.id === item.id)) {
                    this.errors.push({
                        id: item.id,
                        name: item.name
                    });
                }
            });
        },

        prepareOptionsList() {
            this.errors.forEach(error => {
                if (!this.optionsList.find(option => option.name === error.id)) {
                    this.optionsList.push({
                        name: error.id,
                        label: error.name,
                        selectable: true
                    });
                }
            });

            Dep.prototype.prepareOptionsList.call(this);
        }

    })
);
