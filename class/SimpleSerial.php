<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SimpleSerial
 *
 * @author Massimiliano Palermo <maxx.palermo@gmail.com>
 */
class SimpleSerial {
    private $device;
    private $baudrate;
    private $bitdata;
    private $parity;
    private $bitstop;
    
    public function __construct($device,$baudrate,$bitdata,$parity,$bitstop) 
    {
        $this->device=$device;
        $this->baudrate=$baudrate;
        $this->bitdata=$bitdata;
        $this->parity=$parity;
        $this->bitstop=$bitstop;
    }
    
    public function initialize()
    {
        print exec("stty -F " . $this->device . " " . $this->baudrate . " cs" . $this->bitdata . " -cstopb -ixon");
        print exec("stty -a");
    }
    
    public function msg($message)
    {
        print exec("echo " . $message . " > " . $this->device);
    }
}
