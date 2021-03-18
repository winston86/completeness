
Espo.define('completeness:views/completeness-error/fields/completeness-error-template', 'treo-core:views/fields/filtered-link-multiple',
    Dep => Dep.extend({

        createDisabled: true,
        selectBoolFilterList:  ['notLinkedWithErrorsInProduct'],

        boolFilterData: {
            notLinkedWithErrorsInProduct() {
                return {
                    productId: this.model.get('productId'),
                }
            }
        }

    })
);
