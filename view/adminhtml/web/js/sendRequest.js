define(
    ['jquery'],
    function ($) {
        $.widget('mage.ContentorSendRequest', {
            options: {
                formular: document.getElementById('productform'),
                sendUrl: ''
            },
            /**
             * @private
             */
            _create: function () {
                let self = this;
                $("button[name=submitme]").on('click', function () {
                    self.doSend(self.options.formular);
                })
            },

            /**
             * @param formular
             */
            doSend: function (formular) {
                if (!document.getElementsByName("theend").value && (formular !== null) && (typeof (formular.products) !== 'undefined')) {

                    const products = formular.products.value;

                    const source = formular.source.value;

                    const total = formular.total.value;

                    const deliverySpeed = formular.deliverySpeed.value;

                    const machineTranslation = formular.machineTranslation ? formular.machineTranslation.value : 'none';

                    let inputs = null,
                        self = this;

                    if (document.getElementById('configAttributesContentCreation')) {
                        inputs = document.getElementById('configAttributesContentCreation').getElementsByTagName('input');
                    }

                    if (document.getElementById('configAttributesContentCreationResponse')) {
                        inputs = document.getElementById('configAttributesContentCreationResponse').getElementsByTagName('input');
                    }

                    let elesContentCreation = {};

                    if (inputs) {
                        for (var i = 0; i < inputs.length; i++) {

                            if (inputs[i].name.indexOf('attributesConfig') === 0) {

                                let attributeName = '';

                                let matches = inputs[i].name.match(/\[(.*?)\]/);

                                if (matches) {
                                    attributeName = matches[1];
                                }

                                if (attributeName !== '') {
                                    elesContentCreation[attributeName] = {
                                        word_count: $('#' + attributeName + '_word_count').val(),
                                        field_type: $('#' + attributeName + '_field_type').val(),
                                        context_value: $('#' + attributeName + '_context_value').val()
                                    };
                                }
                            }
                        }
                    }

                    let targets;

                    if (formular.targets.type === 'select-multiple') {
                        targets = [];
                        for (x = 0; x < formular.targets.length; x++) {
                            if (formular.targets[x].selected) {
                                targets.push(formular.targets[x].value);
                            }
                        }
                        targets = targets.join(',');
                    } else {
                        targets = formular.targets.value;
                    }
                    $.post(this.options.sendUrl, {
                        products: products,
                        source: source,
                        targets: targets,
                        total: total,
                        deliverySpeed: deliverySpeed,
                        machineTranslation: machineTranslation,
                        attributesConfig: elesContentCreation
                    }, function (data) {
                        document.getElementById('resultdiv').innerHTML = data;
                        self.doSend(document.getElementById('progressform'));
                    });
                }
            }
        });

        return $.mage.ContentorSendRequest;
    });
