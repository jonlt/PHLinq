<?php

/**
 * Iterator used for SELECT clauses
 */
class PHLinq_Iterators_Select implements Iterator {
    
    private $_selector;
    private $_iterator;
    
    public function __construct($selector, Iterator $iterator) {
        $this->_selector = $selector;
        $this->_iterator = $iterator;
    }

    public function current() {
        return $this->_select();
    }

    public function key() {
        return $this->_iterator->key();
    }

    public function next() {
        $this->_iterator->next();
    }

    public function rewind() {
        $this->_iterator->rewind();
    }

    public function valid() {
        return $this->_iterator->valid();
    }
    
    private function _select() {
        $result = call_user_func($this->_selector, $this->_iterator->current());
        if(!isset($result)){
            throw new Exception("selector in select clause did not return a value");
        }
        return $result;
    }
    
}