<?php

/**
 * Iterator used for WHERE clauses
 */
class PHLinq_Iterators_Where implements Iterator {
    private $_condition;
    private $_iterator;
    private $_position = 0;
    
    public function __construct($condition, Iterator $iterator) {
        $this->_condition = $condition;
        $this->_iterator = $iterator;
    }

    public function current() {
        return $this->_iterator->current();
    }

    public function key() {
        return $this->_position;
    }

    public function next() {
        $this->_position++;
        $this->_iterator->next();
        
        if(!$this->_iterator->valid()) {
            return;
        }
        
        if(!$this->_isSatisfactory()) {
            $this->next();
        }
    }

    public function rewind() {
        $this->_position = 0;
        $this->_iterator->rewind();
        
        if(!$this->_isSatisfactory()) {
            $this->next();
        }        
    }

    public function valid() {
        return $this->_iterator->valid();
    }
    
    private function _isSatisfactory() {
        $result = call_user_func($this->_condition, $this->_iterator->current());
        if(!isset($result) || !is_bool($result)){
            throw new Exception("where clause did not evaluate to bool");
        }
        return $result;
        
    }
}
