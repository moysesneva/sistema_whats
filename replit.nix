{pkgs}: {
  deps = [
    pkgs.php82Extensions.gd
    pkgs.php82Extensions.curl
    pkgs.php82Extensions.mbstring
    pkgs.php82Extensions.pdo_mysql
    pkgs.php82Extensions.mysqli
    pkgs.mysql80
    pkgs.unzip
  ];
}
