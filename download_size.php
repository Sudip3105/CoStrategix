<?php

error_reporting(0);

class DownloadSize {

    /**
     * Download size
     *
     * @var int
     */
    public $downloadSize = 0;

    /**
     * HTTP requests
     *
     * @var int
     */
    public $httpRequests = 0;

    /**
     * URL
     *
     * @var string
     */
    public $url;

    public function __construct($url) {

        $this->url = $url;
    }

    /*
     * Get the size of the url
     */

    function getSize() {
        if ($this->checkHtml($this->url) == FALSE) {
            $this->totalSize = $this->getFileSize($this->url);
            $this->totalNumResources += 1;
            echo "Downloaded Size: $this->totalSize Bytes ,";
            echo "HTTP requests: $this->totalNumResources";
            return;
        }
        $this->getAllCss();
        $this->getAllScript();
        $this->getAllIframe();
        echo "Downloaded Size: $this->totalSize Bytes ,";
        echo "HTTP requests: $this->totalNumResources";
        return;
    }

    /*
     *  Get CSS
     */

    function getAllCss() {
        $html = file_get_contents($this->url);
        $dom = new domDocument;
        $dom->loadHTML($html);
        $dom->preserveWhiteSpace = false;
        $styles = $dom->getElementsByTagName('link');
        foreach ($styles as $style) {
            $size = $this->getFileSize($style->getAttribute('href'));
            $this->totalSize = $this->totalSize + $size;
            $this->totalNumResources += 1;
        }
    }

    /*
     *  Get js
     */

    function getAllScript() {
        $html = file_get_contents($this->url);
        $dom = new domDocument;
        $dom->loadHTML($html);
        $dom->preserveWhiteSpace = false;
        $scripts = $dom->getElementsByTagName('script');
        foreach ($scripts as $script) {
            $size = $this->getFileSize($script->getAttribute('src'));
            $this->totalSize = $this->totalSize + $size;
            $this->totalNumResources += 1;
        }
    }

    /*
     *  Get Iframes
     */

    function getAllIframe() {
        $html = file_get_contents($this->url);
        $dom = new domDocument;
        $dom->loadHTML($html);
        $dom->preserveWhiteSpace = false;
        $iframes = $dom->getElementsByTagName('iframe');
        foreach ($iframes as $iframe) {
            $size = $this->getFileSize($iframe->getAttribute('src'));
            $this->totalSize = $this->totalSize + $size;
            $this->totalNumResources += 1;
        }
    }

    /*
     * Check if the URL is HTML
     */

    function checkHtml() {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE);
        $data = curl_exec($ch);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);
        if (strpos($contentType, 'text/html') !== false)
            return TRUE;
        else
            return FALSE;
    }

    /*
     * return the size of a given file/url
     * @param string $url
     */

    function getFileSize($url) {
        $headers = get_headers($url, 1);
        $c = curl_init();
        curl_setopt_array($c, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
        ));
        curl_exec($c);
        $size = curl_getinfo($c, CURLINFO_SIZE_DOWNLOAD);
        return $size;
        curl_close($c);
    }

}

$urlArg = $argv[1];
$obj = new DownloadSize($urlArg);
$obj->getSize();
?>

