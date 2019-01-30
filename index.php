<?php


function __autoload($classname) {
    $filename = $classname .".php";
    include_once($filename);
}

function validateImagesLink()
{

    $images = new Images();
    $allImages = $images->getImages();

    if($allImages != $images->emptyPattern && $allImages != $images->messageToErrorCode){

        foreach ($allImages as $key => $link) {

            if (stristr($link, 'https://') === false
                && stristr($link, 'http://') === false
                && stristr($link, '//') === false) {

                $domain = $images->url;
                $allImages[$key] = $domain . $link;
            }

        }
        return $allImages;

    }

    return $allImages;
}

$allImages = validateImagesLink();

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Изображения</title>

    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" media="all">
    <script type="text/javascript" src="js/bootstrap.js"></script>

</head>
<body>

<div class="col-12 mx-auto mb-5 text-center">
    <h1>Ссылки на все изображения со страницы <strong>https://mail.ru</strong></h1>
</div>
<div class="col-12 mx-auto text-left">
    <?php if (!empty($allImages) && is_array($allImages)) { ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>id</th>
                    <th>link</th>
                </tr>
                </thead>
                <tbody>
                <? foreach ($allImages as $key => $link) {
                    if (!empty($link)) { ?>
                        <tr>
                            <td><? echo $key ?></td>
                            <td><a href="<? echo $link ?>" target="_blank"><? echo $link ?></a></td>
                        </tr>
                    <? } else { ?> <p>Результатов не найдено.</p><?
                    }
                } ?>
                </tbody>
            </table>
        </div>

    <? } else {  ?>
        <p>Изображений нет</p>
    <? } ?>

</div>


</body>
</html>

