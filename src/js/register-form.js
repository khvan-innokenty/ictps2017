(function( $, document, window ) {
    var settings = {
        formSelector: '#form',
        overlaySelector: '.rf__overlay',
        headerSelector: '.rf__header',
        subheaderSelector: '.rf__subheader',
        headerMobileSelector: '.rf__header__mobile',
        currentStepTitleSelector: '.current-title',
        currentStepSelector: '.current-step',
        totalStepsSelector: '.total-steps',
        roadSelector: '.rf__road',
        stepSelector: '.rf__step',
        footerSelector: '.rf__footer',
        buttonPrevSelector: '.js-prev',
        buttonNextSelector: '.js-next',
        totalPriceSelector: '#rf-total-price',
        bulletClass: 'rf__bullet',
        bulletTitleClass: 'rf__bullet__title',
        registerUrl: '/register'
    };

    var $form = $(settings.formSelector),
        $overlay = $(settings.overlaySelector),
        $road = $form.find(settings.roadSelector),
        slider,
        $steps = $form.find(settings.stepSelector),
        $header = $form.find(settings.headerSelector),
        $subheader = $form.find(settings.subheaderSelector),
        $buttonPrev = $form.find(settings.buttonPrevSelector),
        $buttonNext = $form.find(settings.buttonNextSelector),
        $totalPrice = $form.find(settings.totalPriceSelector),
        $currentTitle = $form.find( settings.currentStepTitleSelector ),
        $currentStep = $form.find( settings.currentStepSelector ),
        $totalSteps = $form.find( settings.totalStepsSelector ),
        inputs = [], // массив полей ввода для каждого шага, нумерация с 0
        bullets = [], // массив $-объектов - цифр
        bulletTitles = [], // массив $-объектов - попдисей к цифрам
        currentStep, // текущий шаг, нумерация с 0
        lastStep, // номер последнего шага
        totalPrice; // Итоговая цена, которую видит пользователь


    /**
     * Инициализация формы регистрации
      */
    function init() {
        $buttonNext.click(nextStep);
        $buttonPrev.click(prevStep);

        $road.royalSlider({
            autoHeight: true,
            sliderDrag: false,
            navigateByClick: false,
            transitionSpeed: 300,
            controlNavigation: 'none',
            arrowNav: false
        });

        slider = $road.data('royalSlider');

        lastStep = slider.numSlides - 1;
        $totalSteps.html( slider.numSlides );

        slider.ev.on('rsBeforeAnimStart', function(event){
            var newSlide = slider.currSlideId;

            if ( (newSlide > currentStep) && !inputsAreCorrect() ) {
                slider.goTo( currentStep );
                return;
            }

            if ( newSlide === lastStep && currentStep !== lastStep ) {
                register();
            }

            setStep( newSlide );
        });

        $.get('/location', {}, function(msg){
            $("#city").val(msg);
        });

        $("#rbk").submit(function(){
            var $this = $(this),
                $submit = $this.find('button');

            $submit.prop('disabled', true).addClass('loading');
        });
    }


    /**
     * Генерация заголовков формы регистрации (шаги)
     */
    function calculate() {
        var i = 1,
            width = (100 / slider.numSlides) + '%';

        $steps.each(function(){
            var $this = $(this),
                title = $this.data('title'),
                letter = i === slider.numSlides ? '&nbsp;' : i,
                $bullet,
                $title,
                $stepInputs;

            $bullet = $('<div/>', {
                    class: settings.bulletClass,
                    html: '<span>' + letter + '</span>'
                }).appendTo($header);

            $title = $('<div/>', {
                    class: settings.bulletTitleClass,
                    html: title ? title : '&nbsp;'
                }).appendTo($subheader);

            $bullet.css('width', width);
            $title.css('width', width);

            $stepInputs = $this.find('input:visible');

            bullets.push( $bullet );
            bulletTitles.push( $title );

            $stepInputs.each(function(){
                var $this = $(this);

                $this.data('required', $this.data('required') !== undefined);
                if ($this.data('required'))
                    $this.parent().addClass('required');
            });

            $stepInputs.on('keyup change', function(){
                var $this = $(this),
                    required = $this.data('required'),
                    discount = $this.data('discount'),
                    price = $this.data('price'),
                    val = $(this).val();

                if (required) {
                    if (val.trim() !== '')  {
                        $this.parent().addClass('correct').removeClass('error');
                    }
                    else {
                        $this.parent().removeClass('correct');
                    }
                }

                updatePrice();
            });

            $stepInputs.on('click', function(){
                $(this).focus();
            });

            inputs.push($stepInputs);
            i++;
        });
    }


    /**
     * Пересчитать цену
     */
    function updatePrice() {
        var maxDiscount = 0;

        totalPrice = 0;

        for (var i=0; i < inputs.length; i++) {
            inputs[i].each(function(){
                var $this = $(this),
                    type = $this.prop('type'),
                    isCheckboxOrRadio = type === 'radio' || type === 'checkbox',
                    active = isCheckboxOrRadio ? $this.prop('checked') : true,
                    required = $this.data('required'),
                    discount = $this.data('discount'),
                    price = $this.data('price');

                if (active) {
                    if (discount) maxDiscount = Math.max(discount, maxDiscount);
                    if (price) totalPrice += price;
                }
            });
        }

        totalPrice = totalPrice * (1 - maxDiscount/100);
        $totalPrice.html( totalPrice.toRubles() );
    }


    /**
     * Проверяет корректность заполнения полей ввода на текущем шаге
     * @return {boolean}
     */
    function inputsAreCorrect() {
        var allRequiredInputsAreFilled = true;

        inputs[currentStep].each(function(){
            var $this = $(this),
                val = $this.val(),
                required = $this.data('required'),
                $parent = $this.parent();

            if (required) {
                switch ($this.prop('type')) {
                    case 'text':
                    case 'tel':
                    case 'email':
                        if (val.trim() === '') {
                            allRequiredInputsAreFilled = false;
                            $parent.addClass('error').removeClass('correct');
                        }
                        else {
                            $parent.removeClass('error').addClass('correct');
                        }
                        break;
                    case 'checkbox':
                        if ( !$this.is(':checked') ) {
                            allRequiredInputsAreFilled = false;
                            $parent.addClass('error').removeClass('correct');
                        }
                        else {
                            $parent.removeClass('error').addClass('correct');
                        }
                        break;
                }

                if ($this.prop('name') === 'email' && allRequiredInputsAreFilled && !val.isEmail()) {
                    allRequiredInputsAreFilled = false;
                    $parent.addClass('error').removeClass('correct');
                    $parent.find('.error').html('Некорректный адрес!')
                }
            }
        });

        return allRequiredInputsAreFilled;
    }


    /**
     * Сменить текущий шаг
     * Изменяются классы заголовков
     * Обновляются tabindex
     * @param step
     */
    function setStep( step ) {
        var i;

        for (i = 0; i < inputs.length; i++) {
            if ( inputs[i] )
                inputs[i].blur().prop('tabindex', -1);
        }

        currentStep = step > lastStep ? lastStep : ( step < 0 ? 0 : step);

        $buttonPrev.prop('disabled', currentStep === 0);
        $buttonNext.prop('disabled', currentStep === lastStep);

        for (i = 0; i < bullets.length; i++) {
            if (i  <  currentStep) {
                bullets[i].addClass('past').removeClass('current');
                bulletTitles[i].addClass('past').removeClass('current');
            }
            if (i === currentStep) {
                bullets[i].addClass('current').removeClass('past');
                bulletTitles[i].addClass('current').removeClass('past');
            }
            if (i  >  currentStep) {
                bullets[i].removeClass('current').removeClass('past');
                bulletTitles[i].removeClass('current').removeClass('past');
            }
        }

        $currentTitle.html($steps.eq(currentStep).data('title'));
        $currentStep.html(currentStep + 1);

        if (currentStep !== lastStep) {
            $form.removeClass('last-step');
        }

        if ( inputs[currentStep] ) {
            inputs[currentStep].prop('tabindex', 1);
        }
    }


    /**
     * Следующий шаг
     */
    function nextStep() {
        slider.next();
    }


    /**
     * Предыдущий шаг
     */
    function prevStep() {
        if (currentStep === lastStep) {
            slider.goTo(0);
        } else {
            slider.prev();
        }
    }


    /**
     * Выравнавние вариантов участия по высоте
     */
    function alignHeight() {
        var maxHeight = 0,
            $items = $(".js-align-height").find('.title');

        if ( $(document).width() < 600) {
            $items.height('auto');
            return;
        }

        $items.height('auto');
        $items.each(function(){
            maxHeight = Math.max(maxHeight, $(this).height());
        });

        $items.height(maxHeight + 'px');
    }


    /**
     * Получить данные формы в объект
     * @return array
     */
    function getData() {
        var data = {
            price: totalPrice
        };

        for (var i=0; i<inputs.length; i++) {
            inputs[i].each(function(){
                var $this = $(this),
                    name = $this.prop('name'),
                    type = $this.prop('type'),
                    isCheckbox = type === 'checkbox',
                    isRadio = type === 'radio',
                    checked = (isCheckbox || isRadio) && $this.is(':checked');

                switch (type) {
                    case 'checkbox':
                        data[name] = $this.is(':checked') ? 1 : 0;
                        break;
                    case 'radio':
                        if ( $this.is(':checked') ) data[name] = $this.val();
                        break;
                    default:
                        data[name] = $this.val();
                }
            });
        }

        return data;
    }


    /**
     * Регистрация пользователя
     */
    function register() {
        var data = getData();
        $form.addClass('loading');
        $buttonNext.prop('disabled', true);
        $buttonPrev.prop('disabled', true);
        $overlay.hide();

        console.log( data );

        $("input[name='recipientAmount']").val( data['price'] + '.00' );
        $("input[name='user_email']").val( data['email'] );
        $("input[name='userField_1']").val( data['email'] );

        $.post(settings.registerUrl, data, function( msg ){
            $form.removeClass('loading').addClass('last-step');
            $buttonNext.prop('disabled', false);
            $buttonPrev.prop('disabled', false);
            $overlay.hide();
            $(".f-modal-icon").addClass('animate');
            setStep( lastStep );
            console.log( msg );
        });
    }


    /**
     * Форматировать число в читабельный формат
     * 1000000 => 1 000 000
     * @return {string}
     */
    Number.prototype.toRubles = function () {
        var value = this + "",
            km, kw;
        if( (j = value.length) > 3 ){
            j = j % 3;
        } else{
            j = 0;
        }
        km = (j ? value.substr(0, j) + " " : "");
        kw = value.substr(j).replace(/(\d{3})(?=\d)/g, "$1 ");

        return km + kw;
    };

    /**
     * Валидация почты
     * @return {boolean}
     */
    String.prototype.isEmail = function() {
        var regexp = new RegExp(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);

        return regexp.test(this);
    };


    init();
    calculate();
    setStep(0);
    alignHeight();
    updatePrice();

    $(window).resize(function(){
        alignHeight();
    });

})( jQuery, document, window );