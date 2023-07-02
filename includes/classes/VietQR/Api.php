<?php

namespace VietQR;

/**
 * VietQR Api interface class
 */
class Api {
	private static $instance;

	protected string $protocol = "https";
	protected string $ip = "api.vietqr.org";
	protected string $port = "80";

	// Private constructor to prevent direct instantiation
	private function __construct() {

	}

	// Static method to retrieve the instance of the class
	public static function get_instance() {
		if (self::$instance === null) {
			self::$instance = new Api();
		}

		return self::$instance;
	}


	/**
	 * Get token
	 *
	 * @return array
	 */
	public function get_token () {
		$url = "$this->protocol://$this->ip/vqr/api/token_generate";
		$username = 'customer-bl-user05'; // test acc
		$password = 'Y3VzdG9tZXItYmwtdXNlcjA1'; // test acc

		// Prepare the request headers
		$headers = array(
			'Authorization: Basic ' . base64_encode($username . ':' . $password),
			'Content-Type: application/json'
		);

		// Prepare the POST data
		$data = array(
			// Add any required data here
		);

		// Initialize cURL
		$curl = curl_init($url);

		// Set the request options
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		// Execute the request
		$response = curl_exec($curl);

		// Check for errors
		// Validate the API response
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ($httpCode !== 200) {
			write_wp_log_to_file('API returned status code ' . $httpCode);
			return [];
		}

		// Close cURL
		curl_close($curl);

		// Output the response
		return json_decode($response);
	}


	/**
	 * Tạo giao dịch
	 *
	 * @return array
	 */
	public function generate_transaction (array $order_info) {
		// api info
		$url = "$this->protocol://$this->ip/vqr/api/qr/generate-customer";
		$token = Options::get_instance()->get_authorization_code();
		$selected_bank_account = Options::get_instance()->get_selected_bank_account();
		$content = $order_info["order_id"];

		// merge transaction info to default
		$data = [
			'bankAccount' => $selected_bank_account["bankAccount"],
			'amount' => $order_info["amount"],
			'content' => $content,
			'bankCode' => $selected_bank_account["bankCode"],
			'userBankName' => $selected_bank_account["bankName"],
			'orderId' => $order_info["order_id"]
		];

		// Prepare the request headers
		$headers = array(
			'Authorization: Bearer ' . $token,
			'Content-Type: application/json'
		);

		// Prepare the POST data

		// Initialize cURL
		$curl = curl_init($url);

		// Set the request options
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		// Execute the request
		$response = curl_exec($curl);

		// Validate the API response
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ($httpCode !== 200) {
			write_wp_log_to_file('API returned status code ' . $httpCode);
			return [];
		}

		// Close cURL
		curl_close($curl);

		// Output the response
		return json_decode($response);
	}


	/**
	 * Lấy danh sách bank
	 *
	 * @return array
	 */
	public function get_bank_code_list () {
		return array(
			array(1, 'ABB', 'Ngân hàng TMCP An Bình'),
			array(2, 'ACB', 'Ngân hàng TMCP Á Châu'),
			array(3, 'BAB', 'Ngân hàng TMCP Bắc Á'),
			array(4, 'BIDV', 'Ngân hàng TMCP Đầu tư và Phát triển Việt Nam'),
			array(5, 'BVB', 'Ngân hàng TMCP Bảo Việt'),
			array(6, 'CAKE', 'TMCP Việt Nam Thịnh Vượng - Ngân hàng số CAKE by VPBank'),
			array(7, 'CBB', 'Ngân hàng Thương mại TNHH MTV Xây dựng Việt Nam'),
			array(8, 'CIMB', 'Ngân hàng TNHH MTV CIMB Việt Nam'),
			array(9, 'COOPBANK', 'Ngân hàng Hợp tác xã Việt Nam'),
			array(10, 'DBS', 'DBS Bank Ltd - Chi nhánh Thành phố Hồ Chí Minh'),
			array(11, 'DOB', 'Ngân hàng TMCP Đông Á'),
			array(12, 'EIB', 'Ngân hàng TMCP Xuất Nhập khẩu Việt Nam'),
			array(13, 'GPB', 'Ngân hàng Thương mại TNHH MTV Dầu Khí Toàn Cầu'),
			array(14, 'HDB', 'Ngân hàng TMCP Phát triển Thành phố Hồ Chí Minh'),
			array(15, 'HLBVN', 'Ngân hàng TNHH MTV Hong Leong Việt Nam'),
			array(16, 'HSBC', 'Ngân hàng TNHH MTV HSBC (Việt Nam)'),
			array(17, 'IBK - HCM', 'Ngân hàng Công nghiệp Hàn Quốc - Chi nhánh TP. Hồ Chí Minh'),
			array(18, 'IBK - HN', 'Ngân hàng Công nghiệp Hàn Quốc - Chi nhánh Hà Nội'),
			array(19, 'ICB', 'Ngân hàng TMCP Công thương Việt Nam'),
			array(20, 'IVB', 'Ngân hàng TNHH Indovina'),
			array(21, 'KBank', 'Ngân hàng Đại chúng TNHH Kasikornbank'),
			array(22, 'KBHCM', 'Ngân hàng Kookmin - Chi nhánh Thành phố Hồ Chí Minh'),
			array(23, 'KBHN', 'Ngân hàng Kookmin - Chi nhánh Hà Nội'),
			array(24, 'KLB', 'Ngân hàng TMCP Kiên Long'),
			array(25, 'LPB', 'Ngân hàng TMCP Bưu Điện Liên Việt'),
			array(26, 'MB', 'Ngân hàng TMCP Quân đội'),
			array(27, 'MSB', 'Ngân hàng TMCP Hàng Hải'),
			array(28, 'NAB', 'Ngân hàng TMCP Nam Á'),
			array(29, 'NCB', 'Ngân hàng TMCP Quốc Dân'),
			array(30, 'NHB HN', 'Ngân hàng Nonghyup - Chi nhánh Hà Nội'),
			array(31, 'OCB', 'Ngân hàng TMCP Phương Đông'),
			array(32, 'Oceanbank', 'Ngân hàng Thương mại TNHH MTV Đại Dương'),
			array(33, 'PBVN', 'Ngân hàng TNHH MTV Public Việt Nam'),
			array(34, 'PGB', 'Ngân hàng TMCP Xăng dầu Petrolimex'),
			array(35, 'PVCB', 'Ngân hàng TMCP Đại Chúng Việt Nam'),
			array(36, 'SCB', 'Ngân hàng TMCP Sài Gòn'),
			array(37, 'SCVN', 'Ngân hàng TNHH MTV Standard Chartered Bank Việt Nam'),
			array(38, 'SEAB', 'Ngân hàng TMCP Đông Nam Á'),
			array(39, 'SGICB', 'Ngân hàng TMCP Sài Gòn Công Thương'),
			array(40, 'SHB', 'Ngân hàng TMCP Sài Gòn - Hà Nội'),
			array(41, 'SHBVN', 'Ngân hàng TNHH MTV Shinhan Việt Nam'),
			array(42, 'STB', 'Ngân hàng TMCP Sài Gòn Thương Tín'),
			array(43, 'TCB', 'Ngân hàng TMCP Kỹ thương Việt Nam'),
			array(44, 'TIMO', 'Ngân hàng số Timo by Ban Viet Bank (Timo by Ban Viet Bank)'),
			array(45, 'TPB', 'Ngân hàng TMCP Tiên Phong'),
			array(46, 'Ubank', 'TMCP Việt Nam Thịnh Vượng - Ngân hàng số Ubank by VPBank'),
			array(47, 'UOB', 'Ngân hàng United Overseas - Chi nhánh TP. Hồ Chí Minh'),
			array(48, 'VAB', 'Ngân hàng TMCP Việt Á'),
			array(49, 'VBA', 'Ngân hàng Nông nghiệp và Phát triển Nông thôn Việt Nam'),
			array(50, 'VCB', 'Ngân hàng TMCP Ngoại Thương Việt Nam'),
			array(51, 'VCCB', 'Ngân hàng TMCP Bản Việt'),
			array(52, 'VIB', 'Ngân hàng TMCP Quốc tế Việt Nam'),
			array(53, 'VIETBANK', 'Ngân hàng TMCP Việt Nam Thương Tín'),
			array(54, 'VNPTMONEY', 'Trung tâm dịch vụ tài chính số VNPT - Chi nhánh Tổng công ty truyền thông (VNPT Fintech)'),
			array(55, 'VPB', 'Ngân hàng TMCP Việt Nam Thịnh Vượng'),
			array(56, 'VRB', 'Ngân hàng Liên doanh Việt - Nga'),
			array(57, 'VTLMONEY', 'Tổng Công ty Dịch vụ số Viettel - Chi nhánh tập đoàn công nghiệp viễn thông Quân Đội'),
			array(58, 'WVN', 'Ngân hàng TNHH MTV Woori Việt Nam')
		);
	}

	/**
	 * Lấy thông tin bank dựa trên bank code
	 *
	 * @return array
	 */
	public function get_bank_info_from_code (string $bank_code) {
		$bank_list = $this->get_bank_code_list();

		foreach ($bank_list as $bank) {
			if ($bank[1] === $bank_code)
				return  $bank;
		}

		return ["Không tồn tại bankcode này"];
	}


	/**
	 * Kết ối lấy thông tin account ngân hàng của tk VietQR
	 *
	 * @return string[]
	 */
	public function get_bank_accounts () {
		$token = Options::get_instance()->get_authorization_code();

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "$this->protocol://$this->ip/vqr/api/account-bank/wp",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				"Authorization: Bearer $token",
			),
		));

		// Execute the request
		$response = curl_exec($curl);

		// Validate the API response
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ($httpCode !== 200) {
			write_wp_log_to_file('API returned status code ' . $httpCode);
			write_wp_log_to_file(curl_error($curl));
			return [];
		}

		// Close cURL
		curl_close($curl);

		// Output the response
		return json_decode($response);
	}


	/**
	 * Lấy hình logo của ngân hàng
	 * @param string $img_id
	 *
	 * @return mixed|void|null
	 */
	public function get_bank_image (string $img_id) {
		$token = Options::get_instance()->get_authorization_code();

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "$this->protocol://$this->ip/vqr/api/images/$img_id",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer $token",
			),
		));

		// Execute the request
		$response = curl_exec($curl);

		// Validate the API response
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ($httpCode !== 200) {
			write_wp_log_to_file('API returned status code ' . $httpCode);
			return [];
		}

		// Close cURL
		curl_close($curl);

		// Output the response
		return json_decode($response);
	}

	/**
	 * Update bank account sync check
	 *
	 * @param string $bank_id
	 * @param bool $is_check
	 *
	 * @return mixed|void|null
	 */
	public function sync_check (string $bank_id, bool $is_check) {
		$token = Options::get_instance()->get_authorization_code();

		$curl = curl_init();

		$data = [
			"bankId" => $bank_id,
			"syncWp" => $is_check
		];

		curl_setopt_array($curl, array(
			CURLOPT_URL => "$this->protocol://$this->ip/vqr/api/account-bank/wp/sync",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				"Authorization: Bearer $token"
			),
		));

		// Execute the request
		$response = curl_exec($curl);

		// Validate the API response
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ($httpCode !== 200) {
			write_wp_log_to_file('API returned status code ' . $httpCode);
			return [];
		}else {
			write_wp_log_to_file('Callback api thành công ' . $httpCode);
		}

		// Close cURL
		curl_close($curl);

		// Output the response
		return json_decode($response);
	}


	/**
	 * Lấy số dư tài khoản ứng với token key
	 *
	 * @return array|mixed
	 */
	public function get_account_current () {
		$token = Options::get_instance()->get_authorization_code();

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "$this->protocol://$this->ip/vqr/api/account-wallet",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer $token",
			),
		));

		// Execute the request
		$response = curl_exec($curl);

		// Validate the API response
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ($httpCode !== 200) {
			write_wp_log_to_file('API returned status code ' . $httpCode);
			return [];
		}

		// Close cURL
		curl_close($curl);

		// Output the response
		return json_decode($response);
	}
}