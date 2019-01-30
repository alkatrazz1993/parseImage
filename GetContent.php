<?php

Class GetContent
{
    private $encoding;
    private $resultOutput;

    public function __construct()
    {
        $this->encoding = "utf-8";
        $this->resultOutput = array();
    }

    public function headerSizeCurlInit($ch)
    {
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        return $header_size;
    }

    public function checkEncodingUTF8($output, $header_size)
    {
        $tmpHeaders = substr($output, 0, $header_size);
        $postResult = substr($output, $header_size);

        $headers = array();
        foreach (explode("\n", $tmpHeaders) as $header) {
            $tmp = explode(":", trim($header), 2);
            if (count($tmp) > 1) {
                $headers[strtolower($tmp[0])] = trim(strtolower($tmp[1]));
            }
        }

        if (isset($headers['content-type'])) {
            $tmp = explode("=", $headers['content-type']);
            if (count($tmp) > 1) {
                $this->encoding = $tmp[1];
            }
        }
        if ($this->encoding != "utf-8") {
            $postResult = iconv($this->encoding, "UTF-8", $postResult);
        }
        return $postResult;
    }

    public function getVendorContent($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $output = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->resultOutput['http_code'] = $http_code;

        if ($http_code == 200) {

            $header_size = $this->headerSizeCurlInit($ch);
            $postResult = $this->checkEncodingUTF8($output, $header_size);
            $this->resultOutput['content'] = $postResult;

            return $this->resultOutput;

        } else {

            return $this->resultOutput;
        }

    }
}
