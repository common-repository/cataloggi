<?php

/**
 * Amount class.
 *
 * @package     cataloggi
 * @subpackage  public/shopping-cart/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_Amount {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name    The name of the plugin.
	 * @param      string    $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Display amount on public view.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  string $amount
	 * @return string $amount
	 */
	public static function ctlggi_amount_public($amount) 
	{ 
	  if( !empty($amount) ){
		 
		$amount = CTLGGI_Amount::ctlggi_format_amount($amount);
		  
		$amount = CTLGGI_Amount::ctlggi_currency_symbol_position($amount);  
		  
		return $amount;
		
	  } else {
		 //return; 
		 $amount = CTLGGI_Amount::ctlggi_currency_symbol_position($amount='0');  
		 return $amount;
	  }
	}

	/**
	 * Amount for hidden fields.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  float $amount - 123.00
	 * @return string $amount - 123.00
	 */
	public static function ctlggi_amount_hidden($amount) 
	{ 
	  // check if amount numeric and positive
	  if( is_numeric($amount) && $amount > 0 ){
		   
        $amount = CTLGGI_Amount::ctlggi_format_amount($amount);
		$amount = CTLGGI_Amount::ctlggi_format_amount_to_string($amount); // ?
		  
		return $amount;
		
	  } else {
		 return 0;
	  }
	}
	
	/**
	 * Amount to string.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  float $amount - 123.00
	 * @return string $amount - 123.00
	 */
	public static function ctlggi_format_amount_to_string($amount) {
	  
	  // get options
	  $ctlggi_currency_options = get_option('ctlggi_currency_options');
	  
	  $thousand_separator = sanitize_text_field( stripslashes( $ctlggi_currency_options['thousand_separator'] ) );
	  $decimal_separator  = sanitize_text_field( stripslashes( $ctlggi_currency_options['decimal_separator'] ) );
		
	  if (empty($decimal_separator)) {
			$locale = localeconv();
			$dec_point = $locale['decimal_point'];
	  }
	  
	 // $amount = floatval(str_replace($decimal_separator, '.', preg_replace('/[^\d'.preg_quote($decimal_separator).']/', '', $amount))); // -> 1234.5
	  $amount = str_replace($decimal_separator, '.', preg_replace('/[^\d'.preg_quote($decimal_separator).']/', '', $amount)); // -> 1234.50
	  
	  return $amount;
	}

	/**
	 * Format Amount.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  float $amount - 123.00
	 * @return string $amount - 123.00
	 */
	public static function ctlggi_format_amount($amount) 
	{ 	
	  // check if amount numeric and positive
	  if( !empty( $amount ) ){
	  
	  // get options
	  $ctlggi_currency_options = get_option('ctlggi_currency_options');
	  
	  $thousand_separator = sanitize_text_field( stripslashes( $ctlggi_currency_options['thousand_separator'] ) );
	  $decimal_separator  = sanitize_text_field( stripslashes( $ctlggi_currency_options['decimal_separator'] ) );
	  $number_of_decimals = sanitize_text_field( stripslashes( $ctlggi_currency_options['number_of_decimals'] ) );
	  
	  //if ( is_float($amount) ) {
		  // check if amount well formatted
		  if ( $thousand_separator == ',' and $decimal_separator == '.' ) {
			  $amount = number_format($amount, $number_of_decimals, $decimal_separator, $thousand_separator); // 1,234.56
		  } elseif ( $thousand_separator == '.' and $decimal_separator == ',' ) {
			  $amount = number_format($amount, $number_of_decimals, $decimal_separator, $thousand_separator); // 1.234,56
		  } else {
			  // error,  Non well formed amount.
			  $amount = __('Error: Not well formed amount.', 'cataloggi');
		  }
	  //}
	  
	  return $amount;
	  } else {
		 return $amount;
	  }
	}

	/**
	 * Amount set currency symbol position. (left or right)
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  float $amount - 123.00
	 * @return void $priceandsymbol
	 */
	public static function ctlggi_currency_symbol_position($amount) 
	{ 
	  if ($amount >= 0) {  
		
		// get options
		$ctlggi_currency_options = get_option('ctlggi_currency_options');
		
		//currency symbol
		$catalog_currency = $ctlggi_currency_options['catalog_currency'];
		$currencysymbol = CTLGGI_Amount::ctlggi_get_currency_data_symbol( $catalog_currency );
		
		// use span - to identify for jquery updates
		$currencyspan = '<span class="ctlggi-currency-symbol">' . esc_html( $currencysymbol ) . '</span>';
		$amountspan   = '<span class="ctlggi-item-price">' . esc_html( $amount ) . '</span>';
		
		$currency_position = $ctlggi_currency_options['currency_position'];
		if ( $currency_position == 'Left' ) {
			return $priceandsymbol = $currencyspan . $amountspan;
		} else {
			return $priceandsymbol = $amountspan . ' ' . $currencyspan;
		}
		
	  } else {
		 return; 
	  }
	}
	
	/**
	 * Admin orders list, order total set Amount currency symbol position. (left or right)
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  float $amount - 123.00
	 * @param  string $currency_symbol
	 * @return void $priceandsymbol
	 */
	public static function ctlggi_orders_currency_symbol_position($amount, $currency_symbol) 
	{ 
	  if ($amount >= 0) {  
		
		// get options
		$ctlggi_currency_options = get_option('ctlggi_currency_options');
		
		// use span - to identify for jquery updates
		$currencyspan = '<span class="ctlggi-currency-symbol">' . esc_html( $currency_symbol ) . '</span>';
		$amountspan   = '<span class="ctlggi-item-price">' . esc_html( $amount ) . '</span>';
		
		$currency_position = $ctlggi_currency_options['currency_position'];
		if ( $currency_position == 'Left' ) {
			return $priceandsymbol = $currencyspan . $amountspan;
		} else { // right
			return $priceandsymbol = $amountspan . ' ' . $currencyspan;
		}
		
	  } else {
		 return; 
	  }
	}

	/**
	 * Get currency name. e.g. British Pound
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  string $currency
	 * @return string $currency_name
	 */
	public static function ctlggi_get_currency_name( $currency ) {
		if ( isset( $currency ) ) {

			$available_currencies = CTLGGI_Amount::ctlggi_available_currencies();

			if ( isset( $available_currencies ) && array_key_exists( $currency, $available_currencies ) ) {
				$currency_name = $available_currencies[ $currency ]['name'];
			} else {
				$currency_name = strtoupper( $currency );
			}

			return $currency_name;
		}

		return null;
	}

	/**
	 * Get currency symbol. e.g. £
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  string $currency
	 * @return string $currency_symbol
	 */
	public static function ctlggi_get_currency_data_symbol( $currency ) {
		if ( isset( $currency ) ) {

			$available_currencies = CTLGGI_Amount::ctlggi_available_currencies();

			if ( isset( $available_currencies ) && array_key_exists( $currency, $available_currencies ) ) {
				$currency_symbol = $available_currencies[ $currency ]['symbol'];
			} else {
				$currency_symbol = strtoupper( $currency );
			}

			return $currency_symbol;
		}

		return null;
	}

	/**
	 * Amound round half up.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  string $amount
	 * @return string $amount
	 */
	public static function ctlggi_round_half_up_amount($amount) 
	{ 
	  // check if amount numeric and positive
	  if( is_numeric($amount) && $amount > 0 ){
		  // http://php.net/manual/en/function.round.php
		  /* Using PHP_ROUND_HALF_UP with 1 decimal digit precision */
		  $amount = round( $amount, 1, PHP_ROUND_HALF_UP);
		  return $amount;
	  } else {
		 return;
	  }
	}

	/**
	 * Currencies array.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @return array
	 */
	public static function ctlggi_available_currencies() { 
        return array(
            'gbp' => array(
                'code'   => 'GBP',
                'name'   => 'British Pound',
                'symbol' => '£'
            ),
            'eur' => array(
                'code'   => 'EUR',
                'name'   => 'Euro',
                'symbol' => '€'
            ),
            'usd' => array(
                'code'   => 'USD',
                'name'   => 'United States Dollar',
                'symbol' => '$'
            ),
            'aed' => array(
                'code'   => 'AED',
                'name'   => 'United Arab Emirates Dirham',
                'symbol' => 'DH'
            ),
            'afn' => array(
                'code'   => 'AFN',
                'name'   => 'Afghan Afghani',
                'symbol' => '؋'
            ),
            'all' => array(
                'code'   => 'ALL',
                'name'   => 'Albanian Lek',
                'symbol' => 'L'
            ),
            'amd' => array(
                'code'   => 'AMD',
                'name'   => 'Armenian Dram',
                'symbol' => '֏'
            ),
            'ang' => array(
                'code'   => 'ANG',
                'name'   => 'Netherlands Antillean Gulden',
                'symbol' => 'ƒ'
            ),
            'aoa' => array(
                'code'   => 'AOA',
                'name'   => 'Angolan Kwanza',
                'symbol' => 'Kz'
            ),
            'ars' => array(
                'code'   => 'ARS',
                'name'   => 'Argentine Peso',
                'symbol' => '$'
            ),
            'aud' => array(
                'code'   => 'AUD',
                'name'   => 'Australian Dollar',
                'symbol' => '$'
            ),
            'awg' => array(
                'code'   => 'AWG',
                'name'   => 'Aruban Florin',
                'symbol' => 'ƒ'
            ),
            'azn' => array(
                'code'   => 'AZN',
                'name'   => 'Azerbaijani Manat',
                'symbol' => 'm.'
            ),
            'bam' => array(
                'code'   => 'BAM',
                'name'   => 'Bosnia & Herzegovina Convertible Mark',
                'symbol' => 'KM'
            ),
            'bbd' => array(
                'code'   => 'BBD',
                'name'   => 'Barbadian Dollar',
                'symbol' => 'Bds$'
            ),
            'bdt' => array(
                'code'   => 'BDT',
                'name'   => 'Bangladeshi Taka',
                'symbol' => '৳'
            ),
            'bgn' => array(
                'code'   => 'BGN',
                'name'   => 'Bulgarian Lev',
                'symbol' => 'лв'
            ),
            'bif' => array(
                'code'   => 'BIF',
                'name'   => 'Burundian Franc',
                'symbol' => 'FBu'
            ),
            'bmd' => array(
                'code'   => 'BMD',
                'name'   => 'Bermudian Dollar',
                'symbol' => 'BD$'
            ),
            'bnd' => array(
                'code'   => 'BND',
                'name'   => 'Brunei Dollar',
                'symbol' => 'B$'
            ),
            'bob' => array(
                'code'   => 'BOB',
                'name'   => 'Bolivian Boliviano',
                'symbol' => 'Bs.'
            ),
            'brl' => array(
                'code'   => 'BRL',
                'name'   => 'Brazilian Real',
                'symbol' => 'R$'
            ),
            'bsd' => array(
                'code'   => 'BSD',
                'name'   => 'Bahamian Dollar',
                'symbol' => 'B$'
            ),
            'bwp' => array(
                'code'   => 'BWP',
                'name'   => 'Botswana Pula',
                'symbol' => 'P'
            ),
            'bzd' => array(
                'code'   => 'BZD',
                'name'   => 'Belize Dollar',
                'symbol' => 'BZ$'
            ),
            'cad' => array(
                'code'   => 'CAD',
                'name'   => 'Canadian Dollar',
                'symbol' => '$'
            ),
            'cdf' => array(
                'code'   => 'CDF',
                'name'   => 'Congolese Franc',
                'symbol' => 'CF'
            ),
            'chf' => array(
                'code'   => 'CHF',
                'name'   => 'Swiss Franc',
                'symbol' => 'Fr'
            ),
            'clp' => array(
                'code'   => 'CLP',
                'name'   => 'Chilean Peso',
                'symbol' => 'CLP$'
            ),
            'cny' => array(
                'code'   => 'CNY',
                'name'   => 'Chinese Renminbi Yuan',
                'symbol' => '¥'
            ),
            'cop' => array(
                'code'   => 'COP',
                'name'   => 'Colombian Peso',
                'symbol' => 'COL$'
            ),
            'crc' => array(
                'code'   => 'CRC',
                'name'   => 'Costa Rican Colón',
                'symbol' => '₡'
            ),
            'cve' => array(
                'code'   => 'CVE',
                'name'   => 'Cape Verdean Escudo',
                'symbol' => 'Esc'
            ),
            'czk' => array(
                'code'   => 'CZK',
                'name'   => 'Czech Koruna',
                'symbol' => 'Kč'
            ),
            'djf' => array(
                'code'   => 'DJF',
                'name'   => 'Djiboutian Franc',
                'symbol' => 'Fr'
            ),
            'dkk' => array(
                'code'   => 'DKK',
                'name'   => 'Danish Krone',
                'symbol' => 'kr'
            ),
            'dop' => array(
                'code'   => 'DOP',
                'name'   => 'Dominican Peso',
                'symbol' => 'RD$'
            ),
            'dzd' => array(
                'code'   => 'DZD',
                'name'   => 'Algerian Dinar',
                'symbol' => 'DA'
            ),
            'egp' => array(
                'code'   => 'EGP',
                'name'   => 'Egyptian Pound',
                'symbol' => 'L.E.'
            ),
            'etb' => array(
                'code'   => 'ETB',
                'name'   => 'Ethiopian Birr',
                'symbol' => 'Br'
            ),
            'fjd' => array(
                'code'   => 'FJD',
                'name'   => 'Fijian Dollar',
                'symbol' => 'FJ$'
            ),
            'fkp' => array(
                'code'   => 'FKP',
                'name'   => 'Falkland Islands Pound',
                'symbol' => 'FK£'
            ),
            'gel' => array(
                'code'   => 'GEL',
                'name'   => 'Georgian Lari',
                'symbol' => 'ლ'
            ),
            'gip' => array(
                'code'   => 'GIP',
                'name'   => 'Gibraltar Pound',
                'symbol' => '£'
            ),
            'gmd' => array(
                'code'   => 'GMD',
                'name'   => 'Gambian Dalasi',
                'symbol' => 'D'
            ),
            'gnf' => array(
                'code'   => 'GNF',
                'name'   => 'Guinean Franc',
                'symbol' => 'FG'
            ),
            'gtq' => array(
                'code'   => 'GTQ',
                'name'   => 'Guatemalan Quetzal',
                'symbol' => 'Q'
            ),
            'gyd' => array(
                'code'   => 'GYD',
                'name'   => 'Guyanese Dollar',
                'symbol' => 'G$'
            ),
            'hkd' => array(
                'code'   => 'HKD',
                'name'   => 'Hong Kong Dollar',
                'symbol' => 'HK$'
            ),
            'hnl' => array(
                'code'   => 'HNL',
                'name'   => 'Honduran Lempira',
                'symbol' => 'L'
            ),
            'hrk' => array(
                'code'   => 'HRK',
                'name'   => 'Croatian Kuna',
                'symbol' => 'kn'
            ),
            'htg' => array(
                'code'   => 'HTG',
                'name'   => 'Haitian Gourde',
                'symbol' => 'G'
            ),
            'huf' => array(
                'code'   => 'HUF',
                'name'   => 'Hungarian Forint',
                'symbol' => 'Ft'
            ),
            'idr' => array(
                'code'   => 'IDR',
                'name'   => 'Indonesian Rupiah',
                'symbol' => 'Rp'
            ),
            'ils' => array(
                'code'   => 'ILS',
                'name'   => 'Israeli New Sheqel',
                'symbol' => '₪'
            ),
            'inr' => array(
                'code'   => 'INR',
                'name'   => 'Indian Rupee',
                'symbol' => '₹'
            ),
            'isk' => array(
                'code'   => 'ISK',
                'name'   => 'Icelandic Króna',
                'symbol' => 'ikr'
            ),
            'jmd' => array(
                'code'   => 'JMD',
                'name'   => 'Jamaican Dollar',
                'symbol' => 'J$'
            ),
            'jpy' => array(
                'code'   => 'JPY',
                'name'   => 'Japanese Yen',
                'symbol' => '¥'
            ),
            'kes' => array(
                'code'   => 'KES',
                'name'   => 'Kenyan Shilling',
                'symbol' => 'Ksh'
            ),
            'kgs' => array(
                'code'   => 'KGS',
                'name'   => 'Kyrgyzstani Som',
                'symbol' => 'COM'
            ),
            'khr' => array(
                'code'   => 'KHR',
                'name'   => 'Cambodian Riel',
                'symbol' => '៛'
            ),
            'kmf' => array(
                'code'   => 'KMF',
                'name'   => 'Comorian Franc',
                'symbol' => 'CF'
            ),
            'krw' => array(
                'code'   => 'KRW',
                'name'   => 'South Korean Won',
                'symbol' => '₩'
            ),
            'kyd' => array(
                'code'   => 'KYD',
                'name'   => 'Cayman Islands Dollar',
                'symbol' => 'CI$'
            ),
            'kzt' => array(
                'code'   => 'KZT',
                'name'   => 'Kazakhstani Tenge',
                'symbol' => '₸'
            ),
            'lak' => array(
                'code'   => 'LAK',
                'name'   => 'Lao Kip',
                'symbol' => '₭'
            ),
            'lbp' => array(
                'code'   => 'LBP',
                'name'   => 'Lebanese Pound',
                'symbol' => 'LL'
            ),
            'lkr' => array(
                'code'   => 'LKR',
                'name'   => 'Sri Lankan Rupee',
                'symbol' => 'SLRs'
            ),
            'lrd' => array(
                'code'   => 'LRD',
                'name'   => 'Liberian Dollar',
                'symbol' => 'L$'
            ),
            'lsl' => array(
                'code'   => 'LSL',
                'name'   => 'Lesotho Loti',
                'symbol' => 'M'
            ),
            'mad' => array(
                'code'   => 'MAD',
                'name'   => 'Moroccan Dirham',
                'symbol' => 'DH'
            ),
            'mdl' => array(
                'code'   => 'MDL',
                'name'   => 'Moldovan Leu',
                'symbol' => 'MDL'
            ),
            'mga' => array(
                'code'   => 'MGA',
                'name'   => 'Malagasy Ariary',
                'symbol' => 'Ar'
            ),
            'mkd' => array(
                'code'   => 'MKD',
                'name'   => 'Macedonian Denar',
                'symbol' => 'ден'
            ),
            'mnt' => array(
                'code'   => 'MNT',
                'name'   => 'Mongolian Tögrög',
                'symbol' => '₮'
            ),
            'mop' => array(
                'code'   => 'MOP',
                'name'   => 'Macanese Pataca',
                'symbol' => 'MOP$'
            ),
            'mro' => array(
                'code'   => 'MRO',
                'name'   => 'Mauritanian Ouguiya',
                'symbol' => 'UM'
            ),
            'mur' => array(
                'code'   => 'MUR',
                'name'   => 'Mauritian Rupee',
                'symbol' => 'Rs'
            ),
            'mvr' => array(
                'code'   => 'MVR',
                'name'   => 'Maldivian Rufiyaa',
                'symbol' => 'Rf.'
            ),
            'mwk' => array(
                'code'   => 'MWK',
                'name'   => 'Malawian Kwacha',
                'symbol' => 'MK'
            ),
            'mxn' => array(
                'code'   => 'MXN',
                'name'   => 'Mexican Peso',
                'symbol' => '$'
            ),
            'myr' => array(
                'code'   => 'MYR',
                'name'   => 'Malaysian Ringgit',
                'symbol' => 'RM'
            ),
            'mzn' => array(
                'code'   => 'MZN',
                'name'   => 'Mozambican Metical',
                'symbol' => 'MT'
            ),
            'nad' => array(
                'code'   => 'NAD',
                'name'   => 'Namibian Dollar',
                'symbol' => 'N$'
            ),
            'ngn' => array(
                'code'   => 'NGN',
                'name'   => 'Nigerian Naira',
                'symbol' => '₦'
            ),
            'nio' => array(
                'code'   => 'NIO',
                'name'   => 'Nicaraguan Córdoba',
                'symbol' => 'C$'
            ),
            'nok' => array(
                'code'   => 'NOK',
                'name'   => 'Norwegian Krone',
                'symbol' => 'kr'
            ),
            'npr' => array(
                'code'   => 'NPR',
                'name'   => 'Nepalese Rupee',
                'symbol' => 'NRs'
            ),
            'nzd' => array(
                'code'   => 'NZD',
                'name'   => 'New Zealand Dollar',
                'symbol' => 'NZ$'
            ),
            'pab' => array(
                'code'   => 'PAB',
                'name'   => 'Panamanian Balboa',
                'symbol' => 'B/.'
            ),
            'pen' => array(
                'code'   => 'PEN',
                'name'   => 'Peruvian Nuevo Sol',
                'symbol' => 'S/.'
            ),
            'pgk' => array(
                'code'   => 'PGK',
                'name'   => 'Papua New Guinean Kina',
                'symbol' => 'K'
            ),
            'php' => array(
                'code'   => 'PHP',
                'name'   => 'Philippine Peso',
                'symbol' => '₱'
            ),
            'pkr' => array(
                'code'   => 'PKR',
                'name'   => 'Pakistani Rupee',
                'symbol' => 'PKR'
            ),
            'pln' => array(
                'code'   => 'PLN',
                'name'   => 'Polish Złoty',
                'symbol' => 'zł'
            ),
            'pyg' => array(
                'code'   => 'PYG',
                'name'   => 'Paraguayan Guaraní',
                'symbol' => '₲'
            ),
            'qar' => array(
                'code'   => 'QAR',
                'name'   => 'Qatari Riyal',
                'symbol' => 'QR'
            ),
            'ron' => array(
                'code'   => 'RON',
                'name'   => 'Romanian Leu',
                'symbol' => 'RON'
            ),
            'rsd' => array(
                'code'   => 'RSD',
                'name'   => 'Serbian Dinar',
                'symbol' => 'дин'
            ),
            'rub' => array(
                'code'   => 'RUB',
                'name'   => 'Russian Ruble',
                'symbol' => 'руб'
            ),
            'rwf' => array(
                'code'   => 'RWF',
                'name'   => 'Rwandan Franc',
                'symbol' => 'FRw'
            ),
            'sar' => array(
                'code'   => 'SAR',
                'name'   => 'Saudi Riyal',
                'symbol' => 'SR'
            ),
            'sbd' => array(
                'code'   => 'SBD',
                'name'   => 'Solomon Islands Dollar',
                'symbol' => 'SI$'
            ),
            'scr' => array(
                'code'   => 'SCR',
                'name'   => 'Seychellois Rupee',
                'symbol' => 'SRe'
            ),
            'sek' => array(
                'code'   => 'SEK',
                'name'   => 'Swedish Krona',
                'symbol' => 'kr'
            ),
            'sgd' => array(
                'code'   => 'SGD',
                'name'   => 'Singapore Dollar',
                'symbol' => 'S$'
            ),
            'shp' => array(
                'code'   => 'SHP',
                'name'   => 'Saint Helenian Pound',
                'symbol' => '£'
            ),
            'sll' => array(
                'code'   => 'SLL',
                'name'   => 'Sierra Leonean Leone',
                'symbol' => 'Le'
            ),
            'sos' => array(
                'code'   => 'SOS',
                'name'   => 'Somali Shilling',
                'symbol' => 'Sh.So.'
            ),
            'std' => array(
                'code'   => 'STD',
                'name'   => 'São Tomé and Príncipe Dobra',
                'symbol' => 'Db'
            ),
            'srd' => array(
                'code'   => 'SRD',
                'name'   => 'Surinamese Dollar',
                'symbol' => 'SRD'
            ),
            'svc' => array(
                'code'   => 'SVC',
                'name'   => 'Salvadoran Colón',
                'symbol' => '₡'
            ),
            'szl' => array(
                'code'   => 'SZL',
                'name'   => 'Swazi Lilangeni',
                'symbol' => 'E'
            ),
            'thb' => array(
                'code'   => 'THB',
                'name'   => 'Thai Baht',
                'symbol' => '฿'
            ),
            'tjs' => array(
                'code'   => 'TJS',
                'name'   => 'Tajikistani Somoni',
                'symbol' => 'TJS'
            ),
            'top' => array(
                'code'   => 'TOP',
                'name'   => 'Tongan Paʻanga',
                'symbol' => '$'
            ),
            'try' => array(
                'code'   => 'TRY',
                'name'   => 'Turkish Lira',
                'symbol' => '₺'
            ),
            'ttd' => array(
                'code'   => 'TTD',
                'name'   => 'Trinidad and Tobago Dollar',
                'symbol' => 'TT$'
            ),
            'twd' => array(
                'code'   => 'TWD',
                'name'   => 'New Taiwan Dollar',
                'symbol' => 'NT$'
            ),
            'tzs' => array(
                'code'   => 'TZS',
                'name'   => 'Tanzanian Shilling',
                'symbol' => 'TSh'
            ),
            'uah' => array(
                'code'   => 'UAH',
                'name'   => 'Ukrainian Hryvnia',
                'symbol' => '₴'
            ),
            'ugx' => array(
                'code'   => 'UGX',
                'name'   => 'Ugandan Shilling',
                'symbol' => 'USh'
            ),
            'uyu' => array(
                'code'   => 'UYU',
                'name'   => 'Uruguayan Peso',
                'symbol' => '$U'
            ),
            'uzs' => array(
                'code'   => 'UZS',
                'name'   => 'Uzbekistani Som',
                'symbol' => 'UZS'
            ),
            'vnd' => array(
                'code'   => 'VND',
                'name'   => 'Vietnamese Đồng',
                'symbol' => '₫'
            ),
            'vuv' => array(
                'code'   => 'VUV',
                'name'   => 'Vanuatu Vatu',
                'symbol' => 'VT'
            ),
            'wst' => array(
                'code'   => 'WST',
                'name'   => 'Samoan Tala',
                'symbol' => 'WS$'
            ),
            'xaf' => array(
                'code'   => 'XAF',
                'name'   => 'Central African Cfa Franc',
                'symbol' => 'FCFA'
            ),
            'xcd' => array(
                'code'   => 'XCD',
                'name'   => 'East Caribbean Dollar',
                'symbol' => 'EC$'
            ),
            'xof' => array(
                'code'   => 'XOF',
                'name'   => 'West African Cfa Franc',
                'symbol' => 'CFA'
            ),
            'xpf' => array(
                'code'   => 'XPF',
                'name'   => 'Cfp Franc',
                'symbol' => 'F'
            ),
            'yer' => array(
                'code'   => 'YER',
                'name'   => 'Yemeni Rial',
                'symbol' => '﷼'
            ),
            'zar' => array(
                'code'   => 'ZAR',
                'name'   => 'South African Rand',
                'symbol' => 'R'
            ),
            'zmw' => array(
                'code'   => 'ZMW',
                'name'   => 'Zambian Kwacha',
                'symbol' => 'ZK'
            )
        );
    }
	
	
}

?>