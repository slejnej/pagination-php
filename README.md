Pagination for PHP
=

[![Build Status](https://travis-ci.org/slejnej/pagination-php.svg?branch=master)](https://travis-ci.org/slejnej/pagination-php)

Light weight pagination for PHP with full control over output and displaying of data. Emulates the Mobile view with replacing excess numbers with dot-dot-dot (ellipse).

## Installation
Install with composer:
```
composer require slejnej/pagination-php
```

## Basic usage
Example with default values
```
<?php

use Slejnej\Pagination;

require '../vendor/autoload.php';

$input = ['one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten'];
$pagination = new Pagination();
$pagination->paginate($input);
?>
<html>
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    </head>
    <body>

    <?php
      foreach ($pagination->getItems() as $item): ?>
        <div class="card">
            <div class="card-body">
                <?= $item ?>
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
```

## Available functions
|         function        |                            action                            |
|-------------------------|--------------------------------------------------------------|
| getItems                | returns items for selected page, or empty array              |
| setMaxPagesToShow       | SET number of page links in navigation (min. 3)              |
| getMaxPagesToShow       | returns int number of links in navigation                    |
| getCurrentPage          | returns int number of current page                           |
| setItemsPerPage         | SET number of items per page (default: 10)                   |
| getItemsPerPage         | returns int number of items per page                         |
| getTotalItems           | returns total number of items sent to paginate               |
| getNumPages             | returns int number of pages                                  |
| setUrlPattern           | SET new navigation URL pattern (default: `(:num)`)           |
| getUrlPattern           | returns current URL pattern                                  |
| getPageUrl              | returns URL that is created from URL pattern and page number |
| getNextPage             | returns int number or null for next page                     |
| getPrevPage             | returns int number or null for previous page                 |
| getNextUrl              | returns URL or null for next page                            |
| getPrevUrl              | returns URL or null for previous page                        |
| getNavigation           | returns array structure for entire navigation                |
| getNavigationHtml       | returns navigation HTML                                      |
| getCurrentPageFirstItem | returns sequential number of first item on selected page     |
| getCurrentPageLastItem  | returns sequential number of last item on selected page      |