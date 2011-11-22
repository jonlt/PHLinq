<?php

require_once(__DIR__."/iterators/Where.php");
require_once(__DIR__."/iterators/Select.php");
require_once(__DIR__."/iterators/Order.php");

/**
 * Collection class
 */
class PHLinq implements IteratorAggregate {
    
    private $_iterator;
    
    public function __construct(Iterator $iterator) {
        $this->_iterator = $iterator;
    }
    
    /**
     * @return Iterator 
     */
    public function getIterator() {
        return $this->_iterator;
    }
    
    /**
     * Convert the collection to an array
     * @param bool $deep whether or not the conversion should be recursive
     * @return array 
     */
    public function toArray($deep = true) {
        $arr = array();
        foreach($this->_iterator as $elem) {
            if($deep && $elem instanceof PHLinq) $elem = $elem->toArray();
            $arr[] = $elem;
        }
        return $arr;
    }
    
    /**
     * count the number of elements in the collection
     * @return int
     */
    public function count(){
        return count($this->toArray());
    }

    /**
     * returns a the first element in the collection
     * @param function $condition
     * @return elementType 
     */       
    public function firstOrNull($condition = null) {
        $elem = null;
        if(isset($condition)){
            $where = $this->where($condition);
            $where->_iterator->rewind();
            $elem = $where->_iterator->current();
        } else {
            $this->_iterator->rewind();
            $elem = $this->_iterator->current();
        }
        return $elem;
    }    
    
    /**
     * returns a the first element in the collection, throws exceptions on failure to find the element
     * @param function $condition
     * @return elementType 
     */      
    public function first($condition = null) {
        $elem = $this->firstOrNull($condition);
        if(!isset($elem)) throw new Exception("sequence did not contain any elements");
        return $elem;
    }
    
    /**
     * returns a the last element in the collection
     * @param function $condition
     * @return elementType 
     */      
    public function lastOrNull($condition = null) {
        $result = null;
        $arr = ($condition) ? $this->where($condition) : $this;
        foreach ($arr as $value) {
            $result = $value;
        }
        return $result;
    }
    
    /**
     * returns a the last element in the collection, throws exceptions on failure to find the element
     * @param function $condition
     * @return elementType 
     */    
    public function last($condition = null) {
        $result = $this->lastOrNull($condition);
        if(!isset($result)) throw new Exception("sequence did not contain any elements");
        return $result;
    }
    
    /**
     * * returns a single element from the collection, throws exceptions if the collection contains multiple mathces
     * @param function $condition
     * @return elementType 
     */
    public function singleOrNull($condition){
        $where = $this->where($condition);
        $count = $where->count();
        if($count > 1) throw new Exception("sequence contains more than one element");
        if($count == 0) return null;
        return $where->_iterator->current();
    }    
    
    /**
     * returns a single element from the collection, throws exceptions upon failure to find the element or if the collection contains multiple mathces
     * @param function $condition
     * @return elementType 
     */
    public function single($condition){
        $elem = $this->singleOrNull($condition);
        if(!isset($elem)) throw new Exception("sequence did not contain any elements");
        return $elem;
    }

    /**
     * find elements in the collection that satisfies the given condition
     * @param function $condition
     * @return PHLinq 
     */
    public function where($condition) {
        return new PHLinq(new PHLinq_Iterators_Where($condition, $this->_iterator));
    }
    
    /**
     * select values in the collection based on the given selector
     * @param function $selector
     * @return PHLinq 
     */
    public function select($selector) {
        return new PHLinq(new PHLinq_Iterators_Select($selector, $this->_iterator));
    }
    
    /**
     * order the elements in the collection
     * @return PHLinq 
     */
    public function order() {
        return $this->orderBy(null);
    }
    
    /**
     * order the elements in the collection, using the value specified by the selector to sort
     * @param function $orderer
     * @return PHLinq 
     */
    public function orderBy($orderer) {
        return new PHLinq(new PHLinq_Iterators_Order($orderer, $this->_iterator));
    }
    
    /**
     * order the elements in the collection
     * @return PHLinq 
     */
    public function orderDesc() {
        return $this->orderByDesc(null);
    }
    
    /**
     * order the elements in the collection, using the value specified by the selector to sort
     * @param function $orderer
     * @return PHLinq 
     */
    public function orderByDesc($orderer) {
        return new PHLinq(new PHLinq_Iterators_Order($orderer, $this->_iterator, false));
    }        
}

/**
 * Wrapper function
 * @return PHLinq 
 */
function phlinq($collection) {
    
    if($collection instanceof IteratorAggregate)
        return new PHLinq($collection->getIterator());
    else if($collection instanceof Iterator)
        return new PHLinq($collection);
    else if(is_array($collection)){
        return phlinq(new ArrayObject($collection));
    }
    else
        throw new Exception("phlinq only accepts an array, an IteratorAggregate or an Iterator");
}
