<?php

namespace src\lib;

use inc\Raise;
use src\lib\RRedis;
use src\lib\Database;

class walletClass extends Database
{
    public function __construct($db = 'db')
    {
        parent::__construct(Raise::params()[$db]);
        $this->rds = new RRedis();
        $this->skp = 0;
    }

    /**
     * Public Funtion to Get Balance
     * @param String $walletName
     * @param INT $userId
     * @return Decimal $balance
     */
    public function getBalance($walletName, $userId)
    {
        $query = "SELECT $walletName FROM user_wallet WHERE user_id=$userId";

        $balance = $this->callSql($query, 'value');

        return $balance;
    }

    /**
     * Public Funtion to Validate Wallet have enough balance
     * @param String $walletName
     * @param INT $userId
     * @param DECIMAL $compareAmount
     * @return Boolean $result 1-Enough Balance 2-Didnt have enough balance
     */
    public function checkBalance($walletName, $userId, $compareAmount)
    {
        $query = "SELECT $walletName FROM user_wallet WHERE user_id=$userId";

        $balance = $this->callSql($query, 'value');

        if ($balance >= $compareAmount)
            $result = 1;
        else
            $result = 0;

        return $result;
    }


    /** 
     * Private Function to update wallet Balance  
     * @param String $walletName
     * @param INT $userId
     * @param Decimal $amount
     * @param Boolean $type 0-Credit 1-Debit
     * @return Array $response
     */
    private function updateBalane($walletName, $userId, $amount, $type)
    {

        $query = 'SELECT ' . $walletName . ' FROM user_wallet WHERE user_id=' . $userId;

        $getBalance = $this->callSql($query, 'value');

        $time = time();
        $ip = getClientIP();


        if ($type == 0) {
            //$this->query('UPDATE user_wallet SET ' . $walletName . '=' . $walletName . '+' . $amount . ' WHERE user_id="' . $userId . '"');
            $this->query('UPDATE user_wallet SET ' . $walletName . '=' . $walletName . '+' . $amount . ' ,updated_at = "'.$time.'" ,updated_ip = "'.$ip.'" WHERE user_id="' . $userId . '"');
            $this->execute();
        } else {
            if ($getBalance > $amount) {
                
                
                
                //$this->callSql('UPDATE user_wallet SET ' . $walletName . '=' . $walletName . '-' . $amount . ' WHERE user_id="' . $userId . '"');
                $this->query('UPDATE user_wallet SET ' . $walletName . '=' . $walletName . '-' . $amount . ' ,updated_at = "'.$time.'" ,updated_ip = "'.$ip.'" WHERE user_id="' . $userId . '"');
                $this->execute();

            } else {

                //$this->callSql('UPDATE user_wallet SET ' . $walletName . '=0 WHERE user_id="' . $userId . '"');
                $this->query('UPDATE user_wallet SET ' . $walletName . '=0 ,updated_at = "'.$time.'" ,updated_ip = "'.$ip.'" WHERE user_id="' . $userId . '"');
                $this->execute();
            }
       } 
        


        $newBal = $this->callSql('SELECT ' . $walletName . ' FROM user_wallet WHERE user_id="' . $userId . '"', 'value');

        
        return array($getBalance, $newBal);
    }


    /** 
     * Private Function to update wallet Balance integrated with Redis to increase performance
     * For Testing Purpose
     * @param String $walletName
     * @param INT $userId
     * @param Decimal $amount
     * @param Boolean $type 0-Credit 1-Debit
     * @return Array $response
     */
    private function updateBalaneOptimized($walletName, $userId, $amount, $type)
    {

        $key = $walletName . '_' . $userId;

        $getBalance = $this->rds->get($key);

        if (empty($getBalance)) {
            $query = 'SELECT ' . $walletName . ' FROM user_wallet WHERE user_id=' . $userId;

            $getBalance = $this->callSql($query, 'value');
        }

        if ($type == 0) {
            $this->callSql('UPDATE user_wallet SET ' . $walletName . '=' . $walletName . '+' . $amount . ' WHERE user_id="' . $userId . '"');
            $newBal = $getBalance + $amount;
        } else {
            if ($getBalance > $amount) {
                $this->callSql('UPDATE user_wallet SET ' . $walletName . '=' . $walletName . '-' . $amount . ' WHERE user_id="' . $userId . '"');

                $newBal = $getBalance - $amount;
            } else {
                $this->callSql('UPDATE user_wallet SET ' . $walletName . '=0 WHERE user_id="' . $userId . '"');

                $newBal = 0;
            }
        }

        //$newBal = $this->callSql('SELECT ' . $walletName . ' FROM user_wallet WHERE user_id="' . $userId . '"', 'value');

        $this->rds->set($key, $newBal, 60); //New Balance Updated to Redis

        return array($getBalance, $newBal);
    }


    /** Update USD Wallet Balance
     * @param INT $userId
     * @param Boolean $creditType 0-Credit 1-Debit (Default Debit)
     * @param INT $transType
     * @param Decimal $amount
     * @param INT $doneBy
     * @return 1
     */
    public function updateUSDWallet($userId, $creditType = 1, $transType, $amount, $doneBy = 0)
    {
        $response = $this->updateBalane('usd_wallet', $userId, $amount, $creditType);

        $preBal = $response[0];
        $newBal = $response[1];

        $time = time();
        $date = date('Y-m-d', $time);

        $ip = $_SERVER['REMOTE_ADDR'];

        $query = 'INSERT INTO `usd_wallet_log` (`user_id`,`credit_type`,`transaction_type`,`value`,`before_bal`,`after_bal`,`createtime`,`createip`,`createid`,`transactiondate`) VALUES
                    (:user_id,:credit_type,:transaction_type,:amount,:before_bal,:after_bal,:createtime,:createip,:createid,:transactiondate)';


        $this->query($query);
        $this->bind(':user_id', $userId);
        $this->bind(':credit_type', $creditType);
        $this->bind(':transaction_type', $transType);
        $this->bind(':amount', $amount);
        $this->bind(':before_bal', $preBal);
        $this->bind(':after_bal', $newBal);
        $this->bind(':createtime', $time);
        $this->bind(':createip', $ip);
        $this->bind(':createid', $doneBy);
        $this->bind(':transactiondate', $date);

        $this->execute();

        return $newBal;
    }

    /** Update USDT Coin Wallet Balance
     * @param INT $userId
     * @param Boolean $creditType 0-Credit 1-Debit (Default Debit)
     * @param INT $transType
     * @param Decimal $amount
     * @param INT $doneBy
     * @return 1
     */
    public function updateUSDTCoinWallet($userId, $creditType = 1, $transType, $amount, $doneBy = 0)
    {
        $response = $this->updateBalane('usdt_wallet', $userId, $amount, $creditType);

        $preBal = $response[0];
        $newBal = $response[1];

        $time = time();
        $date = date('Y-m-d', $time);

        $ip = $_SERVER['REMOTE_ADDR'];

        $query = 'INSERT INTO `usdt_wallet_log` (`user_id`,`credit_type`,`transaction_type`,`value`,`before_bal`,`after_bal`,`createtime`,`createip`,`createid`,`transactiondate`) VALUES
                    (:user_id,:credit_type,:transaction_type,:amount,:before_bal,:after_bal,:createtime,:createip,:createid,:transactiondate)';


        $this->query($query);
        $this->bind(':user_id', $userId);
        $this->bind(':credit_type', $creditType);
        $this->bind(':transaction_type', $transType);
        $this->bind(':amount', $amount);
        $this->bind(':before_bal', $preBal);
        $this->bind(':after_bal', $newBal);
        $this->bind(':createtime', $time);
        $this->bind(':createip', $ip);
        $this->bind(':createid', $doneBy);
        $this->bind(':transactiondate', $date);

        $this->execute();

        return $newBal;
    }

    /** Update Life Style Wallet Balance
     * @param INT $userId
     * @param Boolean $creditType 0-Credit 1-Debit (Default Debit)
     * @param INT $transType
     * @param Decimal $amount
     * @param INT $doneBy
     * @return 1
     */
    public function updateLifeStyleWallet($userId, $creditType = 1, $transType, $amount, $doneBy = 0)
    {
        $response = $this->updateBalane('lifestyle_wallet', $userId, $amount, $creditType);

        $preBal = $response[0];
        $newBal = $response[1];

        $time = time();
        $date = date('Y-m-d', $time);

        $ip = $_SERVER['REMOTE_ADDR'];

        $query = 'INSERT INTO `lifestyle_wallet_log` (`user_id`,`credit_type`,`transaction_type`,`value`,`before_bal`,`after_bal`,`createtime`,`createip`,`createid`,`transactiondate`) VALUES
                    (:user_id,:credit_type,:transaction_type,:amount,:before_bal,:after_bal,:createtime,:createip,:createid,:transactiondate)';


        $this->query($query);
        $this->bind(':user_id', $userId);
        $this->bind(':credit_type', $creditType);
        $this->bind(':transaction_type', $transType);
        $this->bind(':amount', $amount);
        $this->bind(':before_bal', $preBal);
        $this->bind(':after_bal', $newBal);
        $this->bind(':createtime', $time);
        $this->bind(':createip', $ip);
        $this->bind(':createid', $doneBy);
        $this->bind(':transactiondate', $date);

        $this->execute();

        return $newBal;
    }

    /** Update JACredit Wallet Balance
     * @param INT $userId
     * @param Boolean $creditType 0-Credit 1-Debit (Default Debit)
     * @param INT $transType
     * @param Decimal $amount
     * @param INT $doneBy
     * @return 1
     */
    public function updateJACreditWallet($userId, $creditType = 1, $transType, $amount, $doneBy = 0)
    {
        $response = $this->updateBalane('jacredit_wallet', $userId, $amount, $creditType);

        $preBal = $response[0];
        $newBal = $response[1];

        $time = time();
        $date = date('Y-m-d', $time);

        $ip = $_SERVER['REMOTE_ADDR'];

        $query = 'INSERT INTO `jacredit_wallet_log` (`user_id`,`credit_type`,`transaction_type`,`value`,`before_bal`,`after_bal`,`createtime`,`createip`,`createid`,`transactiondate`) VALUES
                    (:user_id,:credit_type,:transaction_type,:amount,:before_bal,:after_bal,:createtime,:createip,:createid,:transactiondate)';


        $this->query($query);
        $this->bind(':user_id', $userId);
        $this->bind(':credit_type', $creditType);
        $this->bind(':transaction_type', $transType);
        $this->bind(':amount', $amount);
        $this->bind(':before_bal', $preBal);
        $this->bind(':after_bal', $newBal);
        $this->bind(':createtime', $time);
        $this->bind(':createip', $ip);
        $this->bind(':createid', $doneBy);
        $this->bind(':transactiondate', $date);

        $this->execute();

        return $newBal;
    }

    /** Update Income Wallet Balance
     * @param INT $userId
     * @param Boolean $creditType 0-Credit 1-Debit (Default Debit)
     * @param INT $transType
     * @param Decimal $amount
     * @param INT $doneBy
     * @return 1
     */
    public function updateIncomeWallet($userId, $creditType = 1, $transType, $amount, $doneBy = 0)
    {
        $response = $this->updateBalane('income_wallet', $userId, $amount, $creditType);

        $preBal = $response[0];
        $newBal = $response[1];

        $time = time();
        $date = date('Y-m-d', $time);

        $ip = $_SERVER['REMOTE_ADDR'];

        $query = 'INSERT INTO `income_wallet_log` (`user_id`,`credit_type`,`transaction_type`,`value`,`before_bal`,`after_bal`,`createtime`,`createip`,`createid`,`transactiondate`) VALUES
                    (:user_id,:credit_type,:transaction_type,:amount,:before_bal,:after_bal,:createtime,:createip,:createid,:transactiondate)';


        $this->query($query);
        $this->bind(':user_id', $userId);
        $this->bind(':credit_type', $creditType);
        $this->bind(':transaction_type', $transType);
        $this->bind(':amount', $amount);
        $this->bind(':before_bal', $preBal);
        $this->bind(':after_bal', $newBal);
        $this->bind(':createtime', $time);
        $this->bind(':createip', $ip);
        $this->bind(':createid', $doneBy);
        $this->bind(':transactiondate', $date);

        $this->execute();

        return $newBal;
    }

    /** Update Capital Wallet Balance
     * @param INT $userId
     * @param Boolean $creditType 0-Credit 1-Debit (Default Debit)
     * @param INT $transType
     * @param Decimal $amount
     * @param INT $doneBy
     * @return 1
     */
    public function updateCapitalWallet($userId, $creditType = 1, $transType, $amount, $doneBy = 0)
    {
        $response = $this->updateBalane('capital_wallet', $userId, $amount, $creditType);

        $preBal = $response[0];
        $newBal = $response[1];

        $time = time();
        $date = date('Y-m-d', $time);

        $ip = $_SERVER['REMOTE_ADDR'];

        $query = 'INSERT INTO `capital_wallet_log` (`user_id`,`credit_type`,`transaction_type`,`value`,`before_bal`,`after_bal`,`createtime`,`createip`,`createid`,`transactiondate`) VALUES
                    (:user_id,:credit_type,:transaction_type,:amount,:before_bal,:after_bal,:createtime,:createip,:createid,:transactiondate)';


        $this->query($query);
        $this->bind(':user_id', $userId);
        $this->bind(':credit_type', $creditType);
        $this->bind(':transaction_type', $transType);
        $this->bind(':amount', $amount);
        $this->bind(':before_bal', $preBal);
        $this->bind(':after_bal', $newBal);
        $this->bind(':createtime', $time);
        $this->bind(':createip', $ip);
        $this->bind(':createid', $doneBy);
        $this->bind(':transactiondate', $date);

        $this->execute();

        return $newBal;
    }

    /** Update BTC Coin Wallet Balance
     * @param INT $userId
     * @param Boolean $creditType 0-Credit 1-Debit (Default Debit)
     * @param INT $transType
     * @param Decimal $amount
     * @param INT $doneBy
     * @return 1
     */
    public function updateBTCWallet($userId, $creditType = 1, $transType, $amount, $doneBy = 0 ,$remarks)
    {


        $response = $this->updateBalane('btc_wallet', $userId, $amount, $creditType);


        $preBal = $response[0];
        $newBal = $response[1];

        $time = time();
        $date = date('Y-m-d', $time);

        $ip = $_SERVER['REMOTE_ADDR'];

        $query = 'INSERT INTO `wallet_log` (`user_id`,`credit_type`,`transaction_type`,`value`,`before_bal`,`after_bal`,`created_at`,`created_by`,`created_ip`,`remarks`) VALUES
                    (:user_id,:credit_type,:transaction_type,:value,:before_bal,:after_bal,:created_at,:created_by,:created_ip,:remarks)';


        $this->query($query);
        $this->bind(':user_id', $userId);
        $this->bind(':credit_type', $creditType);
        $this->bind(':transaction_type', $transType);
        $this->bind(':value', $amount);
        $this->bind(':before_bal', $preBal);
        $this->bind(':after_bal', $newBal);
        $this->bind(':created_at', $time);
        $this->bind(':created_ip', $ip);
        $this->bind(':created_by', $doneBy);
        $this->bind(':remarks', $remarks);
       // $this->bind(':transactiondate', $date);

        $this->execute();

        return $newBal;
    }


    public function updateWallet($userId, $creditType = 1, $transType, $amount, $doneBy = 0 ,$remarks,$walletName,$coin_id)
    {


        $response = $this->updateBalane($walletName, $userId, $amount, $creditType);


        $preBal = $response[0];
        $newBal = $response[1];

        $time = time();
        $date = date('Y-m-d', $time);

        $ip = $_SERVER['REMOTE_ADDR'];

        $query = 'INSERT INTO `wallet_log` (`user_id`,`credit_type`,`transaction_type`,`value`,`before_bal`,`after_bal`,`created_at`,`created_by`,`created_ip`,`remarks`,`coin_id`) VALUES
                    (:user_id,:credit_type,:transaction_type,:value,:before_bal,:after_bal,:created_at,:created_by,:created_ip,:remarks,:coin_id)';


        $this->query($query);
        $this->bind(':user_id', $userId);
        $this->bind(':credit_type', $creditType);
        $this->bind(':transaction_type', $transType);
        $this->bind(':value', $amount);
        $this->bind(':before_bal', $preBal);
        $this->bind(':after_bal', $newBal);
        $this->bind(':created_at', $time);
        $this->bind(':created_ip', $ip);
        $this->bind(':created_by', $doneBy);
        $this->bind(':remarks', $remarks);
        $this->bind(':coin_id', $coin_id);
       // $this->bind(':transactiondate', $date);

        $this->execute();

        return $newBal;
    }


    /** Update ETH Coin Wallet Balance
     * @param INT $userId
     * @param Boolean $creditType 0-Credit 1-Debit (Default Debit)
     * @param INT $transType
     * @param Decimal $amount
     * @param INT $doneBy
     * @return 1
     */
    public function updateETHCoinWallet($userId, $creditType = 1, $transType, $amount, $doneBy = 0 ,$remarks)
    {


        $response = $this->updateBalane('eth_wallet', $userId, $amount, $creditType);


        $preBal = $response[0];
        $newBal = $response[1];

        $time = time();
        $date = date('Y-m-d', $time);

        $ip = $_SERVER['REMOTE_ADDR'];

        $query = 'INSERT INTO `wallet_log` (`user_id`,`credit_type`,`transaction_type`,`value`,`before_bal`,`after_bal`,`created_at`,`created_by`,`created_ip`,`remarks`) VALUES
                    (:user_id,:credit_type,:transaction_type,:value,:before_bal,:after_bal,:created_at,:created_by,:created_ip,:remarks)';


        $this->query($query);
        $this->bind(':user_id', $userId);
        $this->bind(':credit_type', $creditType);
        $this->bind(':transaction_type', $transType);
        $this->bind(':value', $amount);
        $this->bind(':before_bal', $preBal);
        $this->bind(':after_bal', $newBal);
        $this->bind(':created_at', $time);
        $this->bind(':created_ip', $ip);
        $this->bind(':created_by', $doneBy);
        $this->bind(':remarks', $remarks);
       // $this->bind(':transactiondate', $date);

        $this->execute();

        return $newBal;
    }

    /** Update AQN Coin Wallet Balance
     * @param INT $userId
     * @param Boolean $creditType 0-Credit 1-Debit (Default Debit)
     * @param INT $transType
     * @param Decimal $amount
     * @param INT $doneBy
     * @return 1
     */
    public function updateAQNWallet($userId, $creditType = 1, $transType, $amount, $doneBy = 0)
    {
        $response = $this->updateBalane('aqn_wallet', $userId, $amount, $creditType);

        $preBal = $response[0];
        $newBal = $response[1];

        $time = time();
        $date = date('Y-m-d', $time);

        $ip = $_SERVER['REMOTE_ADDR'];

        $query = 'INSERT INTO `aqn_wallet_log` (`user_id`,`credit_type`,`transaction_type`,`value`,`before_bal`,`after_bal`,`createtime`,`createip`,`createid`,`transactiondate`) VALUES
                    (:user_id,:credit_type,:transaction_type,:amount,:before_bal,:after_bal,:createtime,:createip,:createid,:transactiondate)';


        $this->query($query);
        $this->bind(':user_id', $userId);
        $this->bind(':credit_type', $creditType);
        $this->bind(':transaction_type', $transType);
        $this->bind(':amount', $amount);
        $this->bind(':before_bal', $preBal);
        $this->bind(':after_bal', $newBal);
        $this->bind(':createtime', $time);
        $this->bind(':createip', $ip);
        $this->bind(':createid', $doneBy);
        $this->bind(':transactiondate', $date);

        $this->execute();

        return $newBal;
    }

    /** Update Caps Left Balance
     * @param INT $userId
     * @param Boolean $creditType 0-Credit 1-Debit (Default Debit)
     * @param INT $transType
     * @param Decimal $amount
     * @param INT $doneBy
     * @return 1
     */
    public function updateCapsLeftWallet($userId, $creditType = 1, $transType, $amount, $doneBy = 0)
    {

        if ($creditType == 0) {
            //When there is a Credit we will increase the Max Cap limit also    
            $this->updateBalane('max_caps', $userId, $amount, $creditType);
        }

        $response = $this->updateBalaneOptimized('caps_left', $userId, $amount, $creditType);

        $preBal = $response[0];
        $newBal = $response[1];



        $time = time();
        $date = date('Y-m-d', $time);

        if ($newBal == 0) {
            $query = "UPDATE user_wallet SET caps_expire_time='$time' WHERE user_id='$userId'";
            $this->callSql($query);
        }

        $ip = $_SERVER['REMOTE_ADDR'];

        $query = 'INSERT INTO `caps_left_wallet_log` (`user_id`,`credit_type`,`transaction_type`,`value`,`before_bal`,`after_bal`,`createtime`,`createip`,`createid`,`transactiondate`) VALUES
                    (:user_id,:credit_type,:transaction_type,:amount,:before_bal,:after_bal,:createtime,:createip,:createid,:transactiondate)';


        $this->query($query);
        $this->bind(':user_id', $userId);
        $this->bind(':credit_type', $creditType);
        $this->bind(':transaction_type', $transType);
        $this->bind(':amount', $amount);
        $this->bind(':before_bal', $preBal);
        $this->bind(':after_bal', $newBal);
        $this->bind(':createtime', $time);
        $this->bind(':createip', $ip);
        $this->bind(':createid', $doneBy);
        $this->bind(':transactiondate', $date);

        $this->execute();

        return $newBal;
    }
}
