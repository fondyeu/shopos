<?php

define('MODULE_PAYMENT_OPLATA_TEXT_TITLE', 'Oplata');
define('MODULE_PAYMENT_OPLATA_TEXT_PUBLIC_TITLE', 'oplata');
define('MODULE_PAYMENT_OPLATA_TEXT_ADMIN_DESCRIPTION', 'Модуль оплаты Oplata<br />Как правильно настроить модуль читайте.');
define('MODULE_PAYMENT_OPLATA_TEXT_DESCRIPTION', 'После нажатия кнопки Подтвердить заказ Вы перейдёте на сайт платёжной системы для оплаты заказа, после оплаты Ваш заказ будет выполнен.');
  
define('MODULE_PAYMENT_OPLATA_STATUS_TITLE' , 'Разрешить модуль Oplata');
define('MODULE_PAYMENT_OPLATA_STATUS_DESC' , 'Вы хотите разрешить использование модуля при оформлении заказов?');
define('MODULE_PAYMENT_OPLATA_SHOP_ID_TITLE' , 'ID сайта:');
define('MODULE_PAYMENT_OPLATA_SHOP_ID_DESC' , 'Укажите номер (ID магазина) Вашего магазина в Oplata.');
define('MODULE_PAYMENT_OPLATA_SECRET_KEY_TITLE' , 'Секретное слово');
define('MODULE_PAYMENT_OPLATA_SECRET_KEY_DESC' , 'В данной опции укажите Ваше секретное слово, указанное в опции Секретное на сайте Oplata.');
define('MODULE_PAYMENT_OPLATA_ORDER_STATUS_ID_TITLE' , 'Укажите оплаченный статус заказа');
define('MODULE_PAYMENT_OPLATA_ORDER_STATUS_ID_DESC' , 'Укажите оплаченный статус заказа.');
define('MODULE_PAYMENT_OPLATA_HELP',  
  'Как настроить Oplata.
1. У Вас должен быть зарегистрирован магазин в системе Oplata и у Вас должен быть ID номер магазина.
2. На сайте Oplata в настройках магазина нужно заполнить пару опций в форме:
Оповещение о платеже: - http://ваш-магазин.ру/oplata.php
Секретное слово - Укажите любой набор букв, цифр и запишите, значение опции Секретное слово нужно будет указать в Админки - Модули - Оплата - oplata.
3. Заполняйте оставшиеся поля и нажимайте Сохранить.

Настройка модуля оплаты в магазине:

1. В Админке - Модули - Оплата устанавливайте модуль oplata.
2. Указываете свой ID номер магазина.
3. В поле Секретное слово указываете своё секретное слово, указанное в настройках магазина на сайте Oplata.

Всё, модуль должен работать.');


define('MODULE_PAYMENT_OPLATA_TEXT_ERROR_MESSAGE', 'Произошла ошибка при проведении платежа. Проверьте данные и попробуйте оплатить позже. В случае неудачи свяжитесь с администрацией.');
define('MODULE_PAYMENT_OPLATA_TEXT_ERROR', 'Ошибка при проведении платежа');

define('MODULE_PAYMENT_OPLATA_ERROR_DECLINE', 'Произошла ошибка при проведении платежа. Транзакция была отклонена.');
define('MODULE_PAYMENT_OPLATA_ERROR_SIGNATURE', 'Произошла ошибка при проведении платежа. Сигнатура не валидна.');

?>