<html>
<body>

<?php

  putenv("ORACLE_HOME=/u01/app/oracle/product/11.2.0/xe/");

  $conn = oci_connect("timmy", "timmy", "xe")
     or die("<br>Couldn't connect");

  $query = "select user from dual";

  $stmt = oci_parse($conn, $query);
  oci_define_by_name($stmt, "USER", $u);

  oci_execute($stmt);
  oci_fetch($stmt);

  echo "The user is: $u";

  oci_close($conn);
?>

</body>
