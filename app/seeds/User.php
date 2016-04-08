<?php
return [
  'model' => 'User',
  'dependencies' => [],
  'seeds' => function() {
    $seeds = [];
    require_once Config::app('fakerAutoloader');
    $faker = Faker\Factory::create();
    foreach(range(1,1) as $index) {
      $seeds[] = [
        'name_family'=> $faker->name,
        'password'=> password_hash('04EC533CF5326D89DEF', PASSWORD_DEFAULT),
        'nikname'=> $faker->userName,
        'age'=> $faker->numberBetween(18,50),
        'email'=> 'B255B9D104EC5@email.com' /*$faker->email*/ ,
      ];
    }
    return $seeds;
  },
];
