<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Main
 *
 * @author mkreine
 */
class Main {
    
    /**
     * Версия форума
     * @var string
     */
    protected $forum_version;
    
    /**
     * Требуемая версия PHP для функционирования форума
     * @var string
     */
    protected $required_php_version = '7.0.0';
    
    /**
     * Требуемая версия MySQL для функционирования форума
     * @var string
     */
    protected $required_mysql_version = '5.5.54';
    
    public function __construct() {
        
        $this->forum_version    =   '0.1';
        
    }
}
