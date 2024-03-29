<?php

use Bitrix\Main;
use Bitrix\Main\Localization;
use Bitrix\Crm\Order;
use Bitrix\Sale;
use Bitrix\Catalog;
use Bitrix\Catalog\v2\Helpers;
use Bitrix\Iblock;
use Bitrix\Crm\CompanyTable;
use Bitrix\Main\Loader;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

Main\Loader::includeModule('sale');

class SalesCenterOrderDetails extends CBitrixComponent
{
	/** @var Order\Order $order */
	protected $order;

	public function onPrepareComponentParams($params)
	{
		$params["CACHE_TIME"] = 3600;
		$params['CACHE_GROUPS'] = (isset($params['CACHE_GROUPS']) && $params['CACHE_GROUPS'] == 'N' ? 'N' : 'Y');

		$params['ID'] = (int)$params['ID'];
		$params['PAYMENT_ID'] = (int)$params['PAYMENT_ID'];

		$params['ALLOW_INNER'] = 'N';

		if (empty($params["ACTIVE_DATE_FORMAT"]))
		{
			$params["ACTIVE_DATE_FORMAT"] = Main\Type\Date::getFormat();
		}

		if (!is_array($params["CUSTOM_SELECT_PROPS"]))
		{
			$params["CUSTOM_SELECT_PROPS"] = [];
		}
		if (!in_array('PROPERTY_MORE_PHOTO', $params['CUSTOM_SELECT_PROPS']))
		{
			$params['CUSTOM_SELECT_PROPS'][] = 'PROPERTY_MORE_PHOTO';
		}

		// resample sizes
		$params["PICTURE_WIDTH"] = $params["PICTURE_WIDTH"] ?? 110;
		$params["PICTURE_HEIGHT"] = $params["PICTURE_HEIGHT"] ?? 110;

		// resample type for images
		if (!in_array($params['RESAMPLE_TYPE'], [BX_RESIZE_IMAGE_EXACT, BX_RESIZE_IMAGE_PROPORTIONAL, BX_RESIZE_IMAGE_PROPORTIONAL_ALT]))
		{
			$params['RESAMPLE_TYPE'] = BX_RESIZE_IMAGE_PROPORTIONAL;
		}

		if (!$params['HEADER_TITLE'])
		{
			if (Loader::includeModule('crm'))
			{
				$res = CompanyTable::getList(
					[
						'select' => [
							'ID', 'TITLE'
						],
						'filter' => [
							'=IS_MY_COMPANY' => 'Y'
						],
						'order' => [
							'DATE_MODIFY' => 'desc'
						]
					]
				);
				if ($row = $res->fetch())
				{
					$title = $row['TITLE'];
				}
			}
			$params['HEADER_TITLE'] = $title ?? 'Company 24';
		}

		return $params;
	}

	/**
	 * @return void
	 */
	protected function checkOrder()
	{
		if (!$this->order)
		{
			$this->doCaseOrderIdNotSet();
		}
	}

	/**
	 * Function could describe what to do when order ID not set. By default, component will redirect to list page.
	 *
	 * @throws Main\SystemException
	 * @return void
	 */
	protected function doCaseOrderIdNotSet()
	{
		throw new Main\SystemException(
			Localization\Loc::getMessage("SPOD_NO_ORDER", array("#ID#" => $this->arParams["ID"]))
		);
	}

	protected function checkAuthorized()
	{
		$context = Main\Context::getCurrent();
		$request = $context->getRequest();

		if ($request->get('access') !== $this->order->getHash())
		{
			throw new Main\SystemException(
				Localization\Loc::getMessage("SPOD_ACCESS_DENIED")
			);
		}
	}

	protected function loadOrder($id)
	{
		$registry = Sale\Registry::getInstance(\Bitrix\Sale\Registry::REGISTRY_TYPE_ORDER);
		/** @var Order\Order $orderClassName */
		$orderClassName = $registry->getOrderClassName();

		if (!$this->order)
		{
			$this->order = $orderClassName::load($id);
		}
	}

	protected function obtainData()
	{
		$this->obtainOrder();
		$this->obtainBasket();
		$this->obtainShipment();
		$this->obtainPrice();
		$this->obtainPayment();
	}

	protected function obtainPayment()
	{
		$payment = null;

		if ($this->arParams['PAYMENT_ID'])
		{
			$payment = $this->order->getPaymentCollection()->getItemById($this->arParams['PAYMENT_ID']);
		}
		else
		{
			/** @var Order\Payment $item */
			foreach ($this->order->getPaymentCollection() as $item)
			{
				if (!$item->isPaid())
				{
					$payment = $item;
					break;
				}
			}
		}

		if ($payment)
		{
			$this->arResult['ACCOUNT_NUMBER'] = $payment->getField('ACCOUNT_NUMBER');
			$this->arResult['PAYMENT'] = $payment->getFieldValues();

			$dateBill = $payment->getField('DATE_BILL');
			if ($dateBill instanceof Main\Type\DateTime)
			{
				$date = new Main\Type\Date($dateBill);
				$this->arResult['DATE_BILL_FORMATTED'] = $date;
			}
			else
			{
				$this->arResult['DATE_BILL_FORMATTED'] = $dateBill;
			}
		}
	}

	protected function getPaymentId()
	{
		if (
			isset($this->arParams['PAYMENT_ID'])
			&& $this->arParams['PAYMENT_ID'] > 0
		)
		{
			return $this->arParams['PAYMENT_ID'];
		}

		return 0;
	}

	protected function obtainOrder()
	{
		$this->arResult['CURRENCY'] = $this->order->getCurrency();
	}

	protected function obtainBasket()
	{
		$this->arResult['BASKET'] = [];

		if ($this->arParams['PAYMENT_ID'])
		{
			/** @var Order\Payment $payment */
			$payment = $this->order->getPaymentCollection()->getItemById($this->arParams['PAYMENT_ID']);
			if ($payment)
			{
				/** @var Order\PayableBasketItem $item */
				foreach ($payment->getPayableItemCollection()->getBasketItems() as $item)
				{
					/** @var Order\BasketItem $basketItem */
					$basketItem = $item->getEntityObject();

					$basketValues = $this->extractBasketItemData($basketItem);
					$basketValues['QUANTITY'] = $item->getQuantity();

					$this->arResult['BASKET'][$basketValues['ID']] = $basketValues;
				}
			}
		}
		elseif ($this->order)
		{
			foreach ($this->order->getBasket() as $basketItem)
			{
				$basketValues = $this->extractBasketItemData($basketItem);

				$this->arResult['BASKET'][$basketValues['ID']] = $basketValues;
			}
		}
	}

	protected function extractBasketItemData(Order\BasketItem $basketItem)
	{
		$discounts = $this->order->getDiscount();
		$showPrices = $discounts->getShowPrices();

		$data = $showPrices['BASKET'][$basketItem->getBasketCode()];

		$basketItem->setFieldNoDemand('BASE_PRICE', $data['SHOW_BASE_PRICE']);
		$basketItem->setFieldNoDemand('PRICE', $data['SHOW_PRICE']);
		$basketItem->setFieldNoDemand('DISCOUNT_PRICE', $data['SHOW_DISCOUNT']);

		$basketValues = $basketItem->getFieldValues();

		$propertyCollection = $basketItem->getPropertyCollection();
		$basketValues['PROPERTIES'] = $propertyCollection ? $propertyCollection->getPropertyValues() : [];
		unset($basketValues['PROPERTIES']['CATALOG.XML_ID'], $basketValues['PROPERTIES']['PRODUCT.XML_ID']);

		$basketValues['FORMATED_PRICE'] = SaleFormatCurrency($basketValues["PRICE"], $basketValues["CURRENCY"]);
		$basketValues['FORMATED_BASE_PRICE'] = SaleFormatCurrency($basketValues["BASE_PRICE"], $basketValues["CURRENCY"]);

		$iblockId = static::getIblockId($basketValues['PRODUCT_ID']);
		if ($iblockId)
		{
			$productRepository = Catalog\v2\IoC\ServiceContainer::getProductRepository($iblockId);
			if ($productRepository)
			{
				$product = $productRepository->getEntityById($basketValues['PRODUCT_ID']);
				if (!$product)
				{
					$skuRepository = Catalog\v2\IoC\ServiceContainer::getSkuRepository($iblockId);
					if ($skuRepository)
					{
						/** @var Catalog\v2\BaseEntity $product */
						$product = $skuRepository->getEntityById($basketValues['PRODUCT_ID']);
					}
				}

				$imageCollection = $product->getFrontImageCollection();
				$frontImage = $imageCollection->getFrontImage();

				$frontImageData = null;
				if ($frontImage)
				{
					$frontImageData = $frontImage->getFields();
				}

				if ($frontImageData
					&& isset($frontImageData['FILE_STRUCTURE'])
					&&
					(
						$this->arParams['PICTURE_WIDTH']
						|| $this->arParams['PICTURE_HEIGHT']
					)
				)
				{
					$arFileTmp = CFile::ResizeImageGet(
						$frontImageData['FILE_STRUCTURE'],
						array("width" => $this->arParams['PICTURE_WIDTH'], "height" => $this->arParams['PICTURE_HEIGHT']),
						$this->arParams['PICTURE_RESAMPLE_TYPE'],
						true
					);

					$basketValues["PICTURE"] = array_change_key_case($arFileTmp, CASE_UPPER);
				}
				else
				{
					$basketValues["PICTURE"] = $frontImageData['FILE_STRUCTURE'];
				}
			}

		}

		return $basketValues;
	}

	protected function obtainShipment()
	{
		if ($this->arParams['PAYMENT_ID'])
		{
			/** @var Order\Payment $payment */
			$payment = $this->order->getPaymentCollection()->getItemById($this->arParams['PAYMENT_ID']);
			if ($payment)
			{
				/** @var Sale\PayableShipmentItem $item */
				foreach ($payment->getPayableItemCollection()->getShipments() as $item)
				{
					/** @var Order\Shipment $shipment */
					$shipment = $item->getEntityObject();
					if (!$shipment)
					{
						continue;
					}

					$this->arResult['SHIPMENT'] = $this->extractShipmentData($shipment);
				}
			}
		}
		elseif ($this->order)
		{
			foreach ($this->order->getShipmentCollection() as $shipment)
			{
				$this->arResult['SHIPMENT'] = $this->extractShipmentData($shipment);
			}
		}
	}

	protected function extractShipmentData(Order\Shipment $shipment)
	{
		$fields = $shipment->getFieldValues();

			$fields["PRICE_DELIVERY_FORMATTED"] = SaleFormatCurrency(
			$fields['PRICE_DELIVERY'],
			$fields['CURRENCY']
		);

		return $fields;
	}

	public function obtainPrice()
	{
		if (isset($this->arResult['BASKET']))
		{
			$this->arResult['BASE_PRODUCT_SUM'] = 0;
			$this->arResult['PRODUCT_SUM'] = 0;
			$this->arResult['DISCOUNT_VALUE'] = 0;

			foreach ($this->arResult['BASKET'] as $item)
			{
				$this->arResult['BASE_PRODUCT_SUM'] += $item["BASE_PRICE"] * $item['QUANTITY'];
				$this->arResult['PRODUCT_SUM'] += $item["PRICE"] * $item['QUANTITY'];
				$this->arResult['DISCOUNT_VALUE'] += $item["DISCOUNT_PRICE"] * $item['QUANTITY'];
			}
		}
	}

	private static function getIblockId($productId)
	{
		$iblockData = Iblock\ElementTable::getList([
			'select' => ['IBLOCK_ID'],
			'filter' => ['=ID' => $productId],
			'cache' => ['ttl' => 86400],
			'limit' => 1,
		])->fetch();

		return $iblockData['IBLOCK_ID'] ?? null;
	}

	/**
	 * Function implements all the life cycle of the component
	 * @return void
	 */
	public function executeComponent()
	{
		try
		{
			$this->setFrameMode(false);
			$this->checkRequiredModules();

			$this->loadOrder(urldecode(urldecode($this->arParams["ID"])));

			$this->checkOrder();
			$this->checkAuthorized();

			$this->obtainData();

			$this->formatResultPrices();
		}
		catch(Exception $e)
		{
			$this->arResult['ERRORS']['FATAL'][$e->getCode()] = $e->getMessage();
		}

		$this->includeComponentTemplate();
	}

	/**
	 * Function formats price info in arResult
	 * @return void
	 */
	protected function formatResultPrices()
	{
		$this->arResult["PRICE_FORMATED"] = SaleFormatCurrency($this->arResult["PAYMENT"]['SUM'], $this->arResult['CURRENCY']);
		$this->arResult["PRODUCT_SUM_FORMATED"] = SaleFormatCurrency($this->arResult["PRODUCT_SUM"], $this->arResult["CURRENCY"]);
		$this->arResult["BASE_PRODUCT_SUM_FORMATED"] = SaleFormatCurrency($this->arResult["BASE_PRODUCT_SUM"], $this->arResult["CURRENCY"]);
		$this->arResult["PRODUCT_SUM_DISCOUNT_FORMATED"] = SaleFormatCurrency(
			$this->arResult["BASE_PRODUCT_SUM"] - $this->arResult["PRODUCT_SUM"],
			$this->arResult["CURRENCY"]
		);

		if (doubleval($this->arResult["DISCOUNT_VALUE"]))
		{
			$this->arResult["DISCOUNT_VALUE_FORMATED"] = SaleFormatCurrency(
				$this->arResult["DISCOUNT_VALUE"],
				$this->arResult["CURRENCY"]
			);
		}

		if (doubleval($this->arResult["SUM_PAID"]))
		{
			$this->arResult["SUM_PAID_FORMATED"] = SaleFormatCurrency(
				$this->arResult["SUM_PAID"],
				$this->arResult["CURRENCY"]
			);
		}
	}

	/**
	 * Function checks if required modules installed. If not, throws an exception
	 * @throws Main\SystemException
	 * @return void
	 */
	protected function checkRequiredModules()
	{
		if (!Loader::includeModule('sale'))
		{
			throw new Main\SystemException(
				Localization\Loc::getMessage("SPOD_SALE_MODULE_NOT_INSTALL")
			);
		}
	}
}