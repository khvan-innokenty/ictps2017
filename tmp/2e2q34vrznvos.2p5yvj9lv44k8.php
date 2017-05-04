<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta property="og:image" content="/ui/images/og_image.jpg">

    <title><?= $DICT['courseShortName'] ?></title>
    <meta name="description" content="<?= $DICT['courseDescription'] ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon">

    <link href="https://fonts.googleapis.com/css?family=Ubuntu+Condensed|Ubuntu:300,400,700&amp;subset=cyrillic" rel="stylesheet">
    <link rel="stylesheet" href="/dist/style.css?v=<?= $APP_VERSION ?>">
</head>

<body>
<div class="header">
    <div class="container cf">
        <div class="header__left">
            <ul class="header__navigation cf" id="navigation">
                <li><a href="#about"><span><?= $DICT['about'] ?></span></a></li>
                <li><a href="#schedule"><span><?= $DICT['schedule'] ?></span></a></li>
                <?php if ($LANG === 'ru'): ?>
                    <li><a href="#dissection"><span><?= $DICT['dissection'] ?></span></a></li>
                    <li><a href="#pricelist"><span><?= $DICT['pricelist'] ?></span></a></li>
                    <li><a href="#book"><span><?= $DICT['book'] ?></span></a></li>
                <?php endif; ?>
                <li><a href="#gala-dinner"><span><?= $DICT['dinner'] ?></span></a></li>
                <li><a href="#venue"><span><?= $DICT['venue'] ?></span></a></li>
            </ul>
            <div class="hamburger" id="hamburger">
                <div class="hamburger__line1"></div>
                <div class="hamburger__line2"></div>
                <div class="hamburger__line3"></div>
                <div class="hamburger__line4"></div>
                <div class="hamburger__line5"></div>
            </div>
        </div>
        <div class="header__right">
            <a class="change-language" href="/<?= $other_lang ?>"><span><?= $other_lang ?></span></a><a class="change-site" href="http://www.iectc.ru"><span>IECTC 2017</span></a>
        </div>
    </div>
</div>

<div class="hero">
    <div class="hero__absolute hero__block1">
        <div class="hero__date-n-city">
            <div><?= periodFormat($course['start'], $course['end']) ?></div>
            <div><?= $DICT['courseCity'] ?></div>
        </div>
    </div>

    <div class="hero__absolute hero__block2">
        <div class="hero__logo">
            <div><img src="/img/logo.png"></div>
            <div><?= $DICT['courseFullName'] ?></div>
        </div>
    </div>

    <?php if ($LANG === 'ru'): ?>
        <div class="hero__absolute hero__block3">
            <div class="hero__register">
                <a href="#book" class="button"><span><?= $DICT['book'] ?></span></a>
            </div>
        </div>
    <?php endif; ?>
</div>

<div id="about"></div>
<?php if ($LANG === 'ru'): ?>
    <div class="facts block">
        <div class="container cf">
            <h2><?= $DICT['about'] ?></h2>
            <div class="fact">
                <h3><?= $DICT['fact1_header'] ?></h3>
                <?= $this->raw($DICT['fact1_text']) ?>
            </div>
            <div class="fact">
                <h3><?= $DICT['fact2_header'] ?></h3>
                <?= $this->raw($DICT['fact2_text']) ?>
            </div>
            <div class="fact">
                <h3><?= $DICT['fact3_header'] ?></h3>
                <?= $this->raw($DICT['fact3_text']) ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="block">
    <h2><?= $DICT['committee'] ?></h2>
    <ul class="committee">
        <?php foreach (($committee?:[]) as $item): ?>
            <li>
                <img src="/img/<?= $item['image'] ?>">
                <h3 class="committee__fullname"><?= $item['fullname'] ?></h3>
                <div class="committee__description"><?= $item['description'] ?></div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<div class="speakers block">
    <div class="container">
        <h2><?= $DICT['speakers'] ?></h2>
        <ul class="speakers__list">
            <?php foreach (($speakers?:[]) as $item): ?>
                <li>
                    <img src="/img/<?= $item['image'] ?>">
                    <div class="speaker__fullname">
                        <?= $item['fullname'] ?>
                        <?php if ($item['twitter']): ?><a href="<?= $item['twitter'] ?>" target="_blank" title="Twitter"><img src="/img/twitter.svg"></a><?php endif; ?>
                        <?php if ($item['facebook']): ?><a href="<?= $item['facebook'] ?>" target="_blank" title="Facebook"><img src="/img/facebook.svg"></a><?php endif; ?>
                        <?php if ($item['instagram']): ?><a href="<?= $item['instagram'] ?>" target="_blank" title="Instagram"><img src="/img/instagram.svg"></a><?php endif; ?>
                    </div>
                    <div class="speaker__description"><?= $item['description'] ?></div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<div class="schedule block" id="schedule">
    <div class="container">
        <h2><?= $DICT['schedule'] ?></h2>
        <ul class="day__selector">
            <?php foreach (($dates?:[]) as $key=>$item): ?>
                <li><a href="#day-<?= $key + 1 ?>" class="js-select-day <?= $key === 0 ? 'active' : '' ?>"><span class="day__title"><?= shortDateFormat( $item ) ?></span><span class="day__dof"><?= dof( $item ) ?></span></a></li>
            <?php endforeach; ?>
        </ul>
        <?php foreach (($dates?:[]) as $key=>$item): ?>
            <div class="js-day-schedule day__schedule <?= $key === 0 ? '' : 'hidden' ?>" id="day-<?= $key + 1 ?>">

                <?php echo $this->render('../data/' . $LANG . '/day' . ($key + 1) . '.html',NULL,get_defined_vars(),0); ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php if ($LANG === 'ru'): ?>
    <div class="dissection block" id="dissection">
        <div class="container">
            <h2>Диссекционный курс в рамках ICTPS</h2>
            <div class="dissection__title">«Хирургическая анатомия носа и алгоритмы эстетической ринопластики с учётом функциональных аспектов»</div>
            <div class="dissection__table">
                <div class="dissection__left">
                    <div class="dissection__date">12 июня 2017</div>
                    <div class="dissection__venue">Кафедра клинической анатомии и оперативной хирургии СПбГМУ им. Академика И.П. Павлова</div>
                    <b>Пшениснов Кирилл Павлович</b><br>д.м.н., профессор
                </div>
                <div class="dissection__center">
                    <img src="/img/dissection.png">
                </div>
                <div class="dissection__right">
                    <p><b>Нефиксированный</b> биологический материал высочайшего качества.</p>
                    <p>2 участника на препарат.</p>
                    <p>Каждый участник <b>отработает навыки</b> под руководством тренера и проведёт диссекцию.</p>
                </div>
            </div>
            <p class="text-center">Заявки на участие в диссекционном курсе принимаются на e-mail: <a href="mailto:events@bioconcept.ru">events@bioconcept.ru</a></p>
        </div>
    </div>

    <div class="business block" id="business">
        <div class="container">
            <h2>Бизнес-практикум для руководителей</h2>
            <div class="business__title">&laquo;Как увеличить оборот клиники на 100%&raquo;</div>
            <div class="business__hero">
                <img class="business__hero__bg" src="/img/growth.svg">
                <img class="business__hero__logo" src="/img/ruble.svg">
                <div class="business__hero__description">
                    <div class="business__hero__dates">9&ndash;10 июня 2017</div>
                    В рамках IECTS / ICTPS
                </div>
            </div>
        </div>
        <div class="business__pricelist__wrapper container">
            <div class="business__pricelist">
                <div class="business__pricelist__row">
                    <?php foreach (($business['tickets']?:[]) as $item): ?>
                        <div class="business__pricelist__cell top"><div class="title"><?= $item['title'] ?></div></div>
                    <?php endforeach; ?>
                </div>
                <div class="business__pricelist__row">
                    <?php foreach (($business['tickets']?:[]) as $item): ?>
                        <div class="business__pricelist__cell middle">
                            <?php foreach (($item['features']?:[]) as $feature): ?>
                                <div class="feature"><?= $feature ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="business__pricelist__row">
                    <?php foreach (($business['tickets']?:[]) as $item): ?>
                        <div class="business__pricelist__cell bottom">
                            <table>
                                <?php foreach (($item['pricelist']?:[]) as $i=>$price): ?>
                                    <tr class="<?= $business['pricelist']['classes'][$i] ?>">
                                        <td><?= $this->raw($business['pricelist']['titles'][$i]) ?></td>
                                        <td><?= price($price) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="container">
            <p class="text-center">Заявки на участие в бизнес–практикуме принимаются на e-mail: <a href="mailto:events@bioconcept.ru">events@bioconcept.ru</a></p>
        </div>
    </div>

    <div class="contest block" id="contest">
        <div class="container">
            <h2>Конкурс в рамках ICTPS</h2>
            <div class="contest__table">
                <div class="contest__image">
                    <img src="/img/contest.png">
                </div>
                <div class="contest__description">
                    <div class="contest__title">«Сложные клинические случаи эстетической хирургии молочной железы»</div>
                    <div class="contest__prizes">
                        <div class="contest__prize">
                            <b>1 место</b> &mdash; поездка на конгресс в Чикаго.
                        </div>
                        <div class="contest__prize">
                            <b>2 место</b> &mdash; регистрационный взнос на конгресс в Чикаго.
                        </div>
                        <div class="contest__prize">
                            <b>3 место</b> &mdash; хирургические инструменты на сумму 15 000 рублей.
                        </div>
                    </div>
                    <a href="/data/ictps-2017-statues.pdf" class="button button--with-image" download><img src="/img/download.svg"> Положение о конкурсе</a>
                    <a href="/data/ictps-2017-bid-form.pdf" class="button button--with-image" download><img src="/img/download.svg"> Заявка на участие</a>
                </div>
            </div>
        </div>
    </div>

    <div class="pricelist block" id="pricelist">
        <div class="container">
            <h2>Стоимость</h2>
            <div class="pricelist__table">
                <div class="pricelist__header">
                    <div class="pricelist__ticket__blank pricelist__ticket__blank--primary">&nbsp;</div>
                    <?php foreach (($pricelist['titles']?:[]) as $i=>$title): ?>
                        <div class="pricelist__ticket__blank <?= $pricelist['classes'][$i] ?>">
                            <?= $this->raw($title) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php foreach (($tickets?:[]) as $item): ?>
                    <div class="pricelist__ticket">
                        <div class="pricelist__ticket__title">
                            <?= $this->raw($item['title']) ?> <span class="pricelist__arrow-down">↓</span>
                            <?php if ($item['comment']): ?><div class="comment"><?= $item['comment'] ?></div><?php endif; ?>
                        </div>
                        <?php foreach (($item['pricelist']?:[]) as $i=>$price): ?>
                            <div class="pricelist__ticket__price <?= $pricelist['classes'][$i] ?>">
                                <div class="pricelist__ticket__price__date"><?= $this->raw($pricelist['titles'][$i]) ?></div>
                                <div class="pricelist__ticket__price__sum"><?= price($price) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="pricelist__comment">
                Пакет участия для онкологов предназначен для специалистов, работающих в гос. учреждениях. Просьба предоставить документ, подтверждающий Вашу должность.
            </div>
        </div>
    </div>

    <div class="block" id="book">
        <h2>Забронировать</h2>
        <div class='rf' id='form'>
            <div class="rf__overlay"></div>

            <div class="rf__header"></div>
            <div class="rf__subheader"></div>
            <div class="rf__header__mobile">
                <button class="rf__header__mobile__prev js-prev" disabled>←<span class="text">Назад</span></button>
                <button class="rf__header__mobile__next js-next">→<span class="text">Далее</span></button>
                <span class="current-title"></span>
                <div class="rf__header__mobile__counter">Шаг <span class="current-step"></span> из <span class="total-steps"></span></div>
            </div>

            <div class='rf__road'>
                <div class='rf__step' data-title='Вариант участия'>
                    <div class="container text-center cf">
                        <?php foreach (($tickets?:[]) as $i=>$item): ?>
                            <input type='radio' name='ticket' class='rf__rich__checkbox' id='<?= "ticket-conf-" . $i ?>' data-price="<?= $pricelist['current'][$i] ?>" value="<?= strip_tags($this->raw($item['title'] . ' ' . $item['comment'])) ?>" <?= $i === 0 ? "checked" : "" ?>>
                            <label for='<?= "ticket-conf-" . $i ?>' class="js-align-height">
                            <span class='title'>
                                <?= $this->raw($item['title']) ?>
                                <span class="comment"><?= $item['comment'] ? $item['comment'] : "&nbsp;" ?></span>
                            </span>
                                <span class='price'><?= price($pricelist['current'][$i]) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class='rf__step' data-title='Контактные данные'>
                    <div class="container">
                        <div class="rf__inputs cf">
                            <?php foreach (($inputs?:[]) as $item): ?>
                                <?php if ($item['required']): ?>
                                    <div class='rf__input-block <?= $item['half'] ? "half" : "" ?> <?= $item['third'] ? "third" : "" ?>'>
                                        <label for='<?= $item['slug'] ?>'><?= $item['title'] ?></label>
                                        <input type='<?= $item['type'] ? $item['type'] : "text" ?>' name='<?= $item['slug'] ?>' id='<?= $item['slug'] ?>' data-required value="<?= $GET[$item['slug']] ?>">
                                        <div class="error"></div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class='rf__step' data-title='Работа'>
                    <div class="container">
                        <div class="rf__inputs cf">
                            <?php foreach (($inputs?:[]) as $item): ?>
                                <?php if (!$item['required']): ?>
                                    <div class='rf__input-block <?= $item['half'] ? "half" : "" ?>'>
                                        <label for='<?= $item['slug'] ?>'><?= $item['title'] ?> <span class="comment">по желанию</span></label>
                                        <input type='text' name='<?= $item['slug'] ?>' id='<?= $item['slug'] ?>' value="<?= $GET[$item['slug']] ?>">
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class='rf__step' data-title='Дополнительные опции'>
                    <div class="container cf">
                        <?php foreach (($options?:[]) as $item): ?>
                            <div class='rf__checkbox-block'>
                                <input type='checkbox' class='rf__checkbox' id='<?= $item['slug'] ?>' name="<?= $item['slug'] ?>"
                                       <?= $item['discount'] ? "data-discount='" .$item['discount'] . "'" : "" ?>
                                <?= $item['price'] ? "data-price='" .$item['price'] . "'" : "" ?>
                                >
                                <label for='<?= $item['slug'] ?>'><?= $this->raw($item['title']) ?></label>
                            </div>
                        <?php endforeach; ?>

                        <div class='rf__checkbox-block'>
                            <input type='checkbox' class='rf__checkbox' id='pols' name="pols" data-required>
                            <label for='pols'>Я принимаю условия <a href="/data/personal-data.pdf" tabindex="-1" download>Пользовательского соглашения</a></label>
                            <div class="error">Необходимо прочитать и принять условия Пользовательского соглашения.</div>
                        </div>
                    </div>
                </div>
                <div class='rf__step' data-title='Завершение регистрации'>
                    <div class="container">
                        <div class="f-modal-alert">
                            <div class="f-modal-inner">
                                <div class="f-modal-icon f-modal-success">
                                    <span class="f-modal-line f-modal-tip animateSuccessTip"></span>
                                    <span class="f-modal-line f-modal-long animateSuccessLong"></span>
                                    <div class="f-modal-placeholder"></div>
                                    <div class="f-modal-fix"></div>
                                </div>
                                <div class="f-modal-message">
                                    <p>Отлично! Вы зарегистрировались на мероприятие. Мы скоро свяжемся с Вами, чтобы ответить на все интересующие вопросы.</p>
                                    <p>Если вопросов нет &mdash; переходите к оплате.</p>

                                    <form action="https://rbkmoney.ru/acceptpurchase.aspx" name="pay" method="POST" id="rbk">
                                        <input type="hidden" name="eshopId" value="2033199">
                                        <input type="hidden" name="orderId" value="<?= date('mdhis') ?>">
                                        <input type="hidden" name="serviceName" value="Форма регистрации участника VII МЕЖДУНАРОДНЫЙ КУРС-ТРЕНИНГ ДЛЯ ПЛАСТИЧЕСКИХ И РЕКОНСТРУКТИВНЫХ ХИРУРГОВ">
                                        <input type="hidden" name="successUrl" value="http://ictps.ru">
                                        <input type="hidden" name="failUrl" value="http://ictps.ru">
                                        <input type="hidden" name="recipientCurrency" value="RUR">
                                        <input type="hidden" name="recipientAmount" value="">
                                        <input type="hidden" name="user_email" value="">
                                        <input type="hidden" name="userField_1" value="">
                                        <button type="submit" class="button button--pay" tabindex="-1"><span>Оплатить</span></button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rf__footer">
                <div class="rf__total-price">
                    К оплате: <span class="primary"><span id="rf-total-price"></span> руб.</span>
                </div>
                <button class="button js-prev" disabled>
                    <span class="arrow_left">←</span> Назад
                </button>
                <button class="button js-next">
                    Далее <span class="arrow_right">→</span>
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="dinner block" id="gala-dinner">
    <h2><?= $DICT['dinner'] ?></h2>
    <div class="container cf">
        <div class="dinner__table">
            <div class="dinner__left">
                <?= $DICT['price'] ?>
                <div class="dinner__price">10 000 <span class="rubles"><?= $DICT['rubles'] ?></span></div>
            </div>
            <div class="dinner__center">
                <div class="dinner__date-n-time">
                    <?php if ($LANG === 'ru'): ?>
                        10 июня
                        <?php else: ?>10 Jun
                    <?php endif; ?>
                    <div class="dinner__at"><?= $DICT['at'] ?></div>
                    20:30
                </div>
            </div>
            <div class="dinner__right">
                <?= $DICT['transfer1'] ?>
                <div class="dinner__transfer__time"><span class="dinner__at"><?= $DICT['at'] ?></span> 19:30</div>
                <?= $DICT['transfer2'] ?>
                <div class="dinner__transfer__time dinner__transfer__time--last"><span class="dinner__at"><?= $DICT['at'] ?></span> 00:00</div>
            </div>
        </div>
    </div>
</div>

<div class="venue block" id="venue">
    <div class="container">
        <h2><?= $DICT['venue'] ?></h2>
        <div class="venue__table">
            <div class="venue__map"></div>
            <div class="venue__description">
                <p class="venue__address">
                    <?= $DICT['address'] ?><br>
                    <?= $DICT['hotel'] ?>
                </p>
                <p>
                    <a href="tel:+74956057424"><img src="/img/phone.svg"><span>+7 (495) 605-74-24</span></a><br>
                    <a href="tel:+79857676693"><img src="/img/phone.svg"><span>+7 (985) 767-66-93</span></a>
                </p>
                <p>
                    <a href="mailto:events@bioconcept.ru"><img src="/img/mail.svg"><span>events@bioconcept.ru</span></a><br>
                    <a href="https://www.facebook.com/spbictps/"><img src="/img/facebook.svg"><span>facebook.com/spbictps</span></a><br>
                    <a href="https://vk.com/ictps"><img src="/img/vk.svg"><span>vk.com/ictps</span></a><br>
                    <a href="https://www.instagram.com/explore/tags/ictps/"><img src="/img/instagram.svg"><span>#ictps</span></a><br>
                    <a href="https://www.youtube.com/channel/UC3XApVGvPT-bkxrTUnDxenA/feed"><img src="/img/youtube.svg"><span><?= $DICT['video'] ?></span></a><br>
                </p>
            </div>
        </div>

        <div class="brands">
            <div class="cf">
                <div class="brand">
                    <h3><?= $DICT['organizer'] ?></h3>
                    <img src="/img/bioconcept.png">
                </div>
                <div class="brand">
                    <h3><?= $DICT['generalSponsor'] ?></h3>
                    <img src="/img/clovermed.png">
                </div>
                <div class="brand">
                    <h3><?= $DICT['support'] ?></h3>
                    <img src="/img/spbgmu.png">
                    <img src="/img/mant.png">
                    <img src="/img/spras.png">
                </div>
                <div class="brand">
                    <h3><?= $DICT['partner'] ?></h3>
                    <img src="/img/kuprin.png">
                </div>
            </div>
            <h3><?= $DICT['sponsors'] ?></h3>
            <img src="/img/motiva.png">
            <img src="/img/jj.png">
        </div>
    </div>
</div>

<div class="block">
    <div class="container">
        <h2><?= $DICT['expo'] ?> ICTPS</h2>
        <ul class="expo">
            <?php foreach (($expo['ictps']?:[]) as $item): ?>
                <li><img src="<?= $item ?>"></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<div class="block">
    <div class="container">
        <h2><?= $DICT['expo'] ?> IECTC</h2>
        <ul class="expo">
            <?php foreach (($expo['iectc']?:[]) as $item): ?>
                <li><img src="<?= $item ?>"></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
<script src="/dist/app.min.js?v=<?= $APP_VERSION ?>"></script>

<!-- Yandex.Metrika counter --> <script type="text/javascript"> (function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter28534656 = new Ya.Metrika({ id:28534656, clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks"); </script> <noscript><div><img src="https://mc.yandex.ru/watch/28534656" style="position:absolute; left:-9999px;" alt="" /></div></noscript> <!-- /Yandex.Metrika counter -->
</body>
</html>