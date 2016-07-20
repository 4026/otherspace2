<?php

use Illuminate\Database\Seeder;

class AdjectiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Adjective groups -> adjectives -> tags
        $adjective_specification = [
            'metal'          => [
                'rusty'     => ['old'],
                'corroded'  => ['old'],
                'tarnished' => ['old'],
                'silver'    => ['precious'],
                'golden'    => ['precious'],
                'iron'      => [],
                'copper'    => [],
                'steel'     => [],
            ],
            'glass'          => [
                'cracked'      => ['old'],
                'chipped'      => ['old'],
                'clouded'      => ['old'],
                'misty'        => ['old'],
                'green-tinted' => [],
                'blue-tinted'  => [],
            ],
            'wood'           => [
                'ashen'         => ['once-alive'],
                'oaken'         => ['once-alive'],
                'birch'         => ['once-alive'],
                'mahogany'      => ['once-alive'],
                'bleached wood' => ['once-alive', 'old'],
                'warped wood'   => ['once-alive', 'old'],
                'stained wood'  => ['once-alive', 'old'],
            ],
            'other material' => [
                'bone'  => ['old', 'once-alive'],
                'ivory' => ['precious'],
                'jade'  => ['precious'],
            ],
            'mammal'         => [
                'fox'      => [],
                'rat'      => [],
                'terrier'  => [],
                'squirrel' => [],
                'wildcat'  => [],
                'mouse'    => [],
            ],
            'bird'           => [
                'crow'     => [],
                'raven'    => [],
                'starling' => [],
                'pigeon'   => [],
                'owl'      => [],
            ]
        ];

        //Create the groups in the DB...
        $groups = [];
        foreach (array_keys($adjective_specification) as $group) {
            $groups[] = ['name' => $group];
        }

        DB::table('adjective_groups')->insert($groups);

        //...and fetch the IDs they were given.
        $group_ids = DB::table('adjective_groups')->pluck('id', 'name');


        //Create the adjectives in the DB...
        $adjectives = [];
        foreach ($adjective_specification as $group => $adjective_tags) {
            $group_id = $group_ids[$group];
            foreach (array_keys($adjective_tags) as $adjective) {
                $adjectives[] = ['word' => $adjective, 'group_id' => $group_id];
            }
        }

        DB::table('adjectives')->insert($adjectives);

        //...and fetch the IDs they were given.
        $adjective_ids = DB::table('adjectives')->pluck('id', 'word');
        $tag_ids       = DB::table('tags')->pluck('id', 'name');


        //Create the tag links for each adjective
        $adjective_tags = [];
        foreach ($adjective_specification as $adjective_tags) {
            foreach ($adjective_tags as $adjective => $tags) {
                $adjective_id     = $adjective_ids[$adjective];
                foreach ($tags as $tag) {
                    $tag_id           = $tag_ids[$tag];
                    $adjective_tags[] = ['adjective_id' => $adjective_id, 'tag_id' => $tag_id];
                }
            }
        }
    }
}
