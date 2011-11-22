<?php

/**
 * Iterator used for ORDER clauses
 */
class PHLinq_Iterators_Order implements Iterator {
    
    private $_iterator;
    private $_sorted = false;
    private $_selector;
    private $_asc;
    
    public function __construct($selector, Iterator $iterator, $asc = true) {
        $this->_iterator = $iterator;
        $this->_selector = $selector;
        $this->_asc = $asc;
    }

    public function current() {
        return $this->_iterator->current();
    }

    public function key() {
        return $this->_iterator->key();
    }

    public function next() {
        if(!$this->_sorted) {
            $this->_sort();
        }
        $this->_iterator->next();
    }

    public function rewind() {
        if(!$this->_sorted) {
            $this->_sort();
        }
        $this->_iterator->rewind();
    }

    public function valid() {
        return $this->_iterator->valid();
    }
    
    private function _sort() {
        $this->_sorted = true;
        
        $arr = new ArrayObject();
        foreach($this->_iterator as $elem) {
            $arr->append($elem);
        }

        $asc = $this->_asc;
        
        if(isset($this->_selector)) {
            $selector = $this->_selector;
            $arr->uasort(function($a,$b) use ($selector, $asc)
            {
                $x = call_user_func($selector, $a);
                $y = call_user_func($selector, $b);
                
                if(!isset($x) || !isset($y)){
                    throw new Exception("selector in orderBy clause did not return a value");
                }
                
                return ($asc)
                    ? $x > $y
                    : $x < $y;
            });
        } else {
            $arr->uasort(function($x,$y) use($asc)
            {
                return ($asc)
                    ? $x > $y
                    : $x < $y;
            });            
        }
            
        $this->_iterator = phlinq($arr)->getIterator();;
        
        $this->rewind();
    }
    
}
