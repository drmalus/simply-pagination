<?php

namespace SimplyPagination;


class Pagination
{
	private $buttonVariables = ['{{class}}', '{{href}}', '{{page_number}}'];
	private $firstButtonClass = 'prev';
	private $prevButtonClass = 'prev';
	private $numberButtonClass = '';
	private $nextButtonClass = 'prev';
	private $lastButtonClass = 'prev';
	private $firstButtonTemplate = '<li class="{{class}}"><a href="{{href}}"><i class="fa fa-angle-double-left"></i></a></li>';
	private $prevButtonTemplate = '<li class="{{class}}"><a href="{{href}}"><i class="fa fa-angle-left"></i></a></li>';
	private $pageButtonTemplate = '<li class="{{class}}"><a href="{{href}}">{{page_number}}</a></li>';
	private $nextButtonTemplate = '<li class="{{class}}"><a href="{{href}}"><i class="fa fa-angle-right"></i></a></li>';
	private $lastButtonTemplate = '<li class="{{class}}"><a href="{{href}}"><i class="fa fa-angle-double-right"></i></a></li>';
	private $options = [];
	private $queryParams = [];
	private $queryString = '';
	private $requestUri = '';
	/**
	 * @var PaginationCalculator
	 */
	private $calculator;

	public function __construct(array $options)
	{
		$this->options = $options;
		$this->parseRequestUri();
		$this->addPageNumberToOptions();
		$this->addItemPerPageToOptions();
		$this->addPaginationWidthToOptions();

		$this->calculator = new PaginationCalculator($this->options);
	}

	private function parseRequestUri()
	{
		$this->requestUri = $_SERVER['REQUEST_URI'];
		if (strpos($this->requestUri, '?') === false) {
			$this->requestUri .= '?';
		}
		list($this->requestUri, $this->queryString) = explode('?', $this->requestUri);
		$this->queryParams = $this->buildQueryParamsFromQueryString();
	}

	private function buildQueryParamsFromQueryString()
	{
		$paramPairs = explode('&', $this->queryString);
		foreach($paramPairs as $index => $paramString) {
			if (strpos($paramString, '=') !== false) {
				list($key, $val) = explode('=', $paramString);
				$paramPairs[$key] = $val;
			}
			unset($paramPairs[$index]);
		}
		return $paramPairs;
	}

	private function addQueryParamToOptionIfExists($paramKey)
	{
		if (key_exists($paramKey, $this->queryParams)) {
			$this->options[$paramKey] = $this->queryParams[$paramKey];
		}
	}

	private function addPageNumberToOptions()
	{
		$this->addQueryParamToOptionIfExists('page_number');
	}

	private function addPaginationWidthToOptions()
	{
		$this->addQueryParamToOptionIfExists('pagination_width');
	}

	private function addItemPerPageToOptions()
	{
		$this->addQueryParamToOptionIfExists('item_per_page');
	}

	private function setQueryParam($key, $value)
	{
		$this->queryParams[$key] = $value;
	}

	private function createPagination()
	{
		$paginatorPagesList = "<ul class=\"pagination\">\n";
		$paginatorPagesList .= $this->createButtonFromTemplate('first',1)."\n";
		$paginatorPagesList .= $this->createButtonFromTemplate('prev', $this->calculator->getPreviousPageNumber())."\n";
		foreach ($this->calculator->getPaginationPageNumbers() as $pageNumber) {
			$paginatorPagesList .= $this->createButtonFromTemplate('page', $pageNumber)."\n";
		}

		$paginatorPagesList .= $this->createButtonFromTemplate('next', $this->calculator->getNextPageNumber())."\n";
		$paginatorPagesList .= $this->createButtonFromTemplate('last', $this->calculator->getTotalNumberOfPages())."\n";
		$paginatorPagesList .= '</ul>';
		return $paginatorPagesList;
	}

	private function createButtonHrefByPageNumber($pageNumber)
	{
		$this->setQueryParam('page_number', $pageNumber);
		return $this->requestUri . '?' . http_build_query($this->queryParams);
	}

	private function createButtonFromTemplate($buttonType = '', $pageNumber)
	{
		$buttonTemplateProperty = "{$buttonType}ButtonTemplate";
		if (is_null($this->{$buttonTemplateProperty})) {
			return '';
		}

		$href = $this->createButtonHrefByPageNumber($pageNumber);

		$buttonClassProperty = "{$buttonType}ButtonClass";
		$btnClass = $this->{$buttonClassProperty};
		$btnClass .= $this->calculator->getCurrentPageNumber() === $pageNumber ? ' active' : '';
		return str_replace(
			$this->buttonVariables, [$btnClass, $href, $pageNumber], $this->{$buttonTemplateProperty}
		);
	}

	public function makeHtml()
	{
		return $this->createPagination();
	}

	public function setFirstButtonTemplate($templateString)
	{
		$this->firstButtonTemplate = $templateString;
	}

	public function setPrevButtonTemplate($prevButtonTemplate)
	{
		$this->prevButtonTemplate = $prevButtonTemplate;
	}

	public function setPageButtonTemplate($pageButtonTemplate)
	{
		$this->pageButtonTemplate = $pageButtonTemplate;
	}

	public function setNextButtonTemplate($nextButtonTemplate)
	{
		$this->nextButtonTemplate = $nextButtonTemplate;
	}

	public function setLastButtonTemplate($lastButtonTemplate)
	{
		$this->lastButtonTemplate = $lastButtonTemplate;
	}
}