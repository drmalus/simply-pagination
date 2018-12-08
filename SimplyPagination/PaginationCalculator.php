<?php

namespace SimplyPagination;


class PaginationCalculator
{

	private $options = [];
	private $pageIndex = 0;
	private $pageNumber = 1;
	private $itemPerPage = 20;
	private $totalRows = 0;
	private $totalNumberOfPages = 0;
	private $paginationWidth = 10;
	private $halfWidthOfPagination = 5;
	private $biggestPaginationStartPageNumber  = 0;
	private $numberOfPagesToLastPage = 0;

	/**
	 * PaginationCalculator constructor.
	 *
	 * @param array $options
	 * @param int page_number Az első oldal = 1
	 */
	public function __construct(array $options)
	{
		$this->options = $options;

		if ($this->optionIsSet('total_rows')) {
			$this->setTotalRows();
		}
		if ($this->optionIsSet('item_per_page')) {
			$this->setItemPerPage();
		}
		if ($this->optionIsSet('pagination_width')) {
			$this->setPaginationWidth();
		}

		$this->calculateHalfWidthOfPagination();
		$this->calculateTotalNumberOfPages();
		$this->calculateBiggerPaginationStartPageNumber();
		$this->calculateNumberOfPagesToLastPage();

		if ($this->optionIsSet('page_number')) {
			$this->setPageNumber();
		}
	}

	private function optionIsSet($key)
	{
		return key_exists($key, $this->options);
	}

	private function setTotalRows()
	{
		$this->totalRows = (int)$this->options['total_rows'];
	}

	private function setItemPerPage()
	{
		$this->itemPerPage = (int)$this->options['item_per_page'];
	}

	private function setPaginationWidth()
	{
		$this->paginationWidth = (int)$this->options['pagination_width'];
	}

	private function calculateHalfWidthOfPagination()
	{
		$this->halfWidthOfPagination = floor($this->paginationWidth / 2);
	}

	private function calculateTotalNumberOfPages()
	{
		$this->totalNumberOfPages = (int) round(ceil($this->totalRows / $this->itemPerPage), 0);
	}

	private function calculateBiggerPaginationStartPageNumber()
	{
		$this->biggestPaginationStartPageNumber = $this->totalNumberOfPages - $this->paginationWidth + 1;
	}

	private function calculateNumberOfPagesToLastPage()
	{
		$this->numberOfPagesToLastPage = $this->paginationWidth - 1;
	}

	private function setPageNumber()
	{
		$pageNumber = (int)$this->options['page_number'];
		$this->pageNumber = $pageNumber > $this->totalNumberOfPages ? $this->totalNumberOfPages : $pageNumber;
		$this->pageIndex = $this->pageNumber === 0 ? 0 : $this->pageNumber - 1;
	}

	public function getTotalNumberOfPages()
	{
		return $this->totalNumberOfPages;
	}

	public function getCurrentPageNumber()
	{
		if ($this->pageNumber > $this->totalNumberOfPages) {
			return $this->totalNumberOfPages;
		}
		return $this->pageNumber;
	}

	public function isLastPage()
	{
		return $this->getCurrentPageNumber() === $this->totalNumberOfPages;
	}

	public function isFirstPage()
	{
		return $this->getCurrentPageNumber() === 1;
	}

	private function calculateSqlLimitStart()
	{
		return $this->pageIndex * $this->itemPerPage;
	}

	public function getSqlLimitStart()
	{
		return $this->calculateSqlLimitStart();
	}

	public function getSqlLimitQuantity()
	{
		return $this->itemPerPage;
	}

	public function getItemsVisibleFromNumber()
	{
		if ($this->totalRows === 0) {
			return 0;
		}
		return $this->calculateSqlLimitStart() + 1;
	}

	public function getItemsVisibleTo()
	{
		if ($this->totalRows === 0) {
			return 0;
		}

		if ($this->isLastPage()) {
			$visibleTo = $this->totalRows;
		}
		else if ($this->isFirstPage()) {
			$visibleTo = $this->itemPerPage;
		}
		else {
			$visibleTo = $this->itemPerPage + $this->calculateSqlLimitStart();
		}
		return $visibleTo;
	}

	public function getTotalRows()
	{
		return $this->totalRows;
	}

	public function getPreviousPageNumber()
	{
		if ($this->pageNumber === 1) {
			return 1;
		}
		return $this->pageNumber - 1;
	}

	public function getNextPageNumber()
	{
		if ($this->pageNumber >= $this->totalNumberOfPages) {
			return $this->pageNumber;
		}
		return $this->pageNumber + 1;
	}

	private function isCurrentPageInTheLastPaginateSection()
	{
		return $this->pageNumber + $this->halfWidthOfPagination > $this->totalNumberOfPages;
	}

	private function isPaginationWiderThanTotalPages()
	{
		return $this->paginationWidth > $this->totalNumberOfPages;
	}

	private function isCurrentPageInTheFistPaginateSection()
	{
		return $this->pageNumber - $this->halfWidthOfPagination <= 0;
	}

	public function getPaginationFirstPageNumber()
	{
		if ($this->isCurrentPageInTheLastPaginateSection() && !$this->isPaginationWiderThanTotalPages()) {
			$startNum = $this->biggestPaginationStartPageNumber;
		}
		else if ($this->isCurrentPageInTheFistPaginateSection()) {
			$startNum = 1;
		}
		else {
			$startNum = (int)($this->pageNumber - $this->halfWidthOfPagination);
		}
		return $startNum;
	}

	public function getPaginationLastPageNumber()
	{
		$lastPageNumber = $this->getPaginationFirstPageNumber() + $this->numberOfPagesToLastPage;
		if ($lastPageNumber > $this->totalNumberOfPages) {
			return $this->totalNumberOfPages;
		}
		return $lastPageNumber;
	}

	public function getPaginationPageNumbers()
	{
		if ($this->totalRows == 0) {
			return range(0, 0);
		}
		return range($this->getPaginationFirstPageNumber(), $this->getPaginationLastPageNumber());
	}

}