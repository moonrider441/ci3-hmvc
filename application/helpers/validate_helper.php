<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('isMessage')) {
    # Check for a message validity
    function isMessage($message)
    {
        return preg_match('/^([^<>{}]|<br \/>)*$/ui', $message);
    }
}

if (!function_exists('isCleanHtml')) {
    /* Check for HTML field validity (no XSS please !) */
    function isCleanHtml($html)
    {
        $jsEvent = 'onmousedown|onmousemove|onmmouseup|onmouseover|onmouseout|onload|onunload|onfocus|onblur|onchange|onsubmit|ondblclick|onclick|onkeydown|onkeyup|onkeypress|onmouseenter|onmouseleave';
        return (!preg_match('/<[ \t\n]*script/ui', $html) && !preg_match('/<.*(' . $jsEvent . ')[ \t\n]*=/ui', $html) && !preg_match('/.*script\:/ui', $html));
    }
}

if (!function_exists('isEmpty')) {
    /////////
    function isEmpty($field)
    {
        return $field === '' OR $field === NULL;
    }
}

if (!function_exists('isEmail')) {
    //####
    function isEmail($email)
    {
        return (isEmpty($email)) ? true : preg_match('/^[a-z0-9!#$%&\'*+\/=?^`{}|~_-]+[.a-z0-9!#$%&\'*+\/=?^`{}|~_-]*@[a-z0-9]+[._a-z0-9-]*\.[a-z0-9]+$/ui', $email);
    }

}

if (!function_exists('isDate')) {
    /**
     * Check for date validity
     *
     * @param string $date Date to validate
     * @return boolean Validity is ok or not
     */
    function isDate($date)
    {
        if (!preg_match('/^([0-9]{4})-((0?[1-9])|(1[0-2]))-((0?[1-9])|([1-2][0-9])|(3[01]))( [0-9]{2}:[0-9]{2}:[0-9]{2})?$/ui', $date, $matches))
            return false;
        return checkdate(intval($matches[2]), intval($matches[5]), intval($matches[0]));
    }
}

if (!function_exists('isPasswd')) {
    /**
     * Check for password validity
     *
     * @param string $passwd Password to validate
     * @param int $size
     * @return bool Validity is ok or not
     */
    function isPasswd($passwd, $size = 5)
    {
        return preg_match('/^[.a-z_0-9-!@#$%\^&*()]{' . $size . ',32}$/ui', $passwd);
    }
}

if (!function_exists('isPrice')) {

    /**
     * Check for price validity
     * @param $price
     * @return int
     */
    function isPrice($price)
    {
        return preg_match('/^[0-9]{1,10}(\.[0-9]{1,9})?$/ui', $price);
    }
}

if (!function_exists('isMobile')) {
    function isMobile($mobile)
    {
        if (strlen($mobile) != 11)
            return false;

        if ($mobile[0] != '0')
            return false;

        if (!is_numeric($mobile))
            return false;

        return true;
    }
}

if (!function_exists('isMd5')) {
    //####
    function isMd5($md5)
    {
        return preg_match('/^[a-z0-9]{32}$/ui', $md5);
    }
}

if (!function_exists('isSha1')) {
    //####
    function isSha1($sha1)
    {
        return preg_match('/^[a-z0-9]{40}$/ui', $sha1);
    }
}

if (!function_exists('isNumber')) {
    /**
     * @param $number
     * @param int $size
     * @return bool
     */
    function isNumber($number, $size = 4)
    {
        return empty($number) OR preg_match('/^[0-9]{1,' . $size . '}$/ui', $number);
    }
}

if (!function_exists('isInt')) {
    /**
     * Check for an integer validity
     *
     * @param $value
     * @return bool Validity is ok or not
     * @internal param int $id Integer to validate
     */
    function isInt($value)
    {
        return ((string)(int)$value === (string)$value OR $value === false);
    }
}

if (!function_exists('isString')) {
    /**
     * Price display method validity
     *
     * @param string $data Data to validate
     * @return boolean Validity is ok or not
     */
    function isString($data)
    {
        return is_string($data);
    }
}

if (!function_exists('isFloat')) {
    //####
    function isFloat($float)
    {
        return strval(floatval($float)) == strval($float);
    }
}

if (!function_exists('isUnsignedFloat')) {
    //####
    function isUnsignedFloat($float)
    {
        return strval(floatval($float)) == strval($float) AND $float >= 0;
    }
}

if (!function_exists('isPhoneNumber')) {
    /** Check for phone number validity
     * @param $phoneNumber
     * @return int
     */
    function isPhoneNumber($phoneNumber)
    {
        return preg_match('/^[+0-9. ()-]*$/ui', $phoneNumber);
    }
}


/** Mostly used in database for insertions (A,B,C),(A,B,C)...
 * @param $list
 * @return bool|int
 */
if (!function_exists('isValuesList')) {
    function isValuesList($list)
    {
        return true;
        return preg_match('/^[0-9,\'(). NULL]+$/ui', $list);
    }
}

if (!function_exists('isGenericName')) {
    //####
    function isGenericName($name)
    {
        return empty($name) OR preg_match('/^[^<>;=#{}]*$/ui', $name);
    }
}

/** Check object validity
 * @param $url
 * @return int
 */
if (!function_exists('isUrl')) {
    function isUrl($url)
    {
        return preg_match('/^([[:alnum:]]|[:#%&_=\(\)\.\? \+\-@\/])+$/ui', $url);
    }
}

/** Check for phone number validity
 * @param $phoneNumber
 * @return int
 */
if (!function_exists('isPhoneNumber')) {
    function isPhoneNumber($phoneNumber)
    {
        return preg_match('/^[+0-9. ()-]*$/ui', $phoneNumber);
    }
}

/**
 * Check for a float number validity
 *
 * @param float $float Float number to validate
 * @return boolean Validity is ok or not
 */
if (!function_exists('isOptFloat')) {
    function isOptFloat($float)
    {
        return empty($float) OR isFloat($float);
    }
}

/**
 * Check for a carrier name validity
 * @param $name
 * @return int
 */
if (!function_exists('isValidPicForThumb')) {
    function isValidPicForThumb($name)
    {
        return preg_match('/^([0-9]{2,3})_([0-9]{2,3})?$/ui', $name);
    }
}

/**
 * Check for an image size validity
 * @param $size
 * @return int
 */
if (!function_exists('isImageSize')) {
    function isImageSize($size)
    {
        return preg_match('/^[0-9]{1,4}$/ui', $size);
    }
}

if (!function_exists('isOptId')) {
    function isOptId($id)
    {
        return empty($id) OR isUnsignedId($id);
    }
}

/**
 * Check for name validity
 * @param $name
 * @return int
 */
if (!function_exists('isName')) {
    function isName($name)
    {
        return preg_match('/^[^0-9!<>,;?=+()@#"°{}_$%:]*$/ui', stripslashes($name));
    }
}

if (!function_exists('isMailName')) {
    /**
     * Check for sender name validity
     * @param $mailName
     * @return int
     */
    function isMailName($mailName)
    {
        return preg_match('/^[^<>;=#{}]*$/ui', $mailName);
    }
}

/**
 * Check for e-mail subject validity
 * @param $mailSubject
 * @return int
 */
if (!function_exists('isMailSubject')) {
    function isMailSubject($mailSubject)
    {
        return preg_match('/^[^<>;{}]*$/ui', $mailSubject);
    }
}

/**
 * Check for icon file validity
 * @param $icon
 * @return int
 */
if (!function_exists('isIconFile')) {
    function isIconFile($icon)
    {
        return preg_match('/^[a-z0-9_-]+\.[gif|jpg|jpeg|png]$/ui', $icon);
    }
}

/**
 * Check for ico file validity
 * @param $icon
 * @return int
 */
if (!function_exists('isIcoFile')) {
    function isIcoFile($icon)
    {
        return preg_match('/^[a-z0-9_-]+\.ico$/ui', $icon);
    }
}

/**
 * Check for image type name validity
 * @param $type
 * @return int
 */
if (!function_exists('isImageTypeName')) {
    function isImageTypeName($type)
    {
        return preg_match('/^[a-z0-9_ -]+$/ui', $type);
    }
}

/**
 * Check for language code (ISO) validity
 * @param $isoCode
 * @return int
 */
if (!function_exists('isLanguageIsoCode')) {
    function isLanguageIsoCode($isoCode)
    {
        return preg_match('/^[a-z]{2,3}$/ui', $isoCode);
    }
}

if (!function_exists('isStateIsoCode')) {
    function isStateIsoCode($isoCode)
    {
        return preg_match('/^[a-z]{1,4}$/ui', $isoCode);
    }
}

/**
 * Check for gender code (ISO) validity
 * @param $isoCode
 * @return int
 */
if (!function_exists('isGenderIsoCode')) {
    function isGenderIsoCode($isoCode)
    {
        return preg_match('/^[0|1|2|9]$/ui', $isoCode);
    }
}

/**
 * Check for gender code (ISO) validity
 * @param $genderName
 * @return int
 */
if (!function_exists('isGenderName')) {
    function isGenderName($genderName)
    {
        return preg_match('/^[a-z.]+$/ui', $genderName);
    }
}

/**
 * Check for discount coupon name validity
 *
 * @param string $discountName Discount coupon name to validate
 * @return boolean Validity is ok or not
 */
if (!function_exists('isDiscountName')) {
    function isDiscountName($discountName)
    {
        return preg_match('/^[^!<>,;?=+()@"°{}_$%:]{3,32}$/ui', $discountName);
    }
}

/**
 * Check for product or category name validity
 *
 * @param string $name Product or category name to validate
 * @return boolean Validity is ok or not
 */
if (!function_exists('isCatalogName')) {
    function isCatalogName($name)
    {
        return preg_match('/^[^<>;=#{}]*$/ui', $name);
    }
}

/**
 * Check for a country name validity
 *
 * @param string $name Country name to validate
 * @return boolean Validity is ok or not
 */
if (!function_exists('isCountryName')) {
    function isCountryName($name)
    {
        return preg_match('/^[a-z -]+$/ui', $name);
    }
}

/**
 * Check for a link (url-rewriting only) validity
 *
 * @param string $link Link to validate
 * @return boolean Validity is ok or not
 */
if (!function_exists('isLinkRewrite')) {
    function isLinkRewrite($link)
    {
        return empty($link) OR preg_match('/^[_a-z0-9-]+$/ui', $link);
    }
}

/**
 * Check for zone name validity
 *
 * @param string $name Zone name to validate
 * @return boolean Validity is ok or not
 */
if (!function_exists('isZoneName')) {
    function isZoneName($name)
    {
        return preg_match('/^[a-z -()]+$/ui', $name);
    }
}

/**
 * Check for a postal address validity
 *
 * @param string $address Address to validate
 * @return boolean Validity is ok or not
 */
if (!function_exists('isAddress')) {
    function isAddress($address)
    {
        return empty($address) OR preg_match('/^[^!<>?=+@{}_$%]*$/ui', $address);
    }
}

/**
 * Check for city name validity
 *
 * @param string $region City name to validate
 * @return boolean Validity is ok or not
 */
if (!function_exists('isCityName')) {
    function isCityName($region)
    {
        return preg_match('/^[^!<>;?=+@#"°{}_$%]*$/ui', $region);
    }
}


/**
 * Check for product reference validity
 *
 * @param string $reference Product reference to validate
 * @return boolean Validity is ok or not
 */
if (!function_exists('isReference')) {
    function isReference($reference)
    {
        return preg_match('/^[^<>;={}]*$/ui', $reference);
    }
}

if (!function_exists('isPasswdAdmin')) {
    function isPasswdAdmin($passwd)
    {
        return isPasswd($passwd, 8);
    }
}

/**
 * Check for configuration key validity
 *
 * @param string $configName Configuration key to validate
 * @return boolean Validity is ok or not
 */
if (!function_exists('isConfigName')) {
    function isConfigName($configName)
    {
        return preg_match('/^[a-z_0-9-]+$/ui', $configName);
    }
}

/**
 * Check for birthDate validity
 *
 * @param string $date birthdate to validate
 * @return boolean Validity is ok or not
 */
if (!function_exists('isBirthDate')) {
    function isBirthDate($date)
    {
        if (empty($date) || $date == '0000-00-00')
            return true;
        if (preg_match('/^([0-9]{4})-((0?[1-9])|(1[0-2]))-((0?[1-9])|([1-2][0-9])|(3[01]))( [0-9]{2}:[0-9]{2}:[0-9]{2})?$/ui', $date, $birthDate)) {
            if ($birthDate[1] >= date('Y') - 9)
                return false;
            return true;
        }
        return false;
    }
}

/**
 * Check for boolean validity
 *
 * @param boolean $bool Boolean to validate
 * @return boolean Validity is ok or not
 */
if (!function_exists('isBool')) {
    function isBool($bool)
    {
        return is_null($bool) OR is_bool($bool) OR preg_match('/^[0|1]{1}$/ui', $bool);
    }
}


/**
 * Check for barcode validity (EAN-13)
 *
 * @param string $ean13 Barcode to validate
 * @return boolean Validity is ok or not
 */
if (!function_exists('isEan13')) {
    function isEan13($ean13)
    {
        return !$ean13 OR preg_match('/^[0-9]{0,13}$/ui', $ean13);
    }
}

/**
 * Check for postal code validity
 *
 * @param string $postcode Postal code to validate
 * @return boolean Validity is ok or not
 */
if (!function_exists('isPostCode')) {
    function isPostCode($postcode)
    {
        return preg_match('/^[a-z 0-9-]+$/ui', $postcode);
    }
}

/**
 * Check for table or identifier validity
 * Mostly used in database for ordering : ASC / DESC
 *
 * @param string $orderWay Keyword to validate
 * @return boolean Validity is ok or not
 */
if (!function_exists('isOrderWay')) {
    function isOrderWay($orderWay)
    {
        return ($orderWay === 'ASC' | $orderWay === 'DESC' | $orderWay === 'asc' | $orderWay === 'desc');
    }
}

/**
 * Check for table or identifier validity
 * Mostly used in database for ordering : ORDER BY field
 *
 * @param string $orderBy Field to validate
 * @return boolean Validity is ok or not
 */
if (!function_exists('isOrderBy')) {
    function isOrderBy($orderBy)
    {
        return preg_match('/^[a-z0-9_-]+$/ui', $orderBy);
    }
}

/**
 * Check for tags list validity
 *
 * @param string $list List to validate
 * @return boolean Validity is ok or not
 */
if (!function_exists('isTagsList')) {
    function isTagsList($list)
    {
        return preg_match('/^[^!<>;?=+#"°{}_$%]*$/ui', $list);
    }
}

/**
 * Check for an integer validity (unsigned)
 * @param $value
 * @return bool
 */
if (!function_exists('isUnsignedInt')) {
    function isUnsignedInt($value)
    {
        return (isInt($value) AND $value < 4294967296 AND $value >= 0);
    }
}

/**
 * Check for an integer validity (unsigned)
 * @param $id
 * @return bool
 */
if (!function_exists('isUnsignedId')) {
    function isUnsignedId($id)
    {
        return isUnsignedInt($id); /* Because an id could be equal to zero when there is no association */
    }
}

if (!function_exists('isNullOrUnsignedId')) {
    function isNullOrUnsignedId($id)
    {
        return is_null($id) OR isUnsignedId($id);
    }
}

/**
 * Check object validity
 * @param $object
 * @return bool
 */
if (!function_exists('isLoadedObject')) {
    function isLoadedObject($object)
    {
        return is_object($object) AND $object->id;
    }
}

/**
 * Check object validity
 *
 * @param $color
 * @return bool Validity is ok or not
 * @internal param int $object Object to validate
 */
if (!function_exists('isColor')) {
    function isColor($color)
    {
        return preg_match('/^(#[0-9A-Fa-f]{6}|[[:alnum:]]*)$/ui', $color);
    }
}

/**
 * Check object validity
 *
 * @param $url
 * @return bool Validity is ok or not
 * @internal param int $object Object to validate
 */
if (!function_exists('isAbsoluteUrl')) {
    function isAbsoluteUrl($url)
    {
        if (!empty($url))
            return preg_match('/^https?:\/\/([[:alnum:]]|[:#%&_=\(\)\.\? \+\-@\/])+$/ui', $url);
        return true;
    }
}

/**
 * Check for standard name file validity
 *
 * @param string $name Name to validate
 * @return boolean Validity is ok or not
 */
if (!function_exists('isFileName')) {
    function isFileName($name)
    {
        return preg_match('/^[a-z0-9_.-]*$/ui', $name);
    }
}

/**
 * Check for admin panel tab name validity
 *
 * @param string $name Name to validate
 * @return boolean Validity is ok or not
 */
if (!function_exists('isTabName')) {
    function isTabName($name)
    {
        return preg_match('/^[a-z0-9_-]*$/ui', $name);
    }
}

if (!function_exists('isWeightUnit')) {
    function isWeightUnit($unit)
    {
        return preg_match('/^[[:alpha:]]{1,3}$/ui', $unit);
    }
}

if (!function_exists('isProtocol')) {
    function isProtocol($protocol)
    {
        return preg_match('/^http(s?):\/\/$/ui', $protocol);
    }
}


if (!function_exists('isSubDomainName')) {
    function isSubDomainName($subDomainName)
    {
        return preg_match('/^[[:alnum:]]*$/ui', $subDomainName);
    }
}

if (!function_exists('isVoucherDescription')) {
    function isVoucherDescription($text)
    {
        return preg_match('/^([^<>{}]|<br \/>)*$/ui', $text);
    }
}

/**
 * Check if the char values is a granularity value
 *
 * @param char $value
 * @return boolean Validity is ok or not
 */
if (!function_exists('isGranularityValue')) {
    function isGranularityValue($value)
    {
        return (!is_null($value) AND ($value === 'd' OR $value === 'm' OR $value === 'y'));
    }
}

/**
 * Check if the value is a sort direction value (DESC/ASC)
 *
 * @param char $value
 * @return boolean Validity is ok or not
 */
if (!function_exists('IsSortDirection')) {
    function IsSortDirection($value)
    {
        return (!is_null($value) AND ($value === 'ASC' OR $value === 'DESC'));
    }
}

/**
 * Customization fields' label validity
 *
 * @param $label
 * @return bool Validity is ok or not
 * @internal param int $object Object to validate
 */
if (!function_exists('isLabel')) {
    function isLabel($label)
    {
        return (preg_match('/^[^{}<>]*$/ui', $label));
    }
}

/**
 * Price display method validity
 *
 * @param integer $data Data to validate
 * @return boolean Validity is ok or not
 */
if (!function_exists('isPriceDisplayMethod')) {
    function isPriceDisplayMethod($data)
    {
        return ($data == PS_TAX_EXC OR $data == PS_TAX_INC);
    }
}

/**
 * Check for Dni validity
 *
 * @param string $dni to validate
 * @return int
 */
if (!function_exists('isDni')) {
    function isDni($dni)
    {
        /*
        Return code:
        1 : It's Ok
        0 : Bad format for DNI
        -1 : DNI duplicate
        -2 : NIF error
        -3 : CIF error
        -4 : NIE error
        */

        if (!$dni)
            return 1;

        $dni = strtoupper($dni);
        if (!preg_match('/((^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$|^[T]{1}[A-Z0-9]{8}$)|^[0-9]{8}[A-Z]{1}$)/', $dni))
            return 0;

        $result = Db::getInstance()->getValue('
		SELECT COUNT(`id_customer`) AS total
		FROM `pd_moarefi_customer`
		WHERE `dni` = \'' . pSQL($dni) . '\'
		');
        if ($result)
            return -1;

        for ($i = 0; $i < 9; $i++)
            $char[$i] = substr($dni, $i, 1);
        // 12345678T
        if (preg_match('/(^[0-9]{8}[A-Z]{1}$)/', $dni))
            if ($char[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($dni, 0, 8) % 23, 1))
                return 1;
            else
                return -2;

        $sum = $char[2] + $char[4] + $char[6];
        for ($i = 1; $i < 8; $i += 2)
            $sum += substr((2 * $char[$i]), 0, 1) + substr((2 * $char[$i]), 1, 1);

        $n = 10 - substr($sum, strlen($sum) - 1, 1);

        if (preg_match('/^[KLM]{1}/', $dni))
            if ($char[8] == chr(64 + $n))
                return 1;
            else
                return -2;

        if (preg_match('/^[ABCDEFGHJNPQRSUVW]{1}/', $dni))
            if ($char[8] == chr(64 + $n) || $char[8] == substr($n, strlen($n) - 1, 1))
                return 1;
            else
                return -3;

        if (preg_match('/^[T]{1}/', $dni))
            if ($char[8] == preg_match('/^[T]{1}[A-Z0-9]{8}$/', $dni))
                return 1;
            else
                return -4;

        if (preg_match('/^[XYZ]{1}/', $dni))
            if ($char[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr(str_replace(array('X', 'Y', 'Z'), array('0', '1', '2'), $dni), 0, 8) % 23, 1))
                return 1;
            else
                return -4;

        return 0;
    }
}

/**
 * Check if $data is a PrestaShop cookie object
 *
 * @param mixed $data to validate
 * @return bool
 */
if (!function_exists('isCookie')) {
    function isCookie($data)
    {
        return (is_object($data) AND get_class($data) == 'Cookie');
    }
}

/**
 *  is file upload
 *
 * @param $post_name
 * @param bool $edit_mode_id
 * @param bool $edit_path
 * @param bool $custom_message
 * @return bool Validity is ok or not
 * @internal param string $data Data to validate
 */
if (!function_exists('is_file_upload')) {
    function is_file_upload($post_name, $edit_mode_id = FALSE, $edit_path = FALSE, $custom_message = FALSE)
    {
        if (intval($edit_mode_id) && $edit_path) {
            $pic_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . (substr($edit_path, 0, 1) == '@' ? 'PicturesAndMovies/' . substr($edit_path, 1) : $edit_path) . '/' . intval($edit_mode_id) . '*.*';
            if (glob($pic_path)) {
                return FALSE;
            }
        }
        if (!Tools::getValue($post_name))
            return ($custom_message ? $custom_message : 'متاسفانه هیچ فایلی آپلود نشده است!');
        return FALSE;
    }
}
/**
 * mesl isGenericName ast ba in tafavot ke max 64 charecter dare va hamintor mitone khali bashe
 * @param $search
 * @return int
 */
if (!function_exists('isValidSearch')) {
    function isValidSearch($search)
    {
        return preg_match('/^[^<>;=#{}]{0,64}$/ui', $search);
    }
}

if (!function_exists('isTableOrIdentifier')) {
    //#####
    function isTableOrIdentifier($table)
    {
        return preg_match('/^[a-z0-9_-]+$/ui', $table);
    }
}


?>
