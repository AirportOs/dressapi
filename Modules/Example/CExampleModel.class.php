<?php
/**
 * 
 * DressAPI
 * @version 1.0
 * @license This file is under Apache 2.0 license
 * @author Tufano Pasquale
 * @copyright Tufano Pasquale
 * @url https://dressapi.com
 * @year 2021
 * 
 * Example of a Custom Model
 */

namespace DressApi\Modules\Example;

use DressApi\Core\Cache\CFileCache as CCache;
use DressApi\Core\User\CUser;
use Exception;

class CExampleModel extends \DressApi\Modules\Base\CBaseModel
{
    public const REGEX_CF = '/^[A-Z]{6}\d{2}[ABCDEHLMPRST]{1}\d{2}[A-Z]{1}[\d]{3}[A-Z]{1}$/';

    /**
     * @param $table $table current table name
     * @param array $all_tables list of all tables of current DB with column_list list
     * @param ?CUser $user object that manages user data
     * @param ?CCache $cache object that manages cached data
     *
     */
    public function __construct( string $table, array $all_tables, ?CUser $user = null, ?CCache $cache )
    {
        parent::__construct($table, $all_tables, $user, $cache);

        // Other useful method:
        // public function getAdditionalConditions() : string
        //
        // public function setRequiredInt($item, $min = null, $max = null);
        // public function setRequiredFloat($item, $min = null, $max = null);
        // public function setRule($item, $pattern);
        // public function setRequired($item, bool $required, $min = null, $max = null);
        // public function setRequiredPattern($item, $pattern, $options = null);


        // Italian fiscal code (with predefined regular expression as a rule )
        // setRequiredPattern(string $field_name, string $pattern, string $options = null)
        $this->setRequiredPattern('cf', self::REGEX_CF);
    }


    /**
     *
     * Return a list of column of current table
     *
     * @return array list of column names of current table
     */
    public function getListItems(): array
    {
        return ['id', 'id_user', 'name'];
    }
} // end class
