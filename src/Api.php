<?php

namespace PM;

/**
 * Class API perfect money
 */
class Api
{
    private $wallet;
    private $passphrase;
    private $status_url;
    private $payment_url;
    private $nopayment_url;
    private $payee_name;
    private $account_id;
    private $password;
    private $currency;
    private $payment_url_method;
    private $nopayment_url_method;
    private $html_submit;
    private $check_ip;

    /**
     * PM API class constructor
     * 
     * @param string $wallet Perfect money wallet
     * @param string $passphrase Alternative passphrase
     * @param string $status_url Link to which payment notification will be sent
     * @param string $payment_url Link where the user will be redirected upon successful payment
     * @param string $nopayment_url Link where the user will be redirected upon successful payment
     * @param string $payee_name Receiver name
     * @param integer $account_id Account id (specify NULL if you do not need to request a balance or send money)
     * @param string $password Password account
     * @param string $currency USD, EUR or OAU. Currency must correspond to wallet you selected
     * @param string $payment_url_method what method to use for payment_url
     * @param string $nopayment_url_method what method to use for nopayment_url
     * @param string $html_submit Html code for submit button
     * @param string $check_ip Check ip for status_url, set false on problems
     * 
     * @return null
     */
    function __construct($wallet, $passphrase, $status_url, $payment_url, $nopayment_url, $payee_name, $account_id = NULL, $password = NULL, $currency = 'USD', $payment_url_method = 'GET', $nopayment_url_method = 'GET', $html_submit = '<input type="submit" value="Pay">', $check_ip = true)
    {
        $this->wallet = $wallet;
        $this->passphrase = $passphrase;
        $this->status_url = $status_url;
        $this->payment_url = $payment_url;
        $this->nopayment_url = $nopayment_url;
        $this->payee_name = $payee_name;
        $this->account_id = $account_id;
        $this->password = $password;
        $this->currency = $currency;
        $this->payment_url_method = $payment_url_method;
        $this->nopayment_url_method = $nopayment_url_method;
        $this->html_submit = $html_submit;
        $this->check_ip = $check_ip;
    }

    /**
     * Gets a payment form
     *
     * @param string $order_id Order id in your system
     * @param string $amount Amount money
     * @param string $description Description payment
     *
     * @return string Html code
     */
    function getForm($order_id, $amount, $description)
    {
        ob_start();
        include __DIR__.'/form.php';
        $html = ob_get_clean();
        return $html;
    }

    /**
     * Check incoming payment request
     * 
     * @param string $data Incoming data array, if set to false uses $_POST
     * 
     * @return array
     * On error returns an array with 'status' = false and a description of the error in the 'error' field.<br>
     * Returns an array on success:<br>
     * <b>success</b> : true<br>
     * <b>order_id</b> : Order id<br>
     * <b>amount</b> : Amount<br>
     * <b>transaction_id</b> : Transaction id in perfect money<br>
     * <b>payer</b> : Payer wallet<br>
     * <b>timestampgmt</b> : Operation time in GMT
     */
    function checkPay($data = false)
    {
        if ($data === false)
            $data = $_POST;
        if($this->check_ip && !in_array($_SERVER['REMOTE_ADDR'], [ '77.109.141.170', '91.205.41.208', '94.242.216.60', '78.41.203.75' ]))
            return [ 'status' => false, 'error' => 'unauthorized access' ];
        if (isset($data['PAYEE_ACCOUNT'], $data['PAYMENT_ID'], $data['PAYMENT_AMOUNT'], $data['PAYMENT_UNITS'], $data['PAYMENT_BATCH_NUM'], $data['PAYER_ACCOUNT'], $data['TIMESTAMPGMT'], $data['V2_HASH']))
        {
            $hash = strtoupper(md5($this->passphrase));
            $v2hash = strtoupper(md5(implode(':', [
                $data['PAYMENT_ID'],
                $data['PAYEE_ACCOUNT'],
                $data['PAYMENT_AMOUNT'],
                $data['PAYMENT_UNITS'],
                $data['PAYMENT_BATCH_NUM'],
                $data['PAYER_ACCOUNT'],
                $hash,
                $data['TIMESTAMPGMT']
            ])));
            if ($v2hash !== $data['V2_HASH']) {
                return [ 'status' => false, 'error' => 'wrong hash' ];
            }
            if ($data['PAYEE_ACCOUNT'] !== $this->wallet)
                return [ 'status' => false, 'error' => 'invalid payee account' ];
            if ($data['PAYMENT_UNITS'] !== $this->currency)
                return [ 'status' => false, 'error' => 'invalid currency' ];
            return [
                'status' => true,
                'order_id' => $data['PAYMENT_ID'],
                'amount' => $data['PAYMENT_AMOUNT'],
                'transaction_id' => $data['PAYMENT_BATCH_NUM'],
                'payer' => $data['PAYER_ACCOUNT'],
                'timestampgmt' => $data['TIMESTAMPGMT']
            ];
        }
        else
            return [ 'status' => false, 'error' => 'not enough data' ];
    }

    /**
     * Get balances
     * 
     * @return array
     * Returns an array with the key 'ERROR' on error containing an error<br>
     * On success, returns an array with of wallets and balances on them
     */
    function getBalance()
    {
        $client = new \GuzzleHttp\Client([]);
        $res = $client->request('POST', 'https://perfectmoney.is/acct/balance.asp', [
            'form_params' => [
                'AccountID' => $this->account_id,
                'PassPhrase' => $this->password
            ]
        ]);
        $res = $res->getBody()->getContents();
        $dom = new \PHPHtmlParser\Dom;
        $dom->loadStr($res);
        $list = $dom->find('input');
        $r = [];
        foreach ($list as $item) {
            $r[$item->name] = $item->value;
        }
        return $r;
    }
}