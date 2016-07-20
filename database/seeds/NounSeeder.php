<?php

use Illuminate\Database\Seeder;

class NounSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Noun -> adjective group mapping.
        $noun_specification = [
            'key'                                  => [
                'adjective_groups' => ['metal'],
                'tags'             => ['opens']
            ],
            'paperknife'                           => [
                'adjective_groups' => ['metal'],
                'tags'             => ['opens']
            ],
            'wire twisted into the shape of a man' => [
                'adjective_groups' => ['metal'],
                'tags'             => []
            ],
            'music box'                            => [
                'adjective_groups' => ['metal'],
                'tags'             => ['opens']
            ],
            'pocket watch'                         => [
                'adjective_groups' => ['metal'],
                'tags'             => ['opens']
            ],
            'locket'                               => [
                'adjective_groups' => ['metal', 'other material'],
                'tags'             => ['opens']
            ],
            'stylus'                               => [
                'adjective_groups' => ['metal', 'wood', 'other material'],
                'tags'             => ['draws']
            ],
            'monocle'                              => [
                'adjective_groups' => ['glass'],
                'tags'             => []
            ],
            'glass marble'                         => [
                'adjective_groups' => ['glass'],
                'tags'             => []
            ],
            'figurine of a bird'                   => [
                'adjective_groups' => ['wood', 'other material'],
                'tags'             => []
            ],
            'flute'                                => [
                'adjective_groups' => ['wood', 'other material'],
                'tags'             => []
            ],
            'smoking pipe'                         => [
                'adjective_groups' => ['wood', 'other material'],
                'tags'             => ['draws']
            ],
            'mask'                                 => [
                'adjective_groups' => ['wood', 'other material'],
                'tags'             => []
            ],
            'tooth'                                => [
                'adjective_groups' => ['mammal'],
                'tags'             => ['once-alive']
            ],
            'tail'                                 => [
                'adjective_groups' => ['mammal'],
                'tags'             => ['once-alive']
            ],
            'feather'                              => [
                'adjective_groups' => ['bird'],
                'tags'             => ['once-alive']
            ],
            'egg'                                  => [
                'adjective_groups' => ['bird'],
                'tags'             => ['opens']
            ],
        ];

        //Build a list of nouns that need to be created...
        $nouns = [];
        foreach (array_keys($noun_specification) as $noun) {
            $nouns[] = ['word' => $noun];
        }

        //...insert them to the DB...
        DB::table('nouns')->insert($nouns);

        //...and get back the IDs that they were given, along with the IDs of the adjective groups.
        $noun_ids  = DB::table('nouns')->pluck('id', 'word');
        $group_ids = DB::table('adjective_groups')->pluck('id', 'name');


        //Build a list of noun -> adjective group mappings to add to the DB...
        $noun_adjective_groups = [];
        foreach ($noun_specification as $noun => $noun_properties) {
            $noun_id = $noun_ids[$noun];
            foreach ($noun_properties['adjective_groups'] as $tag) {
                $noun_adjective_groups[] = [
                    'noun_id'            => $noun_id,
                    'adjective_group_id' => $group_ids[$tag]
                ];
            }

        }

        //... and insert them.
        DB::table('noun_adjective_group')->insert($noun_adjective_groups);


        //Build a list of noun -> tag mappings to add to the DB...
        $tag_ids = DB::table('tags')->pluck('id', 'name');

        $noun_tags = [];
        foreach ($noun_specification as $noun => $noun_properties) {
            $noun_id = $noun_ids[$noun];
            foreach ($noun_properties['tags'] as $tag) {
                $noun_tags[] = [
                    'noun_id' => $noun_id,
                    'tag_id'  => $tag_ids[$tag]
                ];
            }
        }

        //... and insert them.
        DB::table('noun_tag')->insert($noun_tags);

    }
}
