{
  config,
  pkgs,
  ...
}: let
  appName = "symshop";
in {
  packages = [ pkgs.symfony-cli ];

  languages.php = {
    enable = true;
    version = "8.3";
    extensions = ["pcov"];
    ini = ''
      memory_limit = 128M
      display_errors = On
      error_reporting = E_ALL
      xdebug.mode = debug
      xdebug.discover_client_host = 1
      xdebug.client_host = localhost
    '';
    fpm.pools.${appName}.settings = {
      "pm" = "dynamic";
      "pm.max_children" = 10;
      "pm.start_servers" = 2;
      "pm.min_spare_servers" = 1;
      "pm.max_spare_servers" = 10;
    };
  };

  services = {
    mysql = {
      enable = true;
      package = pkgs.mariadb;
      initialDatabases = [{name = appName;}];
      ensureUsers = [
        {
          name = appName;
          password = appName;
          ensurePermissions = {
            "${appName}.*" = "ALL PRIVILEGES";
          };
        }
      ];
    };

    caddy = {
      enable = true;
      virtualHosts.":8000" = {
        extraConfig = ''
          root * public
          php_fastcgi unix/${config.languages.php.fpm.pools.${appName}.socket}
          file_server
        '';
      };
    };

    adminer = {
      enable = true;
      package = pkgs.adminerevo;
      listen = "localhost:8001";
    };
  };
}
