<?php

namespace spec\Slejnej;

use PhpSpec\ObjectBehavior;
use Slejnej\Pagination;

class PaginationSpec extends ObjectBehavior
{
    protected $target = ['one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten'];

    function let() {
        $this->beConstructedWith(4);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Pagination::class);
    }

    function it_can_throw_exception_on_construct()
    {
        $this->shouldThrow(\LogicException::class)
            ->during('__construct', [0]);
    }

    function it_can_throw_exception_on_paginate()
    {
        $this->shouldThrow(\LogicException::class)
            ->during('paginate', [$this->target, 0]);
    }

    function it_can_set_pagination_data_with_paginate()
    {
        $this->paginate($this->target, 1);
        $this->getTotalItems()->shouldBe(10);
        $this->getItemsPerPage()->shouldBe(4);
        $this->getCurrentPage()->shouldBe(1);

        $this->getNumPages()->shouldBe(3);

        $this->paginate($this->target, 3);
        $this->getCurrentPageFirstItem()->shouldBe(9);
        $this->getCurrentPageLastItem()->shouldBe(10);

        $this->paginate($this->target, 4);
        $this->getCurrentPage()->shouldBe(4);
        $this->getCurrentPageFirstItem()->shouldBe(null);
        $this->getCurrentPageLastItem()->shouldBe(null);
    }

    function it_can_return_navigation_as_array()
    {
        $this->paginate([]);
        $this->getNavigation()->shouldBe([]);

        $this->paginate($this->target);
        $this->getNavigation()->shouldNotBe([]);
    }

    function it_can_change_items_per_page()
    {
        $this->getItemsPerPage()->shouldBe(4);
        $this->setItemsPerPage(6);
        $this->getItemsPerPage()->shouldBe(6);
    }

    function it_can_set_max_pages_to_show()
    {
        $this->getMaxPagesToShow()->shouldBe(5);
        $this->setMaxPagesToShow(8);
        $this->getMaxPagesToShow()->shouldBe(8);
    }

    function it_can_throw_an_exception_if_max_pages_to_low()
    {
        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('setMaxPagesToShow', [2]);
    }

    function it_can_update_nav_url_pattern_and_get_url()
    {
        $defaultPattern = $this->getUrlPattern();
        $this->setUrlPattern('?p=(:num)');
        $this->getUrlPattern()->shouldNotBe($defaultPattern);
        $this->getPageUrl(1)->shouldBe('?p=1');
    }

    function it_can_get_nav_data_for_custom_navigation()
    {
        $this->paginate($this->target, 2);

        $this->getPrevPage()->shouldBe(1);
        $this->getPrevUrl()->shouldBe('1');
        $this->getNextPage()->shouldBe(3);
        $this->getNextUrl()->shouldBe('3');
    }
}
