<?php

namespace App\Controller;

use App\Service\FormatService;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class SuggestController
{
    private FormatService $formatService;

    public function __construct(FormatService $formatService)
    {
        // Inject format service dependency
        $this->formatService = $formatService;
    }

    // Display suggestion form page
    public function index()
    {
        $pageTitle = 'Suggest a media item';
        $section = 'suggest';
        $hideSearch = true;

        // Default form values
        $name = $email = $category = $title = $format = $genre = $year = $details = null;
        $error_message = null;

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->handleForm();

            // Extract returned form data
            extract($result);
        }

        // Load dropdown data
        $categories = $this->formatService->categoryDropDown();
        $formats = $this->formatService->formatArray();
        $genres = $this->formatService->genresArray();

        require BASE_PATH . '/view/suggest.php';
    }

    // Process and validate form submission
    private function handleForm(): array
    {
        $data = [
            'name' => null,
            'email' => null,
            'category' => null,
            'title' => null,
            'format' => null,
            'genre' => null,
            'year' => null,
            'details' => null,
            'error_message' => null
        ];

        // Sanitize user input
        $data['name'] = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS));
        $data['email'] = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $data['category'] = trim(filter_input(INPUT_POST, 'category', FILTER_SANITIZE_SPECIAL_CHARS));
        $data['title'] = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS));
        $data['format'] = trim(filter_input(INPUT_POST, 'format', FILTER_SANITIZE_SPECIAL_CHARS));
        $data['genre'] = trim(filter_input(INPUT_POST, 'genre', FILTER_SANITIZE_SPECIAL_CHARS));
        $data['year'] = trim(filter_input(INPUT_POST, 'year', FILTER_SANITIZE_NUMBER_INT));
        $data['details'] = trim(filter_input(INPUT_POST, 'details', FILTER_SANITIZE_SPECIAL_CHARS));

        // Validate required fields
        if (
            empty($data['name']) ||
            empty($data['email']) ||
            empty($data['category']) ||
            empty($data['title'])
        ) {
            $data['error_message'] =
                'Please fill in the required fields: Name, Email, Category and Title';

            return $data;
        }

        // Honeypot spam protection
        if (!empty($_POST['address'])) {
            $data['error_message'] = 'Bad form input';
            return $data;
        }

        // Validate email format
        if (!PHPMailer::validateAddress($data['email'])) {
            $data['error_message'] = 'Invalid email address';
            return $data;
        }

        /* SEND EMAIL */

        // Build email message body
        $email_body = "Name: {$data['name']}\n";
        $email_body .= "Email: {$data['email']}\n\n";
        $email_body .= "Category: {$data['category']}\n";
        $email_body .= "Title: {$data['title']}\n";
        $email_body .= "Format: {$data['format']}\n";
        $email_body .= "Genre: {$data['genre']}\n";
        $email_body .= "Year: {$data['year']}\n";
        $email_body .= "Details:\n{$data['details']}\n";

        // Configure PHPMailer
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = $_ENV['MAIL_HOST'];
        $mail->Port = $_ENV['MAIL_PORT'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->SMTPAuth = true;

        $mail->Username = $_ENV['MAIL_USERNAME'];
        $mail->Password = $_ENV['MAIL_PASSWORD'];

        // Set sender and receiver
        $mail->setFrom($_ENV['MAIL_FROM_EMAIL'], $_ENV['MAIL_FROM_NAME']);
        $mail->addReplyTo($data['email'], $data['name']);
        $mail->addAddress($_ENV['MAIL_FROM_EMAIL']);

        // Set email content
        $mail->Subject = 'Library Suggestion from: ' . $data['name'];
        $mail->Body = $email_body;

        // Send email and redirect on success
        if ($mail->send()) {
            header('Location: index.php?page=suggest&status=thanks');
            exit;
        }

        // Return mail error if sending fails
        $data['error_message'] = 'Mailer Error: ' . $mail->ErrorInfo;

        return $data;
    }
}
