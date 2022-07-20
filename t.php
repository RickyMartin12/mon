<?php

include('Net/SSH2.php');

$ssh = new Net_SSH2('94.126.144.9', 50022);
if (!$ssh->login('lazer', 'RuiPereira+1')) {
    exit('Login Failed');
}
//mysqldump --password=lazerx0! --user=system --port=3307 --host=localhost tes_mon | mysql --password=lazerx0! --user=system example
//echo $ssh->exec('pwd');
echo $ssh->exec('php -v');

?>