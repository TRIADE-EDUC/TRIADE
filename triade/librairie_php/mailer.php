<?php                               
/*---------------------------------------------------------------*/
/*
    Titre : Class: Attachment Mailer                                                                                      
                                                                                                                          
    URL   : https://phpsources.net/code_s.php?id=429
    Auteur           : freemh                                                                                             
    Date édition     : 19 Juil 2008                                                                                      
    Date mise a jour : 15 Fev 2026
                                                                                       
    Rapport de la maj:                                                                                                    
    - refactoring du code en PHP 8                                                                                        
*/
/*---------------------------------------------------------------*/

declare(strict_types=1);

/**
 * Modern PHP 8 Email Mailer with MIME Attachments
 * 
 * Features:
 * - PHP 8.3+ typed properties and constructor promotion
 * - Proper exception handling
 * - PSR-12 coding standards
 * - Enum for content types
 * - Return type declarations
 * - Null safety with union types
 */

namespace App\Mail;

use RuntimeException;
use InvalidArgumentException;

enum ContentType: string
{
    case TEXT_HTML = 'text/html';
    case TEXT_PLAIN = 'text/plain';
    case APPLICATION_OCTET_STREAM = 'application/octet-stream';
    case APPLICATION_PDF = 'application/pdf';
    case IMAGE_JPEG = 'image/jpeg';
    case IMAGE_PNG = 'image/png';
}

class Mailer
{
    private readonly string $mimeBoundary;
    private string $emailMessage = '';
    private array $attachments = [];

    /**
     * Constructor with promoted properties (PHP 8.0+)
     */
    public function __construct(
        private string $emailTo,
        private string $emailSubject,
        string $message,
        private string $headers = '',
        private string $charset = 'UTF-8'
    ) {
        $this->validateEmail($emailTo);
        $this->mimeBoundary = '==Multipart_Boundary_' . bin2hex(random_bytes(16)
) . '==';
        $this->initializeHeaders();
        $this->initializeMessage($message);
    }

    /**
     * Validate email address
     */
    private function validateEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email address: {$email}"
);
        }
    }

    /**
     * Initialize MIME headers
     */
    private function initializeHeaders(): void
    {
        $this->headers .= "\nMIME-Version: 1.0\n";
        $this->headers .= "Content-Type: multipart/mixed;\n";
        $this->headers .= " boundary=\"{$this->mimeBoundary}\"";
    }

    /**
     * Initialize base email message
     */
    private function initializeMessage(string $message): void
    {
        $this->emailMessage = "This is a multi-part message in MIME format.\n\n"
;
        $this->emailMessage .= "--{$this->mimeBoundary}\n";
        $this->emailMessage .= 
"Content-Type: text/html; charset=\"{$this->charset}\"\n";
        $this->emailMessage .= "Content-Transfer-Encoding: 7bit\n\n";
        $this->emailMessage .= $message . "\n\n";
    }

    /**
     * Add attachment from content
     */
    public function attach(
        string|ContentType $contentType,
        string $filename,
        string $content
    ): self {
        $type = $contentType instanceof ContentType 
            ? $contentType->value 
            : $contentType;

        $encodedContent = chunk_split(base64_encode($content));

        $this->emailMessage .= "--{$this->mimeBoundary}\n";
        $this->emailMessage .= "Content-Type: {$type};\n";
        $this->emailMessage .= " name=\"{$filename}\"\n";
        $this->emailMessage .= "Content-Transfer-Encoding: base64\n\n";
        $this->emailMessage .= $encodedContent . "\n\n";
        $this->emailMessage .= "--{$this->mimeBoundary}\n";

        $this->attachments[] = $filename;

        return $this;
    }

    /**
     * Attach a file from filesystem
     * 
     * @throws RuntimeException if file cannot be read
     */
    public function attachFile(string $filepath): self
    {
        if (!is_file($filepath) || !is_readable($filepath)) {
            throw new RuntimeException(
"File not found or not readable: {$filepath}");
        }

        $content = file_get_contents($filepath);
        if ($content === false) {
            throw new RuntimeException("Failed to read file: {$filepath}");
        }

        $filename = basename($filepath);
        $mimeType = mime_content_type($filepath) ?: ContentType::
APPLICATION_OCTET_STREAM->value;

        return $this->attach($mimeType, $filename, $content);
    }

    /**
     * Attach all files from a directory (recursive)
     * 
     * @throws RuntimeException if directory cannot be read
     */
    public function attachDirectory(string $directory): self
    {
        if (!is_dir($directory) || !is_readable($directory)) {
            throw new RuntimeException(
"Directory not found or not readable: {$directory}");
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $directory,
                \RecursiveDirectoryIterator::SKIP_DOTS
            )
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $this->attachFile($file->getPathname());
            }
        }

        return $this;
    }

    /**
     * Send email using PHP's mail() function
     * 
     * @throws RuntimeException if mail sending fails
     */
    public function send(): bool
    {
        $result = mail(
            $this->emailTo,
            $this->emailSubject,
            $this->emailMessage,
            $this->headers
        );

        if (!$result) {
            throw new RuntimeException(
"Failed to send email to: {$this->emailTo}");
        }

        return $result;
    }

    /**
     * Send email using IMAP
     * 
     * @throws RuntimeException if IMAP extension is not loaded or sending fails
     */
    public function sendViaImap(): bool
    {
        if (!extension_loaded('imap')) {
            throw new RuntimeException("IMAP extension is not loaded");
        }

        $result = imap_mail(
            $this->emailTo,
            $this->emailSubject,
            $this->emailMessage,
            $this->headers
        );

        if (!$result) {
            throw new RuntimeException(
"Failed to send email via IMAP to: {$this->emailTo}");
        }

        return $result;
    }

    /**
     * Get email recipient
     */
    public function getRecipient(): string
    {
        return $this->emailTo;
    }

    /**
     * Get email subject
     */
    public function getSubject(): string
    {
        return $this->emailSubject;
    }

    /**
     * Get list of attached files
     * 
     * @return array<string>
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * Get attachment count
     */
    public function getAttachmentCount(): int
    {
        return count($this->attachments);
    }
}
?>
<?php
// Exemple d'utilisation
//

declare(strict_types=1);

require_once 'Mailer.php';

use App\Mail\Mailer;
use App\Mail\ContentType;

// Example 1: Simple HTML email
try {
    $mailer = new Mailer(
        emailTo: 'recipient@example.com',
        emailSubject: 'Welcome to Our Service',
        message: '<h1>Welcome!</h1><p>Thank you for joining us.</p>',
        headers: 'From: sender@example.com'
    );
    
    $mailer->send();
    echo "Email sent successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Example 2: Email with file attachment
try {
    $mailer = new Mailer(
        emailTo: 'client@example.com',
        emailSubject: 'Your Invoice',
        message: '<p>Please find your invoice attached.</p>',
        headers: 'From: billing@example.com'
    );
    
    // Attach a file from filesystem
    $mailer->attachFile('/path/to/invoice.pdf');
    
    $mailer->send();
    echo "Email with attachment sent!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Example 3: Email with multiple attachments using method chaining
try {
    $mailer = new Mailer(
        emailTo: 'team@example.com',
        emailSubject: 'Monthly Reports',
        message: '<p>Here are all the reports for this month.</p>',
        headers: 'From: reports@example.com'
    );
    
    // Method chaining for multiple attachments
    $mailer
        ->attachFile('/path/to/report1.pdf')
        ->attachFile('/path/to/report2.pdf')
        ->attachFile('/path/to/chart.png')
        ->send();
    
    echo "Email with {$mailer->getAttachmentCount()} attachments sent!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Example 4: Attach content directly (without file)
try {
    $mailer = new Mailer(
        emailTo: 'admin@example.com',
        emailSubject: 'System Log',
        message: '<p>System log attached.</p>',
        headers: 'From: system@example.com'
    );
    
    // Create content on-the-fly
    $logContent = "Application log:\n" . date('Y-m-d H:i:s') . 
" - System started\n";
    
    $mailer->attach(
        contentType: ContentType::TEXT_PLAIN,
        filename: 'system.log',
        content: $logContent
    );
    
    $mailer->send();
    echo "Email with generated content sent!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Example 5: Attach all files from a directory
try {
    $mailer = new Mailer(
        emailTo: 'backup@example.com',
        emailSubject: 'Backup Files',
        message: '<p>All backup files from the directory.</p>',
        headers: 'From: backup@example.com'
    );
    
    // Attach entire directory (recursive)
    $mailer->attachDirectory('/path/to/backup/folder');
    
    $mailer->send();
    echo "Backup email sent with all files!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>


