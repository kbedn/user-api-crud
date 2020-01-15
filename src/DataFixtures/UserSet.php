<?php

$set = new h4cc\AliceFixturesBundle\Fixtures\FixtureSet(array(
    'locale' => 'de_DE',
    'seed' => 42,
    'do_drop' => false,
    'do_persist' => true,
));

$set->addFile(__DIR__.'/user.yml', 'yaml');

return $set;
