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

Espo.define('completeness:views/detail', 'views/detail',
    Dep => Dep.extend({

        updateRelationshipPanel(name) {
            let bottom = this.getView('record').getView('bottom');
            if (bottom) {
                let rel = bottom.getView(name);
                if (rel) {
                    if (rel.collection) {
                        rel.collection.fetch();
                    }
                    if (typeof rel.setupList === 'function') {
                        rel.setupList();
                    }
                }
            }
        },

        actionCreateRelatedConfigured: function (data) {
            data = data || {};

            let link = data.link;
            let scope = this.model.defs['links'][link].entity;
            let foreignLink = this.model.defs['links'][link].foreign;
            let fullFormDisabled = data.fullFormDisabled;
            let afterSaveCallback = data.afterSaveCallback;
            let panelView = this.getPanelView(link);

            let attributes = {};

            if (this.relatedAttributeFunctions[link] && typeof this.relatedAttributeFunctions[link] == 'function') {
                attributes = _.extend(this.relatedAttributeFunctions[link].call(this), attributes);
            }

            Object.keys(this.relatedAttributeMap[link] || {}).forEach(function (attr) {
                attributes[this.relatedAttributeMap[link][attr]] = this.model.get(attr);
            }, this);

            this.notify('Loading...');

            let viewName =
                ((panelView || {}).defs || {}).modalEditView ||
                this.getMetadata().get(['clientDefs', scope, 'modalViews', 'edit']) ||
                'views/modals/edit';

            this.createView('quickCreate', viewName, {
                scope: scope,
                relate: {
                    model: this.model,
                    link: foreignLink,
                },
                attributes: attributes,
                fullFormDisabled: fullFormDisabled
            }, function (view) {
                view.render();
                view.notify(false);
                this.listenToOnce(view, 'after:save', function () {
                    this.updateRelationshipPanel(link);
                    this.model.trigger('after:relate', link);

                    if (afterSaveCallback && panelView && typeof panelView[afterSaveCallback] === 'function') {
                        panelView[afterSaveCallback](view.getView('edit').model);
                    }
                }, this);
            }.bind(this));
        },

        actionCreateRelatedEntity(data) {
            let link = data.link;
            let scope = data.scope || this.model.defs['links'][link].entity;
            let afterSaveCallback = data.afterSaveCallback;
            let panelView = this.getPanelView(link);

            let viewName =
                ((panelView || {}).defs || {}).modalEditView ||
                this.getMetadata().get(['clientDefs', scope, 'modalViews', 'edit']) ||
                'views/modals/edit';

            this.notify('Loading...');
            this.createView('quickCreate', viewName, {
                scope: scope,
                attributes: {},
                fullFormDisabled: true
            }, function (view) {
                view.render();
                view.notify(false);
                this.listenToOnce(view, 'after:save', () => {
                    if (afterSaveCallback && panelView && typeof panelView[afterSaveCallback] === 'function') {
                        panelView[afterSaveCallback](view.getView('edit').model);
                    }
                }, this);
            }.bind(this));
        },

        getPanelView(name) {
            let panelView;
            let recordView = this.getView('record');
            if (recordView) {
                let bottomView = recordView.getView('bottom');
                if (bottomView) {
                    panelView = bottomView.getView(name)
                }
            }
            return panelView;
        }
    })
);

