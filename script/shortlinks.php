<?php 


## HERE IS EXAMPLE OF HOW TO ADD CUSTOM SHORTLINKS
## YOU STILL NEED TO ENABLE IN ADMIN PANEL

$settings['sldata']['10000'] = array(
    'id' => '10000', // Start with id greater than 10000
    'name' => 'Example1', // Name of Shortlink
    'apilink' => 'https://example.com/api?api={apikey}&url={url}', // leave ?api={apikey}&url={url} just change url!
    'views' => '1', // Max view count of shortener
    'cpm' => '11.00', // CPM of Shortener
    'referral' => 'https://example.com', // Your Referral link
    'status' => 'N' // Should be Y unless you dont want it to show in list then put N
);

$settings['sldata']['10001'] = array(
    'id' => '10001', // Start with id greater than 10000
    'name' => 'Example2', // Name of Shortlink
    'apilink' => 'https://example.com/api?api={apikey}&url={url}', // leave ?api={apikey}&url={url} just change url!
    'views' => '1', // Max view count of shortener
    'cpm' => '11.00', // CPM of Shortener
    'referral' => 'https://example.com', // Your Referral link
    'status' => 'N' // Should be Y unless you dont want it to show in list then put N
);


