<?php

namespace Slejnej;


class Pagination
{

    const NUM_PLACEHOLDER = '(:num)';

    protected $items;
    protected $itemsToShow;
    protected $totalItems;
    protected $numPages;
    protected $itemsPerPage;
    protected $currentPage;
    protected $urlPattern;
    protected $maxPagesToShow;
    protected $previousText;
    protected $nextText;


    /**
     * Pagination constructor.
     * @param int $itemsPerPage
     * @param int $maxPagesToShow
     */
    public function __construct($itemsPerPage = 10, $maxPagesToShow = 5)
    {
        if ($itemsPerPage <= 0) {
            throw new \LogicException(
                sprintf("Invalid items per page number. Limit: %d, must be greater than zero", $itemsPerPage)
            );
        }

        $this->itemsPerPage = $itemsPerPage;
        $this->maxPagesToShow = $maxPagesToShow;

        $this->totalItems = 0;
        $this->urlPattern = $this::NUM_PLACEHOLDER;
        $this->previousText = 'Previous';
        $this->nextText = 'Next';
    }

    public function paginate($target, $page = 1)
    {
        if ($page <= 0) {
            throw new \LogicException(
                sprintf("Invalid page number. Page: %d, must be greater than zero", $page)
            );
        }

        $this->currentPage = $page;
        $this->items = $target;

        $this->setItemsToShow();
    }

    private function setItemsToShow() {
        $offset = ($this->currentPage - 1) * $this->itemsPerPage;

        if (is_array($this->items)) {
            $this->totalItems = count($this->items);
            $this->itemsToShow = array_slice(
                $this->items,
                $offset,
                $this->itemsPerPage
            );
        } elseif ($this->items instanceof \ArrayObject) {
            $this->totalItems = $this->items->count();
            $this->itemsToShow = new \ArrayObject(array_slice(
                $this->items->getArrayCopy(),
                $offset,
                $this->itemsPerPage
            ));
        }

        $this->updateNumPages();
    }

    private function updateNumPages()
    {
        $this->numPages = ($this->itemsPerPage == 0 ? 0 : (int) ceil($this->totalItems / $this->itemsPerPage));
    }

    public function getItems()
    {
        return ($this->totalItems > 0) ? $this->itemsToShow : [];
    }

    /**
     * @param int $maxPagesToShow
     * @throws \InvalidArgumentException $maxPagesToShow needs to be more than 2 for ellipsis purposes
     */
    public function setMaxPagesToShow($maxPagesToShow)
    {
        if ($maxPagesToShow < 3) {
            throw new \InvalidArgumentException('maxPagesToShow must be more than 2.');
        }
        $this->maxPagesToShow = $maxPagesToShow;
    }

    /**
     * @return int
     */
    public function getMaxPagesToShow()
    {
        return $this->maxPagesToShow;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param int $itemsPerPage
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
        $this->setItemsToShow();
    }

    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * @return int
     */
    public function getTotalItems()
    {
        return $this->totalItems;
    }

    /**
     * @return int
     */
    public function getNumPages()
    {
        return $this->numPages;
    }

    /**
     * @param string $urlPattern
     */
    public function setUrlPattern($urlPattern)
    {
        $this->urlPattern = $urlPattern;
    }

    /**
     * @return string
     */
    public function getUrlPattern()
    {
        return $this->urlPattern;
    }

    /**
     * @param int $pageNum
     * @return string
     */
    public function getPageUrl($pageNum)
    {
        return str_replace(self::NUM_PLACEHOLDER, $pageNum, $this->urlPattern);
    }

    public function getNextPage()
    {
        if ($this->currentPage < $this->numPages) {
            return $this->currentPage + 1;
        }

        return null;
    }

    public function getPrevPage()
    {
        if ($this->currentPage > 1) {
            return $this->currentPage - 1;
        }

        return null;
    }

    public function getNextUrl()
    {
        if (!$this->getNextPage()) {
            return null;
        }

        return $this->getPageUrl($this->getNextPage());
    }

    /**
     * @return string|null
     */
    public function getPrevUrl()
    {
        if (!$this->getPrevPage()) {
            return null;
        }

        return $this->getPageUrl($this->getPrevPage());
    }

    /**
     * Get navigation data for custom display in format:
     * [
     *  ['num' => 1, 'url' => '/page/1',  'isCurrent' => false],
     *  ['num' => '...', 'url' => null,  'isCurrent' => false],
     *  ['num' => 5, 'url' => '/page/5',  'isCurrent' => true],
     *  ['num' => 6, 'url' => '/page/6',  'isCurrent' => false],
     *  ['num' => '...', 'url' => null,  'isCurrent' => false],
     *  ['num' => 10, 'url' => '/page/10',  'isCurrent' => false],
     * ]
     *
     * @return array
     */
    public function getNavigation()
    {
        $nav = [];

        if ($this->numPages <= 1) {
            return [];
        }

        if ($this->numPages <= $this->maxPagesToShow) {
            for ($i = 1; $i <= $this->numPages; $i++) {
                $nav[] = $this->addNavItem($i, $i == $this->currentPage);
            }
        } else {

            // Determine the sliding range, centered around the current page.
            $itemsAfter = (int) floor(($this->maxPagesToShow - 3) / 2);

            if ($this->currentPage + $itemsAfter > $this->numPages) {
                $startWith = $this->numPages - $this->maxPagesToShow + 2;
            } else {
                $startWith = $this->currentPage - $itemsAfter;
            }
            if ($startWith < 2) $startWith = 2;

            $endWith = $startWith + $this->maxPagesToShow - 3;
            if ($endWith >= $this->numPages) $endWith = $this->numPages - 1;

           // add items to navigation and insert ellipsis if needed
            $nav[] = $this->addNavItem(1, $this->currentPage == 1);
            if ($startWith > 2) {
                $nav[] = $this->addNavItem('...', false, false);
            }
            for ($i = $startWith; $i <= $endWith; $i++) {
                $nav[] = $this->addNavItem($i, $i == $this->currentPage);
            }
            if ($endWith < $this->numPages - 1) {
                $nav[] = $this->addNavItem('...', false, false);
            }
            $nav[] = $this->addNavItem($this->numPages, $this->currentPage == $this->numPages);
        }

        return $nav;
    }

    /**
     * @param $pageNum
     * @param bool $isCurrent
     * @param bool $getPageUrl
     * @return array
     */
    protected function addNavItem($pageNum, $isCurrent = false, $getPageUrl = true)
    {
        return [
            'num' => $pageNum,
            'url' => $getPageUrl ? $this->getPageUrl($pageNum) : null,
            'isCurrent' => $isCurrent
        ];
    }

    public function getNavigationHtml()
    {
        if ($this->numPages <= 1) {
            return '';
        }

        $html = '<ul class="pagination">';
        if ($this->getPrevUrl()) {
            $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($this->getPrevUrl()) . '">&laquo; '. $this->previousText .'</a></li>';
        }

        foreach ($this->getNavigation() as $page) {
            if ($page['url']) {
                $html .= '<li  class="page-item' . ($page['isCurrent'] ? ' active' : '') . '">';
                $html .= '<a class="page-link" href="' . htmlspecialchars($page['url']) . '">' . htmlspecialchars($page['num']) . '</a></li>';
            } else {
                $html .= '<li  class="page-item disabled"><span>' . htmlspecialchars($page['num']) . '</span></li>';
            }
        }

        if ($this->getNextUrl()) {
            $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($this->getNextUrl()) . '">'. $this->nextText .' &raquo;</a></li>';
        }
        $html .= '</ul>';

        return $html;
    }

    public function getCurrentPageFirstItem()
    {
        $first = ($this->currentPage - 1) * $this->itemsPerPage + 1;

        if ($first > $this->totalItems) {
            return null;
        }

        return $first;
    }

    public function getCurrentPageLastItem()
    {
        $first = $this->getCurrentPageFirstItem();
        if ($first === null) {
            return null;
        }

        $last = $first + $this->itemsPerPage - 1;
        if ($last > $this->totalItems) {
            return $this->totalItems;
        }

        return $last;
    }
}