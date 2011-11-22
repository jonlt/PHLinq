PHLinq (flink)
==============

My modest go at getting a little bit of a LINQy feel to PHP.

Examples:

    class Person {
        public $name, $age;
        public function __construct($name, $age) {
            $this->name = $name;
            $this->age = $age;
        }
    }

    $persons = array(
        new Person("peter", 10),
        new Person("jane",  14),
        new Person("alan",  42),
        new Person("lis",   23),
        new Person("jimmy", 19),
        new Person("ruth",   7)
    );


    // names of persons above 18 ordered alphabetically
    $names = phlinq($persons)
        ->where(function($p) { return $p->age > 18; })
        ->select(function($p) { return $p->name; })
        ->order()
        ;

    var_dump($names->toArray());
    /*
    array(3) {
      [0]=>
      string(4) "alan"
      [1]=>
      string(5) "jimmy"
      [2]=>
      string(3) "lis"
    } 
    */


    // same names but ordered by age (youngest first)
    $names = phlinq($persons)
        ->where(function($p) { return $p->age > 18; })
        ->orderBy(function($p) { return $p->age; })
        ->select(function($p) { return $p->name; })
        ;

    var_dump($names->toArray());

    /*
    array(3) {
      [0]=>
      string(5) "jimmy"
      [1]=>
      string(3) "lis"
      [2]=>
      string(4) "alan"
    }
    */


Available Methods:
    toArray - convert to array()
    count - count the elements in the result
    firstOrNull - get the first element or null if none is present
    first - get the first element, throws exception on failure to find any
    lastOrNull - get last element or null if none is present
    last - get last element, throws exception on failure to find any
    singleOrNull - get a single element based on a condition or null if no element mathces
    single - get a single element based on a condition, throws an exception on failure to find any
    where - filter elements based on condition
    select - select values from the elements
    order - order the elements
    orderBy - order the elements by a given value
    orderDesc - like order but, descending
    orderByDesc - like orderBy, but descending
