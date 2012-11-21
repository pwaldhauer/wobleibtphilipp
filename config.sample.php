<?php

require_once('lib/WhereAmI.php');

$collector = new WhereAmI();

$collector->initializeFoursquare(
    'client_id',
    'client_secret',
    'access_token'
);

$collector->setStartStop('venue_id_start', 'venue_id_stop');

// The times are in a date('Hi') format. So 10:30 gets 1030, 9:30 gets 930, etc 
// (It's easier to type when writing it down from a pdf ;))

$collector->addWayPart('bus', 15 * 60, 
    array(
        603,608,618,623,628,637,642,647,652,657,702,707,717,722,727,732,737,
        742,747,752,757,802,807,817,822,827,832,842,852,902,903,912,922,932,
        942,952,953,1002,1012,1022,1032,1042,1052,1053,1102)
);

$collector->addWayPart('u2', 30 * 60,
    array(
        601,606,611,617,623,628,633,639,645,651,657,703,709,715,721,727,733,
        739,745,751,757,803,809,815,821,827,833,839,845,851,857,903,909,915,
        921,927,933,939,941,951,1001,1011,1021,1031,1041,1051,1101,1111,1121,
        1131,1141,1151,1201,1211)
);

$collector->addWayPart('laufen', 5 * 60);

$result = $collector->getCurrentStatus();