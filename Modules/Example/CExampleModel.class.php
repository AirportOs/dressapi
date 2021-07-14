<?php
/**
 * 
 * DressAPI
 * @version 1.0
 * @license This file is under Apache 2.0 license
 * @author Tufano Pasquale
 * @copyright Tufano Pasquale
 * @url https://dressapi.com
 * 
 * Example of a Custom Model
 */

namespace DressApi\Modules\Example;

use Exception;

class CExampleModel extends \DressApi\Modules\Base\CBaseModel
{
    public const REGEX_CF = '/^[A-Z]{6}\d{2}[ABCDEHLMPRST]{1}\d{2}[A-Z]{1}[\d]{3}[A-Z]{1}$/';

    /**
     * 
     * Method __construct
     *
     * @param $table $table [explicite description]
     * @param $column_list $column_list [explicite description]
     *
     * @return void
     */
    public function __construct(string $table, array|null $column_list)
    {
        parent::__construct($table, $column_list);

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
