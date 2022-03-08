require([
    'Magento_Ui/js/lib/validation/validator',
    'jquery',
    'mage/translate'
], function(validator, $){
    validator.addRule(
        'validate-json',
        function (value) {
            if (value == "") {
                return true;
            }
            try {
                JSON.parse(value);
                return true;
            } catch (e) {
                return false;
            }
        }
        ,$.mage.__('Value must be a properly-formatted JSON string')
    );
});
