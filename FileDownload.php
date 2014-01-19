<?php
/**
 * Provides the possibility to easily create file downloads in PHP
 *
 * @author Jannik Zschiesche <hello@apfelbox.net>
 * @version 1.0
 * @license MIT
 */


/**
 * Provides a simple way to create file downloads in PHP
 */
class FileDownload
{
    /**
     * The pointer to the file to download
     *
     * @var resource
     */
    private $filePointer;

    /**
     * Mime types
     *
     * @var array
     */
    private $mimeTypes = array(
        '7z' => 'application/octet-stream',
        'ai' => 'application/illustrator',
        'avi' => 'video/x-msvideo',
        'bmp' => 'image/bmp',
        'cab' => 'application/vnd.ms-cab-compressed',
        'css' => 'text/css',
        'diff' => 'text/x-patch',
        'doc' => 'application/msword',
        'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'dot' => 'application/msword',
        'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
        'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'eps' => 'application/postscript',
        'exe' => 'application/x-msdownload',
        'flv' => 'video/x-flv',
        'gif' => 'image/gif',
        'htm' => 'text/html',
        'html' => 'text/html',
        'ico' => 'image/vnd.microsoft.icon',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'mov' => 'video/quicktime',
        'mp3' => 'audio/mpeg',
        'mpe' => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'msi' => 'application/x-msdownload',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'pdf' => 'application/pdf',
        'php' => 'text/html',
        'png' => 'image/png',
        'pot' => 'application/vnd.ms-powerpoint',
        'potm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
        'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
        'ppa' => 'application/vnd.ms-powerpoint',
        'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
        'pps' => 'application/vnd.ms-powerpoint',
        'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
        'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'ps' => 'application/postscript',
        'psd' => 'image/vnd.adobe.photoshop',
        'qt' => 'video/quicktime',
        'rar' => 'application/x-rar-compressed',
        'rtf' => 'application/rtf',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        'swf' => 'application/x-shockwave-flash',
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
        'txt' => 'text/plain',
        'wav' => 'audio/x-wav',
        'xla' => 'application/vnd.ms-excel',
        'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
        'xls' => 'application/vnd.ms-excel',
        'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
        'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xlt' => 'application/vnd.ms-excel',
        'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',
        'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
        'xml' => 'application/xml',
        'zip' => 'application/zip',
    );



    /**
     * Constructs a new file download
     *
     * @param resource $filePointer
     *
     * @throws InvalidArgumentException
     */
    public function __construct ($filePointer)
    {
        if (!is_resource($filePointer))
        {
            throw new InvalidArgumentException("You must pass a file pointer to the ctor");
        }

        $this->filePointer = $filePointer;
    }



    /**
     * Sends the download to the browser
     *
     * @param string $filename
     * @param bool $forceDownload
     *
     * @throws \RuntimeException is thrown, if the headers are already sent
     */
    public function sendDownload ($filename, $forceDownload = true)
    {
        if (headers_sent())
        {
            throw new \RuntimeException("Cannot send file to the browser, since the headers were already sent.");
        }

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-Type: {$this->getMimeType($filename)}");

        if ($forceDownload)
        {
            header("Content-Disposition: attachment; filename=\"{$filename}\";" );
        }
        else
        {
            header("Content-Disposition: filename=\"{$filename}\";" );
        }

        header("Content-Transfer-Encoding: binary");
        header("Content-Length: {$this->getFileSize()}");

        @ob_clean();

        rewind($this->filePointer);
        fpassthru($this->filePointer);
    }



    /**
     * Returns the mime type of a file name
     *
     * @param string $fileName
     *
     * @return string
     */
    private function getMimeType ($fileName)
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

        if (isset($this->mimeTypes[$extension]))
        {
            return $this->mimeTypes[$extension];
        }
        else
        {
            return "application/force-download";
        }
    }



    /**
     * Returns the file size of the file
     *
     * @return int
     */
    private function getFileSize ()
    {
        $stat = fstat($this->filePointer);
        return $stat['size'];
    }



    /**
     * Creates a new file download from a file path
     *
     * @static
     *
     * @param string $filePath
     *
     * @throws \InvalidArgumentException is thrown, if the given file does not exist or is not readable
     *
     * @return FileDownload
     */
    public static function createFromFilePath ($filePath)
    {
        if (!is_file($filePath))
        {
            throw new \InvalidArgumentException("File does not exist");
        }
        else if (!is_readable($filePath))
        {
            throw new \InvalidArgumentException("File to download is not readable.");
        }

        return new FileDownload(fopen($filePath, "rb"));
    }



    /**
     * Creates a new file download helper with a given content
     *
     * @static
     *
     * @param string $content the file content
     *
     * @return FileDownload
     */
    public static function createFromString ($content)
    {
        $file = tmpfile();
        fwrite($file, $content);

        return new FileDownload($file);
    }
}