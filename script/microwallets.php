<?php

// Microwallets Data - Be careful when editing this array
$microwallets = [
    // https://cryptoo.me/api-doc/
    'cryptoo' => [
        'name' => 'Cryptoo',
        'currencies' => ['BTC'],
        'api_base' => 'https://cryptoo.me/api/v1/',
        'check' => 'https://cryptoo.me/check/{address}',
        'url' => 'https://gr8.cc/goto/cryptoo'
    ],
    // https://expresscrypto.io/account/site-owner/panel/documentation
    'expresscrypto' => [ 
        'name' => 'ExpressCrypto',
        'currencies' => ['ADA','BCH','BCN','BNB','BTC','BTT','DASH','DGB','DOGE','ETC','ETH','EXG','EXS','KMD','LSK','LTC','NEO','PIVX','PPC','RDD','RVN','STRAX','TRX','USDT','VTC','WAVES','XMR','XRP','XTZ','ZEC','ZEN'],
        'api_base' => 'https://expresscrypto.io/public-api/v2/',
        'check' => 'https://expresscrypto.io/dashboard',
        'url' => 'https://gr8.cc/goto/expresscrypto',
        'placeholder' => 'Enter your Unique ExpressCrypto ID'
    ],
    // https://www.faucetfly.com/bitcoin-micropayment-api-docs
    'faucetfly' => [
        'name' => 'FaucetFly',
        'currencies' => ['BTC','ETH'],
        'api_base' => 'https://www.faucetfly.com/api/v1/',
        'check' => 'https://www.faucetfly.com/check/{address}',
        'url' => 'https://gr8.cc/goto/faucetfly',
    ],
    // https://faucetpay.io/page/api-documentation
    'faucetpay' => [
        'name' => 'FaucetPay',
        'currencies' => ['BCH','BNB','BTC','DASH','DGB','DOGE','ETH','FEY','LTC','TRX','USDT','ZEC'],
        'api_base' => 'https://faucetpay.io/api/v1/',
        'check' => 'https://faucetpay.io/page/user-admin',
        'url' => 'https://gr8.cc/goto/faucetpay'
    ],
    // https://microwallet.co/docs
    'microwallet' => [
        'name' => 'Microwallet',
        'currencies' => ['BCH','BTC','DOGE','ETH','LTC'],
        'api_base' => 'https://api.microwallet.co/v1/',
        'check' => 'https://microwallet.co/dashboard',
        'url' => 'https://gr8.cc/goto/microwallet'
    ]
];

// Currency List
$currencies = [
	'ADA' 	=> 'Cardano',
    'BCH' 	=> 'Bitcoin Cash',
    'BCN' 	=> 'Bytecoin',
    'BNB' 	=> 'Binance Coin',
    'BTC' 	=> 'Bitcoin',
    'BTT' 	=> 'BitTorrent',
    'DASH' 	=> 'Dash',
    'DGB' 	=> 'DigiByte',
    'DOGE' 	=> 'Dogecoin',
    'EOS' 	=> 'Eos',
    'ETC' 	=> 'Ethereum Classic',
    'ETH' 	=> 'Ethereum',
    'EXG' 	=> 'EX Gold',
    'EXS' 	=> 'EX Silver',
	'FEY'	=> 'Feyorra',
	'KMD'	=> 'Komodo',
    'LSK' 	=> 'Lisk',
    'LTC' 	=> 'Litecoin',
    'NEO' 	=> 'Neo',
	'PIVX' 	=> 'Pivx',
    'PPC' 	=> 'Peercoin',
    'QTUM' 	=> 'Qtum',
	'RDD' 	=> 'Reddcoin',
	'RVN'   => 'Ravencoin',
    'STRAX' => 'Stratis',
    'TRX' 	=> 'Tron',
	'USDT'	=> 'Tether',
	'VTC'	=> 'Vertcoin',
    'WAVES' => 'Waves',
    'XMR' 	=> 'Monero',
    'XPM' 	=> 'Primecoin',
    'XRP' 	=> 'Ripple',
	'XTZ' 	=> 'Tezos',
    'ZEC' 	=> 'Zcash',
	'ZEN'	=> 'Horizen'
];
