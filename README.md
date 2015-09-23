# spoof
Simple PHP Object Oriented Framework consists of convenient and simple, yet advanced database abstraction layer. It's simple to get started and use, but also packs advanced features.

Installation
------------
In your `composer.json` make sure you have this repository:
```
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/unix1/spoof"
        }
    ]
```
and this package:
```
    "require": {
        "spoof/spoof": "~0.5"
    }
```

Basic Usage
-----------

### connection
Create a PDO connection and add to connection pool with alias `test`:
```php
use \spoof\lib360\db\connection as conn;

$conn = new conn\PDO(new conn\Config('mysql:host=localhost;dbname=test', 'root', NULL));
conn\Pool::add($conn, 'test');
```

### select using table factory
This method allows very quick, one-off access to your data.
```php
use \spoof\lib360\db\data\TableFactory;

$userTable = TableFactory::get('test', 'users');
$result = $userTable->select();
```

### select using a table class
Defining a table class allows you to define a reusable component instead of using defaults from factory. Below $db is the database alias name, and $name is the table or storage name.
```php
use \spoof\lib360\db\data\Table

class UserTable extends Table
{
	protected $db = 'test';
	protected $name = 'users';
}

$userTable = new UserTable();
$result = $userTable->select();
```

### create conditions
Condition objects allow you to define a query restriction that can be reused. The condition object below would read: column user_id equals to integer 5.
```php
use \spoof\lib360\db\condition\Condition;
use \spoof\lib360\db\value\Value;

$cond = new Condition(
	new Value('user_id', Value::TYPE_COLUMN),
	Condition::OPERATOR_EQUALS,
	new Value(5, Value::TYPE_INTEGER)
);
```

### use condition groups
Condition groups allow you to combine conditions and other condition groups together.
```php
use \spoof\lib360\db\condition\ConditionGroup;

$condgroup1 = new ConditionGroup($cond1);
$condgroup1->addCondition(ConditionGroup::OPERATOR_AND, $cond2);

$condgroup1->addCondition(ConditionGroup::OPERATOR_OR, $condgroup2);
```

### use conditions in queries
Condition (or condition group) objects can be easily plugged in select/update/delete statements.
```php
$result = $userTable->delete($condgroup1);
```

### limit returned fields per query
This will select id and name fields only.
```php
$result = $userTable->select($condgroup1, NULL, array('id', 'name'));
```

### limit fields in class definition
This is equivalent of above but defined inside the class as default. You can always override per query.
```php
class UserTable extends data\Table
{
	protected $db = 'test';
	protected $name = 'user';
	protected $fields = array('id', 'name');
}

$userTable = new UserTable();
$result = $userTable->select($condgroup1);
```

Advanced Usage
--------------

### create joins
Let's say you want to display comments made by users; but comments table has the users' ID value, so you want to join against the users table to grab name instead. And you also want to join users table against user_groups to grab their group name.
```php
use \spoof\lib360\db\value\Value;
use \spoof\lib360\db\condition\Condition;
use \spoof\lib360\db\join\Join;

// create condition object linking comments.user_id = users.user_id
$cond12 = new Condition(
	new Value('comments.user_id', Value::TYPE_COLUMN),
	Condition::OPERATOR_EQUALS,
	new Value('users.user_id', Value::TYPE_COLUMN)
);

// create a condition object linking users.group_id = user_group.group_id
$cond23 = new Condition(
	new Value('users.group_id', Value::TYPE_COLUMN),
	Condition::OPERATOR_EQUALS,
	new Value('user_group.group_id', Value::TYPE_COLUMN)
);

// create a join object with initial inner join of users and comments tables
$j = new Join('comments', Join::JOIN_TYPE_INNER, 'users', $cond12);

// add user_group table as a left outer join
$j->addTable(Join::JOIN_TYPE_LEFT_OUTER, 'user_group', $cond23);
```

### create views
Now use the above join inside a view class. Notice you can add as many join objects as desired.
```php

use \spoof\lib360\db\data\View;

class UserCommentsView extends data\View
{
	public function __construct()
	{
		// same join creation code as above resulting in $j object
		// ...

		// add join to array
		$this->joins[] = $j;
	}
}
```

### use views
Views can be used very similar to tables. In fact, they share most of the implementation.
```php
$userComments = new UserCommentsView();

$result = $userComments->select();
```

There's a lot more! User guides and full documentation are coming soon.
