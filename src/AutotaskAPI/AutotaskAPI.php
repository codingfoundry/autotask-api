<?php

namespace CodingFoundry\AutotaskAPI;

use ATWS;

class AutotaskAPI
{
    protected $client;

    public function __construct()
    {
        // Initiate the client object
        $this->client = new ATWS\Client(env('AUTOTASK_API'), [
            'login' => env('AUTOTASK_EMAIL'),
            'password' => env('AUTOTASK_PASSWORD')
        ], env('AUTOTASK_INTEGRATION_CODE'));
    }


    /**
     * @param $entity
     * @return \Illuminate\Support\Collection
     */
    public function get($entity) {

        // Wrap the query function to allow dynamic calls; Accounts; Contacts; etc.
        $query = new ATWS\AutotaskObjects\Query($entity);

        // Create a search field. Autotask API recommends using the id field.
        $search = new ATWS\AutotaskObjects\QueryField('id');

        /*
         * When constructing queries, be aware that Autotask will return only 500 records at once, sorted by id.
         * If there are over 500 records that meet your criteria, you must create multiple queries where each query
         * filters for id value > the maximum value received in the previous query.
         */

        $more = true;
        $maxID = 0;
        $accounts = [];

        while ($more) {

            $search->addExpression('GreaterThan', $maxID);
            $query->addField($search);

            $result = $this->client->query($query);

            foreach ($result->queryResult->EntityResults->Entity as $account) {
                array_push($accounts,$account);
            }

            if (count($result->queryResult->EntityResults->Entity) == 500) {
                $more = true;
                $maxID = $result->queryResult->EntityResults->Entity[499]->ID;
            } else {
                $more = false;
            }
        }

        return collect($accounts);
    }

}