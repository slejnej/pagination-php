<?php

use Slejnej\Pagination;

require '../vendor/autoload.php';

// urlPattern and way of getting current page can be anything that is then passed to ->paginate function as second parameter
$urlPattern = '?p=(:num)';
$page = isset($_GET['p']) ? $_GET['p'] : 1;

$input = [ "Italy"=>"Rome", "Luxembourg"=>"Luxembourg", "Belgium"=> "Brussels", "Denmark"=>"Copenhagen", "Finland"=>"Helsinki", "France" => "Paris", "Slovakia"=>"Bratislava", "Slovenia"=>"Ljubljana", "Germany" => "Berlin", "Greece" => "Athens", "Ireland"=>"Dublin", "Netherlands"=>"Amsterdam", "Portugal"=>"Lisbon", "Spain"=>"Madrid", "Sweden"=>"Stockholm", "United Kingdom"=>"London", "Cyprus"=>"Nicosia", "Lithuania"=>"Vilnius", "Czech Republic"=>"Prague", "Estonia"=>"Tallin", "Hungary"=>"Budapest", "Latvia"=>"Riga", "Malta"=>"Valetta", "Austria" => "Vienna", "Poland"=>"Warsaw"];
// input as ArrayObject is also possible
$input2 = new \ArrayObject($input);

$pagination = new Pagination(4);
$pagination->paginate($input2, $page);
$pagination->setUrlPattern($urlPattern);
$pagination->setMaxPagesToShow(4);

?>
<html>
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    </head>
    <body>

    <?php
      foreach ($pagination->getItems() as $country => $capital): ?>
        <div class="card">
            <div class="card-body">
                The capital of
                <span><?= $country ?></span> is
                <span><?= $capital ?></span>
            </div>
        </div>
    <?php
      endforeach;
      echo $pagination->getNavigationHtml();
    ?>
        <div class="text-right">
            <?= $pagination->getTotalItems(); ?> entries. Showing
            <?= $pagination->getCurrentPageFirstItem() .' - '. $pagination->getCurrentPageLastItem(); ?>.
        </div>
    </body>
</html>
