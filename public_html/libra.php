<?php

error_reporting(E_ERROR);

// Define a constante com o caminho para o diretório da aplicação
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));


// Define o ambiente da aplicação (teste / libra/ local / dreamhost)
$env = preg_match('/^[a-zA-Z0-9-\.]+(\.libra)$/', $_SERVER['SERVER_NAME']) ? 'libra' : 'remote';
// essa variavel vai dizer qual bloco de configurações será utilizado no arquivo ../application/configs/application.ini
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : $env));

// Adiciona as bibliotecas no include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../../../library/zend1.11.6'),
    realpath(APPLICATION_PATH . '/../../../library/LibraryC3.1'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// CRIA A APLICAÇÃO, BOOTSTRAP, E EXECUTA
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH.'/configs/application.ini');

$application->bootstrap()->run();
