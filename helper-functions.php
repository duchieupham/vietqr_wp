<?php

/**
 * Write a message to the log file
 *
 * @param string $message The message to write to the log file
 */
function write_wp_log_to_file($message) {
	// Get the current date and time
	$date = date('Y-m-d H:i:s');

	// Initialize an empty log message variable
	$log_message = '';

	// Check if the message is an array
	if (is_array($message)) {
		// Convert the array to a formatted string
		$log_message = "[{$date}] " . print_r($message, true) . "\n";
	} else {
		// Format the log message with the date/time stamp
		$log_message = "[{$date}] {$message}\n";
	}

	// Set the log file path and name
	$log_file = VIETQR_PLUGIN_DIR . '/logs/wp-log.txt';

	// Open the log file in append mode
	$log_handle = fopen($log_file, 'a');

	// Write the log message to the file
	fwrite($log_handle, $log_message);

	// Close the log file handle
	fclose($log_handle);
}

/**
 * Random một chuỗi ký tự
 *
 * @return string
 * @throws Exception
 */
function random_char () {
	// Generate a random string of alphanumeric characters
	$randomString = bin2hex(random_bytes(5)); // Adjust the number of bytes as needed

	// Generate a timestamp to add uniqueness to the filename
	$timestamp = time();

	return $randomString . '_' . $timestamp;
}

/**
 * Convert timestamp to custom format
 *
 * @param $timestamp
 * @param $format
 *
 * @return string
 */
function convert_timestamp_to_date($timestamp, $format) {
	$seconds = $timestamp / 1000; // Convert milliseconds to seconds
	$dateTime = new DateTime();
	$dateTime->setTimestamp($seconds);
	return $dateTime->format($format);
}

/**
 * Get current domain name
 *
 * @return string
 */
function get_current_domainname () {
	return $_SERVER['HTTP_HOST'];
}

/**
 * Format any currency to number
 *
 * @param $number
 *
 * @return int
 */
function format_currency_to_number($number) {

	// Remove thousands separators (commas)
	$number = str_replace(",", "", $number);

	// Remove decimal separator and everything after it
	$number = strtok($number, ".");

	// Convert the number to an integer
	$number = intval($number);

	return $number;
}