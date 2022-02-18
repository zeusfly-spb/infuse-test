<?php

header ('content-type:image/jpeg');
readfile('logo.jpg');

function connect ()
{
  $host = 'mariadb';
  $db_name = 'test1';
  $db_user = 'root';
  $db_pass = 'root';
  $connection = new mysqli($host, $db_user, $db_pass, $db_name);
  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
  } else {
    return $connection;
  }
}

function exists ($connection)
{
  $format = "SELECT * FROM users WHERE ip_address='%s' AND user_agent='%s' AND page_url='%s'";
  $sql = sprintf($format, ipEncoded(), userAgent(), pageUrl());
  $result = mysqli_query($connection, $sql);
  if ($result == false) {
    return false;
  }
  $res = !empty(mysqli_num_rows($result));
  $result->close();
  return $res;
}

function create ($connection)
{
  $format = "INSERT INTO users (ip_address, user_agent, view_date, page_url, views_count) VALUES (%d, '%s', '%s', '%s', %d)";
  $sql = sprintf($format, ipEncoded(), userAgent(), now(), pageUrl(), 1);
  $result = mysqli_query($connection, $sql);
  if ($result == false) {
    print("Произошла ошибка при выполнении запроса");
  } else {
    $result->close();
  }
}

function update ($connection)
{
  $format = "UPDATE users SET views_count = views_count + 1, view_date = '%s'  WHERE ip_address = %d AND user_agent = '%s' AND page_url = '%s'";
  $sql = sprintf($format, now(), ipEncoded(), userAgent(), pageUrl());
  $result = mysqli_query($connection, $sql);
  if ($result == false) {
    print("Произошла ошибка при выполнении запроса");
  } else {
    $result->close();
  }
}

function  ipEncoded ()
{
  return ip2long($_SERVER['REMOTE_ADDR']);
}

function userAgent ()
{
  return $_SERVER['HTTP_USER_AGENT'];
}

function pageUrl ()
{
  return $_SERVER['HTTP_REFERER'];
}

function now ()
{
  return date('Y-m-d H:i:s');
}


$connection = connect();
if (exists($connection)) {
  update($connection);
} else {
  create($connection);
}
