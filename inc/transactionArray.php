<?php

/*$transactionArray = array
(  
	"1" => "Deposit",
	"2" => "Withdraw",
	"3" => "Coin Swap", 
	"4" => "Transfer", 
);*/


$transactionArray = array
(  
	"1" => "deposit_text",
	"2" => "withdraw_text",
	"3" => "coin_swap_text", 
	"4" => "transfer_text", 
	"31" => "system_credit", //admin credit
	"32" => "system_debit", //admin debit
);

$creditArray = array(0=>"Credit",1=>"Debit");

$wallet_decimal_limits = array
(   
    "usd" => "2",
	"btc" => "8",
	"usdt" => "6",
);

$wallet_groups_array = array
(   
    "1" => "BTC group",
	"2" => "ETH group",
);
?>