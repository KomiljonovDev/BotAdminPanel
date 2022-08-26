<?php
    class Dbconfig {
        protected $serverName;
        protected $userName;
        protected $passCode;
        protected $dbName;
        protected $token;
        function Dbconfig() {
            $this -> serverName = 'localhost:3306';
            $this -> userName = 'okdeveloper';
            $this -> passCode = 'st6BWHM33WjiGfqd';
            $this -> dbName = 'sh_music';
        }
    }
?>
