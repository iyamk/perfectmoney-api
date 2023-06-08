<form action="https://perfectmoney.com/api/step1.asp" method="post" accept-charset="utf-8">
    <input type="hidden" name="PAYEE_ACCOUNT" value="<?= $this->wallet ?>">
    <input type="hidden" name="PAYEE_NAME" value="<?= $this->payee_name ?>">
    <input type="hidden" name="PAYMENT_AMOUNT" value="<?= $amount ?>">
    <input type="hidden" name="PAYMENT_UNITS" value="<?= $this->currency ?>">
    <input type="hidden" name="PAYMENT_ID" value="<?= $order_id ?>">
    <input type="hidden" name="STATUS_URL" value="<?= $this->status_url ?>">
    <input type="hidden" name="PAYMENT_URL" value="<?= $this->payment_url ?>">
    <input type="hidden" name="PAYMENT_URL_METHOD" value="<?= $this->payment_url_method ?>">
    <input type="hidden" name="NOPAYMENT_URL" value="<?= $this->nopayment_url ?>">
    <input type="hidden" name="NOPAYMENT_URL_METHOD" value="<?= $this->nopayment_url_method ?>">
    <input type="hidden" name="SUGGESTED_MEMO" value="<?= $description ?>">
    <?= $this->html_submit ?>
</form>