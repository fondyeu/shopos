<?php

class oplata
{
    var $code, $title, $description, $enabled;

    const ORDER_APPROVED = 'approved';
    const ORDER_DECLINED = 'declined';

    const SIGNATURE_SEPARATOR = '|';

    const ORDER_SEPARATOR = ":";

    protected static $responseFields = array(
        'rrn',
        'masked_card',
        'sender_cell_phone',
        'response_status',
        'currency',
        'fee',
        'reversal_amount',
        'settlement_amount',
        'actual_amount',
        'order_status',
        'response_description',
        'order_time',
        'actual_currency',
        'order_id',
        'tran_type',
        'eci',
        'settlement_date',
        'payment_system',
        'approval_code',
        'merchant_id',
        'settlement_currency',
        'payment_id',
        'sender_account',
        'card_bin',
        'response_code',
        'card_type',
        'amount',
        'sender_email');

// class constructor
    function oplata()
    {
        $this->code = 'oplata';
        $this->title = MODULE_PAYMENT_OPLATA_TEXT_TITLE;
        $this->public_title = MODULE_PAYMENT_OPLATA_TEXT_PUBLIC_TITLE;
        $this->description = MODULE_PAYMENT_OPLATA_TEXT_ADMIN_DESCRIPTION;
        $this->enabled = ((MODULE_PAYMENT_OPLATA_STATUS == 'True') ? true : false);
        //$this->icon_small = 'icon_small.png';
        //$this->icon = 'rbk_logo.gif';

        $this->form_action_url = 'https://api.oplata.com/api/checkout/redirect/';
    }

// class methods
    function update_status()
    {
        global $order;
    }

    function javascript_validation()
    {
        return false;
    }

    function selection()
    {
        return array('id' => $this->code, 'module' => $this->title, 'description' => $this->description);
    }

    function pre_confirmation_check()
    {
        global $cartID, $cart;

        if (empty($_SESSION['cart']->cartID)) {
            $cartID = $_SESSION['cart']->cartID = $_SESSION['cart']->generate_cart_id();
        }

        if (!isset($_SESSION['cartID'])) {
            $_SESSION['cartID'] = $cartID;
        }
    }

    function confirmation()
    {
        return false;
    }

    function process_button()
    {
        global $order, $sendto, $osPrice, $currencies, $cart_oplata_id, $shipping;

        $customers_query = os_db_query("select customers_email_address from " . TABLE_CUSTOMERS . " where customers_id = {$_SESSION['customer_id']}");

        if (os_db_num_rows($customers_query)) {
            $customer_email = @mysql_result($customers_query, 0, "customers_email_address");
        }

        $oplata_args = array(
            'order_id' => $_SESSION['cartID'] . self::ORDER_SEPARATOR . time(),
            'merchant_id' => MODULE_PAYMENT_OPLATA_SHOP_ID,
            'order_desc' => 'order',
            'amount' => round($order->info['total'] * 100),
            'currency' => 'UAH',
            'server_callback_url' => os_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL'),
            'response_url' => os_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL'),
            'lang' => 'ru',
            'sender_email' => $customer_email
        );

        $oplata_args['signature'] = $this->getSignature($oplata_args, MODULE_PAYMENT_OPLATA_SECRET_KEY);

        $process_button_string = os_draw_hidden_field('merchant_id', $oplata_args['merchant_id']) .
            os_draw_hidden_field('order_id', $oplata_args['order_id']) .
            os_draw_hidden_field('sender_email', $oplata_args['sender_email']) .
            os_draw_hidden_field('amount', $oplata_args['amount']) .
            os_draw_hidden_field('order_desc', $oplata_args['order_desc']) .
            os_draw_hidden_field('currency', $oplata_args['currency']) .
            os_draw_hidden_field('lang', $oplata_args['lang']) .
            os_draw_hidden_field('response_url', $oplata_args['response_url']) .
            os_draw_hidden_field('server_callback_url', $oplata_args['server_callback_url']) .
            os_draw_hidden_field('signature', $oplata_args['signature']);


        return $process_button_string;
    }

    protected function getSignature($data, $password, $encoded = true)
    {
        $data = array_filter($data, function($var) {
            return $var !== '' && $var !== null;
        });
        ksort($data);

        $str = $password;
        foreach ($data as $k => $v) {
            $str .= self::SIGNATURE_SEPARATOR . $v;
        }

        if ($encoded) {
            return sha1($str);
        } else {
            return $str;
        }
    }

    function before_process()
    {
        if ($_POST['order_status'] == self::ORDER_DECLINED) {
            $payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode(MODULE_PAYMENT_OPLATA_ERROR_DECLINE);
            os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
        }

        $response = $_POST;
        $responseSignature = $_POST['signature'];
        foreach ($response as $k => $v) {
            if (!in_array($k, self::$responseFields)) {
                unset($response[$k]);
            }
        }

        if ($this->getSignature($response, MODULE_PAYMENT_OPLATA_SECRET_KEY) != $responseSignature) {
            $payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode(MODULE_PAYMENT_OPLATA_ERROR_SIGNATURE);
            os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
        }

        if ($response['order_status'] != self::ORDER_APPROVED) {
//            $error = "Thank you for shopping with us. Your payment is processing. We will inform you about results.";
//            $payment_error_return = 'payment_error='.$this->code.'&error='.urlencode($error);
//            os_redirect(os_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
        }


        if ($response['order_status'] == self::ORDER_APPROVED) {
            // success
        }

        return false;
    }

    function after_process()
    {
        return false;
    }

    function output_error()
    {
        return false;
    }

    function check()
    {
        if (!isset($this->_check)) {
            $check_query = os_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_OPLATA_STATUS'");
            $this->_check = os_db_num_rows($check_query);
        }
        return $this->_check;
    }

    function install()
    {

        os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_OPLATA_STATUS', 'True', '6', '1', 'os_cfg_select_option(array(\'True\', \'False\'), ', now())");
        os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_OPLATA_SHOP_ID', '', '6', '2', now())");
        os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_OPLATA_SECRET_KEY', '', '6', '3', now())");
        os_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_OPLATA_ORDER_STATUS_ID', '0', '6', '4', 'os_cfg_pull_down_order_statuses(', 'os_get_order_status_name', now())");
    }

    function remove()
    {
        os_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys()
    {
        return array('MODULE_PAYMENT_OPLATA_STATUS', 'MODULE_PAYMENT_OPLATA_SHOP_ID', 'MODULE_PAYMENT_OPLATA_SECRET_KEY', 'MODULE_PAYMENT_OPLATA_ORDER_STATUS_ID');
    }

    function help()
    {
        return MODULE_PAYMENT_OPLATA_HELP;
    }

    function get_error()
    {
        if (isset ($_GET['ErrMsg']) && (strlen($_GET['ErrMsg']) > 0)) {
            $error = stripslashes(urldecode($_GET['ErrMsg']));
        } elseif (isset ($_GET['error']) && (strlen($_GET['error']) > 0)) {
            $error = stripslashes(urldecode($_GET['error']));
        } else {
            $error = MODULE_PAYMENT_OPLATA_TEXT_ERROR_MESSAGE;
        }

        return array('title' => MODULE_PAYMENT_OPLATA_TEXT_ERROR, 'error' => $error);
    }

}

?>