
Espo.define('completeness:views/completeness-rule/fields/completeness-rule-template', 'treo-core:views/fields/filtered-link',
    Dep => Dep.extend({

        createDisabled: true,

        selectBoolFilterList:  ['onlyActive'],

    })
);
