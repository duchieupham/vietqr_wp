<?php
/**
 * Woocommerce thanks you page custom
 */

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use VietQR\Api;
use VietQR\Options;

// Get order data
$order_id = get_query_var("order_id");
$order = wc_get_order($order_id);

// Check order status
$is_completed = ($order->get_status() === "completed");

// Get bank account data
$bank_account = Options::get_instance()->get_selected_bank_account();

// Get QR Code data
$transaction = Api::get_instance()->generate_transaction([
	"amount" => format_currency_to_number($order->get_total()),
	"order_id" => $order->get_id(),
]);

// Update woocommerce vietqr_content
update_post_meta($order_id, 'vietqr_transaction_content', $transaction->content);

// The string you want to encode in the QR code
$data = $transaction->qrCode ?? "";

// Generate the QR code image using the PHP QR Code library
$writer = new PngWriter();
try {
	// Create a new QR code instance
	$qrCode = QrCode::create($data)
		->setSize(300);
    $result = $writer->write($qrCode);
	$dataUri = $result->getDataUri();

} catch (Throwable $e) {
	// Handle any exceptions that occur during QR code generation
	echo "QR code generation failed: " . $e->getMessage();
}

// Generate the <img> tag using the base64 encoded image data
$img_tag = '<img id="vietqr-code-scan" width="250px" height="250px" src="' . $dataUri . '" alt="QR Code" />';
?>

    <!-- jQuery Modal -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />

    <!--inline style for download image-->
    <style>
        .vietqr-scan__img {
            margin-bottom: 10px;
        }

        .vietqr-scan__img > div:first-child {
            margin-bottom: 15px;
        }
    </style>

    <div class="vietqr-transaction-info-btn">
        <img src="<?php echo VIETQR_PLUGIN_PUBLIC_IMG_URL . "/check-mark.png" ?>" alt="check mark">
        <div>
            <p>Đặt hàng thành công</p>
            <p>Mã đơn hàng <span><?php echo $order_id; ?></span></p>
            <p>Cám ơn bạn đã mua hàng</p>
            <p><a id="vietqr-transaction-info-btn__btn" class="vietqr-button" href="#ex1" rel="modal:open"><?php echo __("Xem thông tin thanh toán", "vietqr-plugin"); ?></a></p>
        </div>
    </div>

    <!-- Modal HTML embedded directly into document -->
    <div id="ex1" class="modal vietqr-transaction-wrapper" style="z-index: 10">
        <!--        Đơn đã thanh toán thành công-->
        <div id="vietqr-transaction-completed" class="vietqr-transaction vietqr-transaction-success" style="<?php echo (!$is_completed) ? "display: none" : "block"; ?>">
            <p style="text-align: right; margin-bottom: 0;">
                <small>Power by <a href="https://vietqr.vn/" target="_blank">Vietqr.vn</a> / <a href="https://vietqr.com/" target="_blank">Vietqr.com</a> / <a target="_blank" href="https://vietqr.org/">Vietqr.org</a></small>
            </p>
            <p class="vietqr-transaction-success__title"><?php echo __("ĐƠN HÀNG ĐÃ THANH TOÁN THÀNH CÔNG", "vietqr-plugin") ?></p>
            <p class="vietqr-transaction-success__total"><?php echo number_format($order->get_total()); ?> <small>VNĐ</small></p>
            <table>
                <tr>
                    <td style="white-space: nowrap; width: auto;">Thời gian:</td>
                    <td style="white-space: nowrap; width: auto;"><?php echo convert_timestamp_to_date(get_post_meta($order_id, "vietqr_transaction_time", true), "d-m-Y h:i:s"); ?></td>
                </tr>
                <tr>
                    <td style="white-space: nowrap; width: auto;">Ngân hàng:</td>
                    <td style="white-space: nowrap; width: auto;"><?php echo $bank_account["bankName"]; ?></td>
                </tr>
                <tr>
                    <td style="white-space: nowrap; width: auto;">Tài khoản:</td>
                    <td style="white-space: nowrap; width: auto;"><?php echo $bank_account["bankAccount"]; ?></td>
                </tr>
                <tr>
                    <td style="white-space: nowrap; width: auto;">Nội dung:</td>
                    <td style="white-space: nowrap; width: auto;"><?php echo get_post_meta($order_id, "vietqr_transaction_content", true); ?></td>
                </tr>
                <tr>
                    <td style="white-space: nowrap; width: auto;">Mã đơn hàng:</td>
                    <td style="white-space: nowrap; width: auto;"><?php echo $order->get_id() . "." . get_current_domainname(); ?></td>
                </tr>
            </table>

        </div>

        <!--        Đơn chưa thanh toán-->
        <div id="vietqr-transaction-not-completed" class="vietqr-transaction" style="<?php echo ($is_completed) ? "display: none" : "block"; ?>">
            <h2 id="popup-modal-title vietqr-transaction__title" style="font-weight: bold; margin-top: 0;"><?php echo __("Chuyển khoản ngân hàng", "vietqr-plugin"); ?></h2>
            <div class="vietqr-transaction__content">
                <div class="vietqr-scan" style="text-align: center">
                    <h3><?php echo __("Cách 1: Chuyển khoản bằng mã QR", "vietqr-plugin"); ?></h3>

                    <div class="vietqr-scan__content">
                        <div id="vietqr-scan__print-area" class="vietqr-scan__print-area" style="padding: 0 8px;">
                            <p><?php echo __("Mở App Ngân Hàng Quét QR Code", "vietqr-plugin"); ?></p>

                            <!--QR start-->
                            <div id="vietqr-scan__img" class="vietqr-scan__img">
                                <div><img width="120px" src="<?php echo VIETQR_PLUGIN_PUBLIC_IMG_URL . "/vietqr_payment_1x.png" ?>" alt="vietqr_payment"></div>
                                <div><?php echo $img_tag; ?></div>
                            </div>
                            <!--QR end-->

                            <p style="margin-bottom: 18px;"><?php echo __("58 ngân hàng hỗ trợ quét mã VietQR", "vietqr-plugin"); ?></p>
                        </div>

                        <p style="margin-block: 0;">
                            <button style="margin-right: 10px;" id="vietqr-save-qr" class="vietqr-button vietqr-save-qr"><?php echo __("Lưu mã QR","vietqr-plugin"); ?></button>
                            <button id="vietqr-print-qr" class="vietqr-button vietqr-save-qr"><?php echo __("In mã QR","vietqr-plugin"); ?></button>
                        </p>
                    </div>
                </div>
                <div class="vietqr-manual">
                    <h3><?php echo __('Cách 2: Chuyển khoản thủ công theo thông tin', "vietqr-plugin"); ?></h3>

                    <div class="vietqr-manual__content">
                        <table id="vietqr-manual__table" class="vietqr-manual__table">
                            <tr>
                                <td><?php echo __('Ngân hàng'); ?></td>
                                <td><?php echo $bank_account["bankName"] ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><?php echo __('Chủ tài khoản'); ?></td>
                                <td><span id="transaction-bank"><?php echo $bank_account["userBankName"] ?></span></td>
                                <td><button class="vietqr-button" id="copy-bank"><?php echo __('Sao chép'); ?></button></td>
                            </tr>
                            <tr>
                                <td><?php echo __('Số tài khoản'); ?></td>
                                <td><span id="transaction-account"><?php echo $bank_account["bankAccount"] ?></span></td>
                                <td><button class="vietqr-button" id="copy-account"><?php echo __('Sao chép'); ?></button></td>
                            </tr>
                            <tr>
                                <td><?php echo __('Số tiền'); ?></td>
                                <td><span id="transaction-amount"><?php echo number_format($order->get_total()); ?></span></td>
                                <td><button class="vietqr-button" id="copy-amount"><?php echo __('Sao chép'); ?></button></td>
                            </tr>
                            <tr>
                                <td><?php echo __('Nội dung'); ?></td>
                                <td><span id="transaction-content" class="vietqr-transaction-text"><?php echo $transaction->content; ?></span></td>
                                <td><button class="vietqr-button" id="copy-content"><?php echo __('Sao chép'); ?></button></td>
                            </tr>
                            <tr>
                                <td><?php echo __('Mã đơn hàng'); ?></td>
                                <td><span id="transaction-temp-code" class="vietqr-transaction-text"><?php echo $order->get_id() . "." . get_current_domainname(); ?></span></td>
                                <td><button class="vietqr-button" id="copy-content"><?php echo __('Sao chép'); ?></button></td>
                            </tr>
                        </table>

                        <p style="text-align: left">
                            Lưu ý: nhập chính xác nội dung <span class="vietqr-transaction-text"><?php echo $transaction->content; ?></span> khi chuyển khoản
                            bạn sẽ nhận được email (hoặc SMS) xác nhận khi giao dịch thành công.
                        </p>

                        <p style="text-align: right; margin-bottom: 0;">
                            <small>Power by <a href="https://vietqr.vn/" target="_blank">Vietqr.vn</a> / <a href="https://vietqr.com/" target="_blank">Vietqr.com</a> / <a target="_blank" href="https://vietqr.org/">Vietqr.org</a></small>
                        </p>
                    </div>
                </div>
            </div>

            <div style="text-align: right">
                <button id="vietqr-recheck" class="vietqr-button vietqr-recheck"><?php echo __('Tôi đã thanh toán', "vietqr-plugin"); ?></button>
            </div>
        </div>
    </div>
    <script>
        (function ($) {

            const copyBank = () => {
                var ele = document.getElementById('transaction-bank');

                // Create a temporary input element
                var input = document.createElement('input');
                input.value = ele.innerText;
                document.body.appendChild(input);

                // Copy the text from the input
                input.select();
                input.setSelectionRange(0, 99999);
                document.execCommand('copy');

                // Remove the temporary input element
                document.body.removeChild(input);
                document.execCommand('copy');
            }

            const copyAccount = () => {
                var ele = document.getElementById('transaction-account');

                // Create a temporary input element
                var input = document.createElement('input');
                input.value = ele.innerText;
                document.body.appendChild(input);

                // Copy the text from the input
                input.select();
                input.setSelectionRange(0, 99999);
                document.execCommand('copy');

                // Remove the temporary input element
                document.body.removeChild(input);
                document.execCommand('copy');
            }

            const copyAmount = () => {
                var ele = document.getElementById('transaction-amount');

                // Create a temporary input element
                var input = document.createElement('input');
                var numberString = ele.innerText;

                input.value = parseInt(numberString.replace(/\./g, ''));
                document.body.appendChild(input);

                // Copy the text from the input
                input.select();
                input.setSelectionRange(0, 99999);
                document.execCommand('copy');

                // Remove the temporary input element
                document.body.removeChild(input);
                document.execCommand('copy');
            }

            const copyContent = () => {
                var ele = document.getElementById('transaction-content');

                // Create a temporary input element
                var input = document.createElement('input');
                input.value = ele.innerText;
                document.body.appendChild(input);

                // Copy the text from the input
                input.select();
                input.setSelectionRange(0, 99999);
                document.execCommand('copy');

                // Remove the temporary input element
                document.body.removeChild(input);
                document.execCommand('copy');
            }

            const recheckTransaction = () => {
                $('#vietqr-recheck').prop('disabled', true);

                // Get the order ID from PHP variable $order_id
                const order_id = <?php echo json_encode($order_id); ?>;

                // AJAX request
                $.ajax({
                    url: '/wp-json/vietqr/recheck',
                    method: 'POST',
                    data: { order_id: order_id },
                    success: function(response) {
                        // Request was successful, log the response
                        if (response.data.status) {
                            $("#vietqr-transaction-completed").css("display", "block");
                            $("#vietqr-transaction-not-completed").css("display", "none");
                        }

                        $('#vietqr-recheck').prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        // Request failed, log the error
                        console.error('Error: ' + xhr.status);

                        $('#vietqr-recheck').prop('disabled', false);
                    }
                });
            };

            const printQrImage = () => {
                var originalElement = document.querySelector('.vietqr-manual__content');
                var clonedElement = originalElement.cloneNode(true);
                let image = document.getElementById("vietqr-scan__print-area");

                image.appendChild(clonedElement);

                domtoimage.toJpeg(document.getElementById('vietqr-scan__print-area'), {bgcolor : 'white'})
                    .then(function (dataUrl) {
                        printJS(dataUrl, 'image');
                        image.removeChild(clonedElement);
                    })
            }

            const saveQrImage = () => {
                var originalElement = document.querySelector('.vietqr-manual__content');
                var clonedElement = originalElement.cloneNode(true);
                let image = document.getElementById("vietqr-scan__print-area");

                image.appendChild(clonedElement);

                domtoimage.toJpeg(document.getElementById('vietqr-scan__print-area'), {bgcolor : 'white'})
                    .then(function (dataUrl) {
                        var link = document.createElement('a');
                        let filename = document.getElementById('transaction-temp-code').innerHTML;
                        link.download = filename + ".jpeg";
                        link.href = dataUrl;
                        link.click();
                        image.removeChild(clonedElement);
                    });
            }

            $(document).ready(function () {
                $("#copy-bank").click(copyBank);
                $("#copy-account").click(copyAccount);
                $("#copy-amount").click(copyAmount);
                $("#copy-content").click(copyContent);
                $('#vietqr-recheck').click(recheckTransaction);
                $("#vietqr-transaction-info-btn__btn").click();
                $("#vietqr-save-qr").click(saveQrImage);
                $("#vietqr-print-qr").click(printQrImage);

	            <?php if (!$is_completed) : ?>
                    var intervalId = setInterval(() => {
                        const order_id = <?php echo json_encode($order_id); ?>;

                        // AJAX request
                        $.ajax({
                            url: '/wp-json/vietqr/recheck',
                            method: 'POST',
                            data: { order_id: order_id },
                            success: function(response) {
                                // Request was successful, log the response
                                if (response.data.status) {
                                    if (response.data.status) {
                                        $("#vietqr-transaction-completed").css("display", "block");
                                        $("#vietqr-transaction-not-completed").css("display", "none");
                                    }

                                    clearInterval(intervalId);
                                }
                            },
                            error: function(xhr, status, error) {
                                // Request failed, log the error
                                console.error('Error: ' + xhr.status);
                            }
                        });

                        console.log("background check");
                    }, 5000);
	            <?php endif; ?>
            })
        })(jQuery)
    </script>
<?php