<?php

if (!defined('UPDRAFTCENTRAL_CLIENT_DIR')) die('No access.');

/**
 * - A container for RPC commands (white label reporting UpdraftCentral commands). Commands map exactly onto method names (and hence this class should not implement anything else, beyond the constructor, and private methods)
 * - Return format is array('response' => (string - a code), 'data' => (mixed));
 *
 * RPC commands are not allowed to begin with an underscore. So, any private methods can be prefixed with an underscore.
 */
class UpdraftCentral_Reporting_Commands extends UpdraftCentral_Commands {

	private $valid_statuses = array(
		'recurring',
		'manual',
	);

	private $max_number_of_recipients = 100;

	/**
	 * Update existing reports.
	 *
	 * @param array $reports Reports data which will be saved.
	 *
	 * @return void
	 */
	private function _update_existing_reports($reports) {
		update_option('updraftcentral_reporting_reports', $reports);
	}

	/**
	 * Normalize email address - lowercase and sanitize.
	 *
	 * @param string $email Email address to normalize.
	 *
	 * @return string Normalized email.
	 */
	private function _normalize_email($email) {
		return strtolower(sanitize_email($email));
	}

	/**
	 * Get existing reports.
	 *
	 * @return array List of all reports.
	 */
	private function _get_existing_reports() {
		return (array) get_option('updraftcentral_reporting_reports', array());
	}

	/**
	 * Get all the reports (scheduled or not scheduled).
	 *
	 * @return array An array of all reports.
	 */
	public function get_reports() {
		return $this->_response(array(
			'reports' => $this->_get_existing_reports(),
		));
	}

	/**
	 * Delete a report.
	 *
	 * @param array $data Data array containing report ID to delete.
	 *
	 * @return array An array containing the result of the current process
	 */
	public function delete_report($data) {
		// Permission check.
		if (!current_user_can('manage_options')) {
			$result = array("error" => true, "message" => "not_allowed");
			return $this->_response($result);
		}

		// Return early if valid data structure is not present.
		if (!is_array($data)) {
			$result = array("error" => true, "message" => "invalid_data");
			return $this->_response($result);
		}

		$report_id = empty($data['id']) ? '' : sanitize_key(strval($data['id']));

		// Return early if ID not supplied.
		if (empty($report_id)) {
			$result = array('error' => true, 'message' => 'missing_id');
			return $this->_response($result);
		}

		// Get the reports from options table.
		$reports = $this->_get_existing_reports();

		// Check if the ID to delete is present.
		if (!isset($reports[$report_id])) {
			$result = array('error' => true, 'message' => 'invalid_id');
			return $this->_response($result);
		}

		unset($reports[$report_id]);

		$this->_update_existing_reports($reports);

		$result = array("error" => false, "message" => "reports_updated");
		return $this->_response($result);
	}

	/**
	 * Add a new report.
	 *
	 * @param array $data Report information to add
	 *
	 * @return array An array containing the result of the current process
	 */
	public function add_report($data) {
		// Permission check.
		if (!current_user_can('manage_options')) {
			$result = array("error" => true, "message" => "not_allowed");
			return $this->_response($result);
		}

		// Return early if valid report structure is not present.
		if (!is_array($data)) {
			$result = array("error" => true, "message" => "invalid_report_data");
			return $this->_response($result);
		}

		$report_name = isset($data['name']) ? sanitize_text_field(strval($data['name'])) : '';

		if ('' === $report_name) {
			$result = array("error" => true, "message" => "empty_name");
			return $this->_response($result);
		}

		$report_status = empty($data['status']) ? '' : sanitize_text_field(strval($data['status']));

		if (!in_array($report_status, $this->valid_statuses)) {
			$result = array("error" => true, "message" => "status_invalid");
			return $this->_response($result);
		}

		$template_id = empty($data['template_id']) ? 0 : absint($data['template_id']);

		if (empty($template_id)) {
			$result = array("error" => true, "message" => "template_id_invalid");
			return $this->_response($result);
		}

		$recipients = (isset($data['recipients']) && is_array($data['recipients'])) ? $data['recipients'] : array();

		if (count($recipients) > $this->max_number_of_recipients) {
			$result = array("error" => true, "message" => "max_number_of_recipients_exceeded");
			return $this->_response($result);
		}

		$recipients = array_unique(array_map(array($this, '_normalize_email'), $recipients));

		// Sanity check for invalid email.
		foreach ($recipients as $email) {
			if (!is_email($email)) {
				$result = array(
					"error" => true,
					"message" => "recipients_invalid",
					"values" => array(
						'invalid_email' => $email,
					)
				);
				return $this->_response($result);
			}
		}

		if (empty($recipients)) {
			$result = array("error" => true, "message" => "no_recipients_provided");
			return $this->_response($result);
		}

		// Get existing reports from options table.
		$existing_reports = $this->_get_existing_reports();

		// Report.
		$report = array();

		$report_id = empty($data['id']) ? '' : sanitize_key(strval($data['id']));

		if (empty($report_id)) {
			// First report timestamp and formatted date.
			$next_report_timestamp = strtotime("+1 month", time());
			$next_report_formatted_date = date("j M, g:i a", $next_report_timestamp);

			$report_id = UpdraftPlus_Manipulation_Functions::generate_random_string(10);

			$report_id_generation_loops = 0;

			// Sanity check to check if the report ID exists.
			while (isset($existing_reports[$report_id])) {
				$report_id = UpdraftPlus_Manipulation_Functions::generate_random_string(10);
				++$report_id_generation_loops;

				// If we somehow exceed the max generation loops then return error - which will not happen in almost any case.
				if ($report_id_generation_loops > 10) {
					$result = array("error" => true, "message" => "report_id_generation_failed");
					return $this->_response($result);
				}
			}

			$report = array(
				'id' => $report_id,
				'name' => $report_name,
				'status' => $report_status,
				'template_id' => $template_id,
				'recipients' => $recipients,
				'last_report_timestamp' => 0,
				'last_report_formatted_date' => __('N/A', 'updraftplus'),
				'next_report_timestamp' => $next_report_timestamp,
				'next_report_formatted_date' => $next_report_formatted_date,
			);
		} elseif (!empty($existing_reports[$report_id])) {
			$report = $existing_reports[$report_id];

			$report['name'] = $report_name;
			$report['status'] = $report_status;
			$report['template_id'] = $template_id;
			$report['recipients'] = $recipients;
		} else {
			$result = array("error" => true, "message" => "report_does_not_exist");
			return $this->_response($result);
		}

		// Add the new report.
		$existing_reports[$report_id] = $report;

		// Update the reports.
		$this->_update_existing_reports($existing_reports);

		$result = array(
			"error" => false,
			"message" => "reports_updated",
			"values" => array(
				'report' => $report,
			)
		);

		return $this->_response($result);
	}
}
