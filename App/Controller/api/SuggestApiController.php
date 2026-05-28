<?php

namespace App\Controller\api;

use App\Service\FormatService;
use PHPMailer\PHPMailer\PHPMailer;

class SuggestApiController
{
    private FormatService $formatService;

    public function __construct(FormatService $formatService)
    {
        // Inject format service dependency
        $this->formatService = $formatService;
    }

    // Handle API suggestion request and return JSON
    public function index(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Use POST to submit a suggestion.',
                'categories' => $this->formatService->categoryDropDown(),
                'formats' => $this->formatService->formatArray(),
                'genres' => $this->formatService->genresArray()
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            exit;
        }

        $requestData = $this->getRequestData();

        $result = $this->handleForm($requestData);

        if (!empty($result['success'])) {
            http_response_code(200);
        } else {
            http_response_code(422);
        }

        echo json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function getRequestData(): array
    {
        $contentType = strtolower(trim(explode(';', $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '')[0] ?? ''));

        if ($contentType === 'application/json') {
            $rawBody = file_get_contents('php://input');
            $payload = json_decode($rawBody, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($payload)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid JSON request body.'
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                exit;
            }

            return $payload;
        }

        return $_POST;
    }

    // Process and validate form submission
    private function handleForm(array $requestData): array
    {
        $data = [
            'success' => false,
            'message' => null,
            'name' => null,
            'email' => null,
            'category' => null,
            'title' => null,
            'format' => null,
            'genre' => null,
            'year' => null,
            'details' => null
        ];

        // Sanitize user input
        $data['name'] = trim(filter_var($requestData['name'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS));
        $data['email'] = trim(filter_var($requestData['email'] ?? null, FILTER_SANITIZE_EMAIL));
        $data['category'] = trim(filter_var($requestData['category'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS));
        $data['title'] = trim(filter_var($requestData['title'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS));
        $data['format'] = trim(filter_var($requestData['format'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS));
        $data['genre'] = trim(filter_var($requestData['genre'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS));
        $data['year'] = trim(filter_var($requestData['year'] ?? null, FILTER_SANITIZE_NUMBER_INT));
        $data['details'] = trim(filter_var($requestData['details'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS));

        // Validate required fields
        if (
            empty($data['name']) ||
            empty($data['email']) ||
            empty($data['category']) ||
            empty($data['title'])
        ) {
            $data['message'] =
                'Please fill in the required fields: Name, Email, Category and Title';

            return $data;
        }

        // Honeypot spam protection
        if (!empty($requestData['address'] ?? null)) {
            $data['message'] = 'Bad form input';
            return $data;
        }

        // Validate email format
        if (!PHPMailer::validateAddress($data['email'])) {
            $data['message'] = 'Invalid email address';
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

        // Send email and return JSON response
        if ($mail->send()) {
            $data['success'] = true;
            $data['message'] = 'Suggestion submitted successfully.';
            return $data;
        }

        // Return mail error if sending fails
        $data['message'] = 'Mailer Error: ' . $mail->ErrorInfo;

        return $data;
    }
}
