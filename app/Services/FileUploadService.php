<?php

namespace App\Services;

class FileUploadService
{
    protected $allowedMimeTypes = [
        'application/pdf',
        'application/msword', // .doc
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
    ];

    protected $allowedExtensions = ['pdf', 'doc', 'docx'];
    protected $maxFileSize = 5242880; // 5MB in bytes
    protected $uploadPath = WRITEPATH . '../uploads/';

    /**
     * Handle abstract file upload
     *
     * @param array $file $_FILES array for uploaded file
     * @param int $userId
     * @param int $registrationId
     * @param string $type (abstract|revision|loa)
     * @return array
     */
    public function uploadAbstractFile($file, int $userId, int $registrationId, string $type = 'abstract'): array
    {
        try {
            // Handle CodeIgniter 4 UploadedFile object
            if ($file instanceof \CodeIgniter\HTTP\Files\UploadedFile) {
                // Basic validation
                if (!$file->isValid()) {
                    return [
                        'success' => false,
                        'message' => 'No file uploaded or invalid file: ' . $file->getErrorString()
                    ];
                }

                // Check file size
                if ($file->getSize() > $this->maxFileSize) {
                    return [
                        'success' => false,
                        'message' => 'File size exceeds maximum allowed size of ' . ($this->maxFileSize / 1024 / 1024) . 'MB'
                    ];
                }

                // Check file type
                $fileType = $file->getClientMimeType();
                if (!in_array($fileType, $this->allowedMimeTypes)) {
                    return [
                        'success' => false,
                        'message' => 'Invalid file type. Only PDF, DOC, and DOCX files are allowed'
                    ];
                }

                // Generate unique filename
                $extension = $file->getClientExtension();
                $fileName = $type . '_' . $userId . '_' . $registrationId . '_' . time() . '.' . $extension;
                $filePath = $this->uploadPath . $fileName;

                // Move uploaded file
                if ($file->move($this->uploadPath, $fileName)) {
                    return [
                        'success' => true,
                        'message' => 'File uploaded successfully',
                        'file_path' => $filePath,
                        'file_name' => $fileName,
                        'original_name' => $file->getClientName(),
                        'file_size' => $file->getSize(),
                        'file_type' => $fileType
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Failed to move uploaded file'
                    ];
                }
            }
            
            // Legacy array format handling
            if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
                return [
                    'success' => false,
                    'message' => 'No file uploaded or invalid file'
                ];
            }

            // Check file size
            if ($file['size'] > $this->maxFileSize) {
                return [
                    'success' => false,
                    'message' => 'File size exceeds maximum limit (5MB)',
                    'file_size' => $file['size'],
                    'max_size' => $this->maxFileSize
                ];
            }

            // Check file extension
            $originalName = $file['name'];
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            
            if (!in_array($extension, $this->allowedExtensions)) {
                return [
                    'success' => false,
                    'message' => 'Invalid file type. Allowed: ' . implode(', ', $this->allowedExtensions),
                    'file_extension' => $extension
                ];
            }

            // Check MIME type for additional security
            $mimeType = mime_content_type($file['tmp_name']);
            if (!in_array($mimeType, $this->allowedMimeTypes)) {
                return [
                    'success' => false,
                    'message' => 'Invalid file format detected',
                    'detected_mime' => $mimeType
                ];
            }

            // Generate secure filename
            $timestamp = time();
            $randomString = bin2hex(random_bytes(8));
            $safeFilename = $this->sanitizeFilename($originalName);
            $newFilename = "{$type}_{$userId}_{$registrationId}_{$timestamp}_{$randomString}_{$safeFilename}";

            // Determine upload directory
            $typeDir = '';
            switch ($type) {
                case 'revision':
                    $typeDir = 'revisions/';
                    break;
                case 'loa':
                    $typeDir = 'loa/';
                    break;
                default:
                    $typeDir = 'abstracts/';
            }

            $uploadDir = $this->uploadPath . $typeDir;
            $fullPath = $uploadDir . $newFilename;

            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    return [
                        'success' => false,
                        'message' => 'Failed to create upload directory'
                    ];
                }
            }

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
                return [
                    'success' => false,
                    'message' => 'Failed to move uploaded file'
                ];
            }

            // Set proper file permissions
            chmod($fullPath, 0644);

            // Return success with file info
            return [
                'success' => true,
                'message' => 'File uploaded successfully',
                'file_info' => [
                    'original_name' => $originalName,
                    'filename' => $newFilename,
                    'file_path' => $typeDir . $newFilename,
                    'full_path' => $fullPath,
                    'file_size' => $file['size'],
                    'mime_type' => $mimeType,
                    'extension' => $extension,
                    'upload_type' => $type,
                    'uploaded_at' => date('Y-m-d H:i:s', $timestamp)
                ]
            ];

        } catch (\Exception $e) {
            log_message('error', 'File upload error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'File upload failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate uploaded file without moving it
     *
     * @param array $file
     * @return array
     */
    public function validateFile($file): array
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return [
                'valid' => false,
                'message' => 'No file uploaded or invalid file'
            ];
        }

        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            return [
                'valid' => false,
                'message' => 'File size exceeds maximum limit (5MB)',
                'file_size' => $file['size'],
                'max_size' => $this->maxFileSize
            ];
        }

        // Check extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions)) {
            return [
                'valid' => false,
                'message' => 'Invalid file type. Allowed: ' . implode(', ', $this->allowedExtensions),
                'file_extension' => $extension
            ];
        }

        // Check MIME type
        $mimeType = mime_content_type($file['tmp_name']);
        if (!in_array($mimeType, $this->allowedMimeTypes)) {
            return [
                'valid' => false,
                'message' => 'Invalid file format detected',
                'detected_mime' => $mimeType
            ];
        }

        return [
            'valid' => true,
            'message' => 'File validation successful',
            'file_info' => [
                'name' => $file['name'],
                'size' => $file['size'],
                'type' => $mimeType,
                'extension' => $extension
            ]
        ];
    }

    /**
     * Get file info from database path
     *
     * @param string $filePath
     * @return array
     */
    public function getFileInfo(string $filePath): array
    {
        $fullPath = $this->uploadPath . $filePath;
        
        if (!file_exists($fullPath)) {
            return [
                'exists' => false,
                'message' => 'File not found'
            ];
        }

        return [
            'exists' => true,
            'file_info' => [
                'file_path' => $filePath,
                'full_path' => $fullPath,
                'file_size' => filesize($fullPath),
                'mime_type' => mime_content_type($fullPath),
                'modified_at' => date('Y-m-d H:i:s', filemtime($fullPath)),
                'readable' => is_readable($fullPath)
            ]
        ];
    }

    /**
     * Delete file from storage
     *
     * @param string $filePath
     * @return array
     */
    public function deleteFile(string $filePath): array
    {
        try {
            $fullPath = $this->uploadPath . $filePath;
            
            if (!file_exists($fullPath)) {
                return [
                    'success' => true,
                    'message' => 'File already deleted or does not exist'
                ];
            }

            if (unlink($fullPath)) {
                return [
                    'success' => true,
                    'message' => 'File deleted successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to delete file'
                ];
            }

        } catch (\Exception $e) {
            log_message('error', 'File deletion error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'File deletion failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Download/serve file
     *
     * @param string $filePath
     * @param string $downloadName
     * @return mixed
     */
    public function serveFile(string $filePath, string $downloadName = null)
    {
        $fullPath = $this->uploadPath . $filePath;
        
        if (!file_exists($fullPath)) {
            return false;
        }

        $downloadName = $downloadName ?: basename($filePath);
        $mimeType = mime_content_type($fullPath);
        
        return [
            'file_path' => $fullPath,
            'download_name' => $downloadName,
            'mime_type' => $mimeType,
            'file_size' => filesize($fullPath)
        ];
    }

    /**
     * Sanitize filename for security
     *
     * @param string $filename
     * @return string
     */
    private function sanitizeFilename(string $filename): string
    {
        // Remove directory path
        $filename = basename($filename);
        
        // Remove special characters except dots, dashes, underscores
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Remove multiple dots (security)
        $filename = preg_replace('/\.+/', '.', $filename);
        
        // Limit length
        if (strlen($filename) > 100) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $filename = substr($name, 0, 95) . '.' . $extension;
        }
        
        return $filename;
    }

    /**
     * Get upload statistics
     *
     * @return array
     */
    public function getUploadStats(): array
    {
        $stats = [
            'max_file_size' => $this->maxFileSize,
            'max_file_size_mb' => round($this->maxFileSize / 1024 / 1024, 2),
            'allowed_extensions' => $this->allowedExtensions,
            'allowed_mime_types' => $this->allowedMimeTypes,
            'upload_path' => $this->uploadPath
        ];

        // Count files if directory exists
        if (is_dir($this->uploadPath)) {
            $abstractsPath = $this->uploadPath . 'abstracts/';
            $revisionsPath = $this->uploadPath . 'revisions/';
            
            $stats['file_counts'] = [
                'abstracts' => is_dir($abstractsPath) ? count(glob($abstractsPath . '*')) : 0,
                'revisions' => is_dir($revisionsPath) ? count(glob($revisionsPath . '*')) : 0,
            ];
        }

        return $stats;
    }
}