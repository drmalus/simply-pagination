<?php

namespace Tests;

use SimplyPagination\PaginationCalculator;

class PaginationCalculatorTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @test
	 * @dataProvider totalNumberOfPagesVariousConfigProvider
	 */
	public function getTotalNumberOfPages_variousConfigGiven_returnsProperTotalNumberOfPages($config, $expectedValue)
	{
		$calculator 		= new PaginationCalculator($config);
		$totalNumberOfPages = $calculator->getTotalNumberOfPages();

		$this->assertInternalType('int', $totalNumberOfPages);
		$this->assertEquals($expectedValue, $totalNumberOfPages);
	}

	public function totalNumberOfPagesVariousConfigProvider()
	{
		return [
			'total_rows = 100, item_per_page = 10' => [[
				'total_rows' => 100,
				'item_per_page' => 10
			], 10],
			'total_rows = 100, item_per_page = 5' => [[
				'total_rows' => 100,
				'item_per_page' => 5
			], 20],
			'total_rows = 1760, item_per_page = 30' => [[
				'total_rows' => 1760,
				'item_per_page' => 30
			], 59],
			'total_rows = 659, item_per_page = 50' => [[
				'total_rows' => 659,
				'item_per_page' => 50
			], 14],
			'total_rows = 1, item_per_page = 5' => [[
				'total_rows' => 1,
				'item_per_page' => 5
			], 1],
			'total_rows = 0, item_per_page = 20' => [[
				'total_rows' => 0,
				'item_per_page' => 20
			], 0],
		];
	}

	/**
	 * @test
	 * @dataProvider currentPageNumberVariousConfigProvider
	 */
	public function getCurrentPageNumber_variousConfigGiven_returnsProperCurrentPageNumber($config, $expectedValue)
	{
		$calculator 		= new PaginationCalculator($config);
		$currentPageNumber 	= $calculator->getCurrentPageNumber();

		$this->assertInternalType('int', $currentPageNumber);
		$this->assertEquals($expectedValue, $currentPageNumber);
	}

	public function currentPageNumberVariousConfigProvider()
	{
		return [
			'total_rows = 100, item_per_page = 10, page_number = not given' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
			], 1], // no page number given, the default is the first page
			'total_rows = 100, item_per_page = 20, page_number = 7' => [[
				'total_rows' => 100,
				'item_per_page' => 20,
				'page_number' => 7
			], 5], // because the last page is 5 and 7 is bigger
			'total_rows = 100, item_per_page = 20, page_number = 4' => [[
				'total_rows' => 100,
				'item_per_page' => 20,
				'page_number' => 4
			], 4], // returns the give page number
			'total_rows = 100, item_per_page = 10, page_number = 7' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'page_number' => 7
			], 7], // returns the give page number
		];
	}

	/**
	 * @test
	 * @dataProvider isLastPageVariousConfigProvider
	 */
	public function isLastPage_variousConfigGiven_returnsProperBooleanValue($config, $expectedValue)
	{
		$calculator = new PaginationCalculator($config);
		$isLastPage = $calculator->isLastPage();

		$this->assertInternalType('bool', $isLastPage);
		$this->assertSame($expectedValue, $isLastPage);
	}

	public function isLastPageVariousConfigProvider()
	{
		return [
			'no page number given, is not last page' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
			], false],
			'total_rows = 100, item_per_page = 20, page_number = 4' => [[
				'total_rows' => 100,
				'item_per_page' => 20,
				'page_number' => 4
			], false],
			'total_rows = 100, item_per_page = 20, page_number = 5' => [[
				'total_rows' => 100,
				'item_per_page' => 20,
				'page_number' => 5
			], true],
			'total_rows = 100, item_per_page = 20, page_number = 7' => [[
				'total_rows' => 100,
				'item_per_page' => 20,
				'page_number' => 7
			], true],
			'total_rows = 0' => [[
				'total_rows' => 0,
			], true],
		];
	}

	/**
	 * @test
	 * @dataProvider isFirstPageVariousConfigProvider
	 */
	public function isFirstPage_variousConfigGiven_returnsProperBooleanValue($config, $expectedValue)
	{
		$calculator = new PaginationCalculator($config);
		$isFirstPage = $calculator->isFirstPage();

		$this->assertInternalType('bool', $isFirstPage);
		$this->assertSame($expectedValue, $isFirstPage);
	}

	public function isFirstPageVariousConfigProvider()
	{
		return [
			'no page number given, is first page' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
			], true],
			'total_rows = 100, item_per_page = 10, page_number = 1' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'page_number' => 1
			], true],
			'total_rows = 100, item_per_page = 10, page_number = 2' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'page_number' => 2
			], false],
			'total_rows = 100, item_per_page = 10, page_number = 7' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'page_number' => 2
			], false]
		];
	}

	/**
	 * @test
	 * @dataProvider getSqlLimitStartVariousConfigProvider
	 */
	public function getSqlLimitStart_variousConfigGiven_returnsProperLimitStartIntegerValue($config, $expectedValue)
	{
		$calculator = new PaginationCalculator($config);
		$limitStart = $calculator->getSqlLimitStart();

		$this->assertInternalType('int', $limitStart);
		$this->assertSame($expectedValue, $limitStart);
	}

	public function getSqlLimitStartVariousConfigProvider()
	{
		return [
			'no page number given, need to be 0' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
			], 0],
			'total_rows = 100, item_per_page = 10, page_number = 2' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'page_number' => 2
			], 10],
			'total_rows = 100, item_per_page = 20, page_number = 2' => [[
				'total_rows' => 100,
				'item_per_page' => 20,
				'page_number' => 2
			], 20],
			'total_rows = 100, item_per_page = 20, page_number = 5' => [[
				'total_rows' => 100,
				'item_per_page' => 20,
				'page_number' => 5
			], 80],
			'total_rows = 100, item_per_page = 15, page_number = 3' => [[
				'total_rows' => 100,
				'item_per_page' => 15,
				'page_number' => 3
			], 30],
			'total_rows = 100, item_per_page = 20, page_number = 7' => [[
				'total_rows' => 100,
				'item_per_page' => 20,
				'page_number' => 7
			], 80]
		];
	}

	/**
	 * @test
	 * @dataProvider getSqlLimitQuantityVariousConfigProvider
	 */
	public function getSqlLimitQuantity_variousConfigGiven_returnsProperLimitAmountIntegerValue($config, $expectedValue)
	{
		$calculator 	= new PaginationCalculator($config);
		$limitQuantity 	= $calculator->getSqlLimitQuantity();

		$this->assertInternalType('int', $limitQuantity);
		$this->assertSame($expectedValue, $limitQuantity);
	}

	public function getSqlLimitQuantityVariousConfigProvider()
	{
		return [
			'no page number given, need to be 0' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
			], 10],
			'total_rows = 100, item_per_page = 10, page_number = 2' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'page_number' => 2
			], 10],
			'total_rows = 100, item_per_page = 20, page_number = 2' => [[
				'total_rows' => 100,
				'item_per_page' => 20,
				'page_number' => 2
			], 20],
			'total_rows = 100, item_per_page = 47, page_number = 5' => [[
				'total_rows' => 100,
				'item_per_page' => 47,
				'page_number' => 5
			], 47],
			'total_rows = 100, item_per_page = 60, page_number = 3' => [[
				'total_rows' => 100,
				'item_per_page' => 60,
				'page_number' => 3
			], 60],
			'total_rows = 100, item_per_page = 20, page_number = 7' => [[
				'total_rows' => 100,
				'item_per_page' => 20,
				'page_number' => 7
			], 20]
		];
	}

	/**
	 * @test
	 * @dataProvider getItemsVisibleFromPageNumberVariousConfigProvider
	 */
	public function getItemsVisibleFromPageNumber_variousConfigGiven_returnsProperVisibleFromIntegerValue($config, $expectedValue)
	{
		$calculator 	= new PaginationCalculator($config);
		$visibleFrom 	= $calculator->getItemsVisibleFromNumber();

		$this->assertInternalType('int', $visibleFrom);
		$this->assertSame($expectedValue, $visibleFrom);
	}

	public function getItemsVisibleFromPageNumberVariousConfigProvider()
	{
		return [
			'no page number given, need to be 0' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
			], 1],
			'total_rows = 100, item_per_page = 10, page_number = 2' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'page_number' => 2
			], 11],
			'total_rows = 100, item_per_page = 10, page_number = 4' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'page_number' => 4
			], 31],
			'total_rows = 100, item_per_page = 100, page_number = 5' => [[
				'total_rows' => 100,
				'item_per_page' => 100,
				'page_number' => 5
			], 1],
			'total_rows = 100, item_per_page = 10, page_number = 8' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'page_number' => 8
			], 71],
		];
	}

	/**
	 * @test
	 * @dataProvider getItemsVisibleToVariousConfigProvider
	 */
	public function getItemsVisibleTo_variousConfigGiven_returnsProperItemsVisibleToIntegerValue($config, $expectedValue)
	{
		$calculator = new PaginationCalculator($config);
		$visibleTo 	= $calculator->getItemsVisibleTo();

		$this->assertInternalType('int', $visibleTo);
		$this->assertSame($expectedValue, $visibleTo);
	}

	public function getItemsVisibleToVariousConfigProvider()
	{
		return [
			'no page number given, need to be 0' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
			], 10],
			'total_rows = 100, item_per_page = 10, page_number = 1' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'page_number' => 2
			], 20],
			'total_rows = 100, item_per_page = 10, page_number = 4' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'page_number' => 4
			], 40],
			'total_rows = 100, item_per_page = 100, page_number = 5' => [[
				'total_rows' => 100,
				'item_per_page' => 100,
				'page_number' => 5
			], 100],
			'total_rows = 100, item_per_page = 10, page_number = 8' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'page_number' => 8
			], 80],
		];
	}

	/**
	 * @test
	 * @dataProvider getTotalRowsVariousConfigProvider
	 */
	public function getTotalRows_variousConfigGiven_returnProperTotalRowsIntegerValue($config, $expectedValue)
	{
		$calculator = new PaginationCalculator($config);
		$totalRows 	= $calculator->getTotalRows();

		$this->assertInternalType('int', $totalRows);
		$this->assertSame($expectedValue, $totalRows);
	}

	public function getTotalRowsVariousConfigProvider()
	{
		return [
			'total_rows = 100, item_per_page = 10, page_number = 1' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'page_number' => 1
			], 100],
			'total_rows = 500, item_per_page = 10, page_number = 1' => [[
				'total_rows' => 500,
				'item_per_page' => 10,
				'page_number' => 1
			], 500],
			'total_rows = 0, item_per_page = 10, page_number = 1' => [[
				'total_rows' => 0,
				'item_per_page' => 10,
				'page_number' => 1
			], 0],
			'total_rows = 1748, item_per_page = 10, page_number = 1' => [[
				'total_rows' => 1748,
				'item_per_page' => 10,
				'page_number' => 1
			], 1748],
		];
	}

	/**
	 * @test
	 * @dataProvider getPreviousPageNumberVariousConfigProvider
	 */
	public function getPreviousPageNumber_variousConfigGiven_returnProperPrevPageIntegerValue($config, $expectedValue)
	{
		$calculator 	= new PaginationCalculator($config);
		$prevPageNumber = $calculator->getPreviousPageNumber();

		$this->assertInternalType('int', $prevPageNumber);
		$this->assertSame($expectedValue, $prevPageNumber);
	}

	public function getPreviousPageNumberVariousConfigProvider()
	{
		return [
			'total_rows = 100, item_per_page = 10' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
			], 1],
			'total_rows = 100, item_per_page = 10, page_number = 1' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'page_number' => 1
			], 1],
			'total_rows = 100, item_per_page = 10, page_number = 3' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'page_number' => 3
			], 2],
			'total_rows = 100, item_per_page = 10, page_number = 6' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'page_number' => 6
			], 5],
			'total_rows = 100, item_per_page = 10, page_number = 15' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'page_number' => 15
			], 9],
		];
	}

	/**
	 * @test
	 * @dataProvider getNextPageNumberVariousConfigProvider
	 */
	public function getNextPageNumber_variousConfigGiven_returnProperNextPageIntegerValue($config, $expectedValue)
	{
		$calculator 	= new PaginationCalculator($config);
		$nextPageNumber = $calculator->getNextPageNumber();

		$this->assertInternalType('int', $nextPageNumber);
		$this->assertSame($expectedValue, $nextPageNumber);
	}

	public function getNextPageNumberVariousConfigProvider()
	{
		return [
			'total_rows = 100, item_per_page = 10' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
			], 2],
			'total_rows = 100, item_per_page = 10, page_number = 1' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'page_number' => 1
			], 2],
			'total_rows = 100, item_per_page = 10, page_number = 3' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'page_number' => 3
			], 4],
			'total_rows = 100, item_per_page = 10, page_number = 6' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'page_number' => 6
			], 7],
			'total_rows = 100, item_per_page = 10, page_number = 15' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'page_number' => 15
			], 10],
		];
	}

	/**
	 * @test
	 * @dataProvider getPaginationFirstPageNumberVariousConfigProvider
	 */
	public function getPaginationFirstPageNumber_variousConfigGiven_returnProperFirstPaginationPageIntegerValue($config, $expectedValue)
	{
		$calculator 		= new PaginationCalculator($config);
		$firstPageNumber 	= $calculator->getPaginationFirstPageNumber();

		$this->assertInternalType('int', $firstPageNumber);
		$this->assertSame($expectedValue, $firstPageNumber);
	}

	public function getPaginationFirstPageNumberVariousConfigProvider()
	{
		return [
			'total_rows = 100, item_per_page = 10, pagination_width = 10' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'pagination_width' => 10
			], 1],
			'total_rows = 100, item_per_page = 10, pagination_width = 10, page_number = 1' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'pagination_width' => 10,
				'page_number' => 1
			], 1],
			'total_rows = 100, item_per_page = 10, pagination_width = 10, page_number = 3' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'pagination_width' => 10,
				'page_number' => 3
			], 1],
			'total_rows = 100, item_per_page = 10, pagination_width = 5, page_number = 6' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'pagination_width' => 5,
				'page_number' => 6
			], 4],
			'total_rows = 200, item_per_page = 10, pagination_width = 10, page_number = 7' => [[
				'total_rows' => 200,
				'item_per_page' => 10,
				'pagination_width' => 10,
				'page_number' => 7
			], 2],
			'total_rows = 200, item_per_page = 10, pagination_width = 10, page_number = 15' => [[
				'total_rows' => 200,
				'item_per_page' => 10,
				'pagination_width' => 10,
				'page_number' => 15
			], 10],
		];
	}

	/**
	 * @test
	 * @dataProvider getPaginationLastPageNumberVariousConfigProvider
	 */
	public function getPaginationLastPageNumber_variousConfigGiven_returnProperLastPaginationPageIntegerValue($config, $expectedValue)
	{
		$calculator 	= new PaginationCalculator($config);
		$lastPageNumber = $calculator->getPaginationLastPageNumber();

		$this->assertInternalType('int', $lastPageNumber);
		$this->assertSame($expectedValue, $lastPageNumber);
	}

	public function getPaginationLastPageNumberVariousConfigProvider()
	{
		return [
			'total_rows = 100, item_per_page = 10, pagination_width = 10' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'pagination_width' => 10
			], 10],
			'total_rows = 100, item_per_page = 10, pagination_width = 10, page_number = 1' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'pagination_width' => 10,
				'page_number' => 1
			], 10],
			'total_rows = 200, item_per_page = 10, pagination_width = 20' => [[
				'total_rows' => 200,
				'item_per_page' => 10,
				'pagination_width' => 20
			], 20],
			'total_rows = 200, item_per_page = 10, pagination_width = 10, page_number = 1' => [[
				'total_rows' => 200,
				'item_per_page' => 10,
				'pagination_width' => 10,
				'page_number' => 1
			], 10],
			'total_rows = 200, item_per_page = 10, pagination_width = 10, page_number = 11' => [[
				'total_rows' => 200,
				'item_per_page' => 10,
				'pagination_width' => 10,
				'page_number' => 11
			], 15],
			'total_rows = 200, item_per_page = 10, pagination_width = 10, page_number = 17' => [[
				'total_rows' => 200,
				'item_per_page' => 10,
				'pagination_width' => 10,
				'page_number' => 17
			], 20],
			'total_rows = 200, item_per_page = 10, pagination_width = 10, page_number = 20' => [[
				'total_rows' => 200,
				'item_per_page' => 10,
				'pagination_width' => 10,
				'page_number' => 20
			], 20]
		];
	}

	/**
	 * @test
	 * @dataProvider getPaginationPageNumbersVariousConfigProvider
	 */
	public function getPaginationPageNumbers_variousConfigGiven_returnProperPaginationPageNumbers($config, $expectedValue)
	{
		$calculator 	= new PaginationCalculator($config);
		$pageNumbers 	= $calculator->getPaginationPageNumbers();

		$this->assertInternalType('array', $pageNumbers);
		$this->assertSame($expectedValue, $pageNumbers);
	}

	public function getPaginationPageNumbersVariousConfigProvider()
	{
		return [
			'total_rows = 100, item_per_page = 10' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
			], range(1, 10)], // default value width 10
			'total_rows = 100, item_per_page = 10, pagination_width = 5' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'pagination_width' => 5
			], range(1, 5)],
			'total_rows = 100, item_per_page = 10, pagination_width = 10, page_number = 1' => [[
				'total_rows' => 100,
				'item_per_page' => 10,
				'pagination_width' => 10,
				'page_number' => 1
			], range(1, 10)],
			'total_rows = 100, item_per_page = 2, pagination_width = 25, page_number = 1' => [[
				'total_rows' => 100,
				'item_per_page' => 2,
				'pagination_width' => 25,
				'page_number' => 1
			], range(1, 25)],
			'total_rows = 2000, item_per_page = 10, pagination_width = 10, page_number = 25' => [[
				'total_rows' => 2000,
				'item_per_page' => 10,
				'pagination_width' => 10,
				'page_number' => 25
			], range(20, 29)],
			'total_rows = 2000, item_per_page = 10, pagination_width = 10, page_number = 51' => [[
				'total_rows' => 2000,
				'item_per_page' => 10,
				'pagination_width' => 10,
				'page_number' => 51
			], range(46, 55)],
			'total_rows = 2000, item_per_page = 10, pagination_width = 11, page_number = 51' => [[
				'total_rows' => 2000,
				'item_per_page' => 10,
				'pagination_width' => 11,
				'page_number' => 51
			], range(46, 56)]
		];
	}

}
