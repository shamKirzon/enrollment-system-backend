# Enrollment System Back End

> Back end PHP server for the enrollment system.

## Prerequisites

1. [PHP](https://nodejs.org/en)
2. [XAMPP](https://www.apachefriends.org/)
3. [GMail app password](https://www.getmailbird.com/gmail-app-password/)

## Note

You must create a `env.php` file on the root directory. This will contain the code for sending emails through Gmail's SMTP.

```php
<?php
function get_password() {
  $password = "insert app password";

  return $password;
}

function get_from() {
  $from = "insert email where password is from";

  return $from;
}
?>
```

To find out how to create an app password, you can refer [here](https://www.getmailbird.com/gmail-app-password/).
