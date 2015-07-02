<?php
class Zend_View_Helper_Data extends Zend_View_Helper_Abstract
{
    protected $_Data;

    public function Data($data,$formato = "dd/MM/yyyy")
    {
    	if(Zend_Date::isDate($data,"yyyy-MM-dd")):
    		$this->_Data = new Zend_Date($data);
    		$this->_Data = $this->_Data->toString($formato);
    	else:
    		$this->_Data = NULL;
    	endif;

        return $this->_Data;
    }
}
