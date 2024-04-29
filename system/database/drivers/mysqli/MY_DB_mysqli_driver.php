// application/core/MY_DB_mysqli_driver.php
<?php
class MY_DB_mysqli_driver extends CI_DB_mysqli_driver {

    public function query($sql, $binds = FALSE, $return_object = TRUE) {
        try {
            return parent::query($sql, $binds, $return_object);
        } catch (Exception $e) {
            // Log or handle the exception
            throw $e;
        }
    }
}