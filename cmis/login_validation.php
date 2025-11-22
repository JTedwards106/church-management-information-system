<?php
	session_start();

	function validateForm($data) {
		$errors = array();
			
		// Student ID
		if (empty($data['mem_id'])) {
			$errors['mem_id'] = 'Member ID is required.';
		} elseif (!preg_match('/^\d{3}$/', $data['mem_id'])) {
			$errors['mem_id'] = 'Enter a valid 3-digit student ID (e.g. 002).';
		}

        // Password
        if (empty($data['password'])) {
            $errors['password'] = 'Password is required.';
        } elseif (!preg_match('/^(?=.*[0-9])(?=.*\W)[A-Za-z0-9\W]{8,}$/', $data['password'])) {
            $errors['password'] = 'Password must be at least 8 characters and include a number and symbol.';
        }
		
		return $errors;
	}

	// Course form submission
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$values = array(
			'mem_id' => trim($_POST['mem_id'] ?? ''),
			'password' => trim($_POST['password'] ?? '')

		);
			
		$errors = validateForm($values);
			
		if (empty($errors)) {
			// Validation passed - merge current page values into session and redirect to the module_info
			$form = $_SESSION['form_values'] ?? array();
			$form = array_merge($form, $values);
			$_SESSION['form_values'] = $form;
			header('Location: .php');
			exit;
		} else {
			// Validation failed - store errors and values in session, redirect back to course_info
			$_SESSION['errors'] = $errors;
			$_SESSION['old_values'] = $values;
			header('Location: login.php');
			exit;
			}
	}
?>