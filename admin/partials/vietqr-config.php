<?php

/**
 * VietQR admin config option page
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    VietQR_Plugin
 * @subpackage VietQR_Plugin/admin/partials
 */

use VietQR\Api;
use VietQR\Options;

// update list account & selected bank

$bank_accounts_list = Api::get_instance()->get_bank_accounts() ?? [];
Options::get_instance()->set_bank_account_list($bank_accounts_list);

foreach ($bank_accounts_list as $account) {
    if ($account->syncAccount)
	    Options::get_instance()->set_selected_bank_account(get_object_vars($account));
}

// update current
$account_current = Api::get_instance()->get_account_current() ?? [];
Options::get_instance()->set_account_current($account_current->amount);

?>

<!--Render-->

<div class="wrap">

    <div class="p_vietqr-admin">
        <div>
            <h1><?php echo __('CONFIG WORDPRESS TO VIETQR CONNECTION', 'vietqr-plugin'); ?></h1>
            <form method="post" action="options.php">
				<?php
				settings_fields('vietqr_options');
				do_settings_sections('vietqr_options');
				?>

                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row">Thiết lập</th>
                        <td></td>
                    </tr>
                    </tbody>
                </table>

                <div style="padding:15px; border-radius: 10px; border: 1px solid gray;">
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php echo __('Enabled/Disabled', 'vietqr-plugin'); ?></th>
                            <td>
                                <label><input type="checkbox" name="vietqr_bank_transfer_enabled" value="1" <?php checked(Options::get_instance()->get_bank_transfer_enabled(), '1'); ?>> <?php echo __('Turn on bank transfer', 'vietqr-plugin'); ?></label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo __('VietQR', 'vietqr-plugin'); ?></th>
                            <td>
                                <label><input type="checkbox" name="vietqr_qr_code_enabled" value="1" <?php checked(Options::get_instance()->get_qr_code_enabled(), '1'); ?>> <?php echo __('Enable QR Code', 'vietqr-plugin'); ?></label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo __('Transaction prefix', 'vietqr-plugin'); ?></th>
                            <td>
                                <input type="text" name="vietqr_transaction_prefix" class="regular-text" value="<?php echo esc_attr(Options::get_instance()->get_transaction_prefix()); ?>">
                            </td>
                        </tr>
                    </table>
                </div>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php echo __('Quick connect Wordpress with VietQR', 'vietqr-plugin'); ?></th>
                        <td>
                            <a href="https://vietqr.vn/ecom" target="_blank" class="button-primary"><?php echo __('Connect Wordpress with VietQR', 'vietqr-plugin'); ?></a>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo __('Authorization code', 'vietqr-plugin'); ?></th>
                        <td>
                            <div style="display: flex; gap: 10px;">
                                <input type="text" name="vietqr_authorization_code" class="regular-text" value="<?php echo esc_attr(Options::get_instance()->get_authorization_code()); ?>" disabled>

                                <!--                            Edit authorization code button-->
                                <div class="edit-authorization-code">
                                    <button id="edit-vietqr-authorization-code" class="button-primary"><?php echo __('Sửa', 'vietqr-plugin'); ?></button>
                                    <button id="update-vietqr-authorization-code" class="button-primary" style="display: none;"  ><?php echo __('Lưu', 'vietqr-plugin'); ?></button>
                                </div>
                            </div>
                            <small><a href="https://vietqr.com/Huong-dan-get-code-vietqr-vn-wp.html" target="_blank"><?php echo __('How to get VietQR authorization code', 'vietqr-plugin'); ?></a></small>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo __('Account on/off', 'vietqr-plugin'); ?></th>
                        <td>
                            <ul class="checkbox-list">
								<?php
								$accounts = Options::get_instance()->get_bank_account_list();
								if (!empty($accounts)) {
									foreach ($accounts as $account) {
										?>
                                        <li><label><input type="radio" name="vietqr_selected_bank_account" value="<?php echo $account->id; ?>" <?php checked($account->syncAccount); ?>> <?php echo esc_html("{$account->bankAccount} - {$account->userBankName} - {$account->bankName}"); ?></label></li>
										<?php
									}
                                } else {
                                    echo __("Không tìm thấy tài khoản nào", "vietqr-plugin");
                                }
								?>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo __('VietQR current', 'vietqr-plugin'); ?></th>
                        <td>
                            <p><span style="font-size : 20px; display: inline-block; margin-right: 20px;"><?php echo number_format(Options::get_instance()->get_account_current()) . " VNĐ"; ?></span>
                                <a href="https://vietqr.vn/naptk" target="_blank" class="button-primary"><?php echo __('Deposit into VietQR account', 'vietqr-plugin'); ?></a></p>
                        </td>
                    </tr>
                </table>

				<?php submit_button(); ?>
            </form>
        </div>

        <div>
            <div class="vietqr-logo">
                <img src="<?php echo VIETQR_PLUGIN_ADMIN_IMG_URL . "/vietqr_payment_1x.png" ?>" alt="vietqr_payment">
            </div>

        </div>
    </div>

    <!--            contact info-->
    <p class="contact-info">
        <span>Power by <a href="https://vietqr.vn/" target="_blank">Vietqr.vn</a> / <a href="https://vietqr.com/" target="_blank">Vietqr.com</a> / <a href="https://vietqr.org/" target="_blank">Vietqr.org</a></span>
        <span><a href="tel:19006234">Hotline: 1900 6234</a></span>
    </p>
</div>

<script>
    (function ($) {
        $(document).ready(function () {
            const editCodeBlock = $(".edit-authorization-code");
            const editCodeBtn = $("#edit-vietqr-authorization-code");
            const saveCodeBtn = $("#update-vietqr-authorization-code");
            const codeInput = $('[name="vietqr_authorization_code"]');

            const editAuthorizationCode = (e) => {
                e.preventDefault();
                editCodeBtn.css("display", "none");
                editCodeBtn.prop("disabled", true);
                saveCodeBtn.prop("disabled", false);
                saveCodeBtn.css("display", "inline-block");
                codeInput.prop("disabled", false);
            }

            const saveAuthorizationCode = (e) => {
                e.preventDefault();
                codeInput.prop("disabled", true);
                editCodeBtn.prop("disabled", true);
                saveCodeBtn.prop("disabled", true);
                let text = saveCodeBtn.text();
                saveCodeBtn.html('<div class="loading-spinner"></div>');

                const authorizationCode = codeInput.val();

                // Create an AJAX request
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>', // WordPress AJAX URL
                    type: 'POST',
                    data: {
                        action: 'save_authorization_code', // AJAX action name
                        authorization_code: authorizationCode, // Data to be sent
                    },
                    success: function(response) {
                        // Handle the AJAX response
                        console.log(response);
                        alert(response.data.message);

                        if (response.data.success) {
                            location.reload();
                        }

                        editCodeBtn.css("display", "inline-block");
                        saveCodeBtn.css("display", "none");
                        editCodeBtn.prop("disabled", false);
                        saveCodeBtn.prop("disabled", true);
                        saveCodeBtn.html(text);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Handle AJAX error
                        console.log('AJAX request failed: ' + errorThrown);

                        editCodeBtn.css("display", "inline-block");
                        saveCodeBtn.css("display", "none");
                        editCodeBtn.prop("disabled", false);
                        saveCodeBtn.prop("disabled", true);
                        saveCodeBtn.html(text);
                    }
                });
            }

            editCodeBtn.click(editAuthorizationCode);
            saveCodeBtn.click(saveAuthorizationCode);
        })
    })(jQuery)
</script>