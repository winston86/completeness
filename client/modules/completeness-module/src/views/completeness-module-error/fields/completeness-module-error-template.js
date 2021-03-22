
Espo.define('completeness-module:views/completeness-module-error/fields/completeness-module-error-template', 'treo-core:views/fields/filtered-link-multiple',
    Dep => Dep.extend({

        createDisabled: true,
        selectBoolFilterList:  ['notLinkedWithErrorsInProduct'],

        boolFilterData: {
            completeAttribute() {
                return {
                    completeAttribute: this.model.get('completeAttribute'),
                }
            },
            totalQuality() {
                return {
                    totalQuality: this.model.get('totalQuality'),
                }
            }
        }

    })
);
