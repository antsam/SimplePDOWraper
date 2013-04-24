SimplePDOWraper
===============

A simple PHP wrapper for PDO that simplifies the use of prepared statements.

## Example Usage
### Initialize the class
    require_once("/path/to/db.php");
    $db = new db($host, $username, $password, $table);
  
### Query for a row
    $sql = "SELECT `name`, `favourite_colour` 
            FROM `favourite_colours` 
            WHERE `name` = ? 
            LIMIT 1";
    $res = $db->get_row($sql, array("John Smith"));

### Query for a set of rows
    $sql = "SELECT `name`, `favourite_colour` 
            FROM `favourite_colours` 
            WHERE `favourite_colour` = ?";
    
    $res = $this->db->get_array($sql, array("blue"));

### Query with multiple parameters
    $sql = "SELECT `name`, `favourite_colour` 
            FROM `favourite_colours` 
            WHERE `favourite_colour` = ? 
              AND `name` = ?";
    
    $res = $this->db->get_array($sql, array("blue", "John Smith"));
