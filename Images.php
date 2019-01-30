<?php

Class Images extends GetContent
{

    public $url;
    public $emptyPattern;
    public $messageToErrorCode;
    private $extension;

    public function __construct()
    {
        parent::__construct();

        $this->url = "https://mail.ru";
        $this->extension = array('jpg', 'jpeg', 'gif', 'png');
        $this->emptyPattern = 'Изображений нет';
        $this->messageToErrorCode = 'Результат запроса вернулся с кодом: ';
    }

    public function searchPatternFromContentMailRu()
    {
        $content = $this->getVendorContent($this->url);

        if($content['http_code'] == "200") {

            preg_match_all('/(img|src)=("|\')[^"\'>]+/i', $content['content'], $media);
            unset($content);
            $pattern = $media[0];

            return $pattern;

        } else {

            return $this->messageToErrorCode . $content['http_code'];
        }

    }

    public function SearchesAndReplacesAfterSearchPattern($pattern)
    {
        $dataImages = preg_replace('/(img|src)("|\'|="|=\')(.*)/i', "$3", $pattern);

        return $dataImages;
    }

    public function getImagesLinksFromContentMailRu($dataImages)
    {
        $images = array();
        foreach ($dataImages as $urlImage) {

            $strBase64 = substr(strstr($urlImage, ','), 1, strlen($urlImage));
            if(base64_decode($strBase64,true) != false){
                array_push($images, $urlImage);
            }

            $pathInfoImage = pathinfo($urlImage);
            if (isset($pathInfoImage['extension'])) {
                foreach ($this->extension as $format) {
                    if ($pathInfoImage['extension'] == $format) {
                        array_push($images, $urlImage);
                    }
                }
            }
        }

        return $images;
    }

    public function getImages()
    {
        $pattern = $this->searchPatternFromContentMailRu();
        if($pattern != $this->messageToErrorCode){

            $dataImages = $this->SearchesAndReplacesAfterSearchPattern($pattern);
            $images = $this->getImagesLinksFromContentMailRu($dataImages);

            return $images;

        } else if (empty($pattern) || empty($dataImages) || empty($images)) {

            return $this->emptyPattern;

        } else {
            return $this->messageToErrorCode;
        }




    }

}
