<?php
namespace EngageApi;

use GuzzleHttp\Client as GuzzleClient;
use Exception;

/**
 * An object to assist with calls to the engage api
 *
 * @link https://unomaha.campuslabs.com/engage/api/docs
 * @link https://unomaha.campuslabs.com/engage/api/swagger/ui/index
 * @author elwingert
 *
 */
class Client
{
    private $client;
    private $debugMode = false;

    public $lastResponse;
    public $statusCode;

    /**
     * Start up the engage api
     * @param string  $base_uri  Full url to the engage endpoint
     * @param string  $api_key   Api key provided by engage
     * @param boolean $debugMode Enable or disable debug mode on construct
     */
    public function __construct($base_uri, $api_key, $debugMode = false)
    {
        $options = [
            'base_uri' => $base_uri,
            'headers' => ['X-Engage-Api-Key' => $api_key],
        ];

        $this->client = new GuzzleClient($options);

        $this->setDebugMode($debugMode);

        if ($this->debugMode) {
            $this->testApi();
        }
    }

    /**
     * Set the debug mode
     * @param boolean $mode Enable or disable debug mode
     */
    public function setDebugMode($mode)
    {
        $this->debugMode = ($mode) ? true : false;
    }

    /**
     * Call the test api endpoint
     * @return boolean    Either throw an exception or return true
     * @throws Exception when not a success
     */
    public function testApi()
    {
        $res = $this->request('GET', 'test');
        $message = $res->items[0]->messages[0];

        if ($message <> 'Success') {
            throw new Exception($message);
        }

        return true;
    }

    /**
     * Call the create membership endpoint
     * @param  array|object $options An array or object of extra options
     * @return object                The full json in object notication if successful
     */
    public function createMembership($options)
    {
        return $this->request('POST', 'Memberships', $options);
    }

    /**
     * Call the end membership endpoint
     * @param  int    $id The id of the endpoint
     * @return object     The full json in object notication if successful
     * @throws Exception  When unable to successfully end the membership
     */
    public function endMembership($id)
    {
        try {
            $ret = $this->request('DELETE', 'Memberships/'.$id);
            return $ret;
        } catch (ClientException $e) {
            $json = json_decode($e->getResponse()->getBody()->getContents());
            $json->statusCode = $e->getResponse()->getStatusCode();
            throw new Exception( json_encode($json) );
        }
        
    }

    /**
     * Call the get attendee endpoint
     * @param  int    $id The id of the endpoint
     * @return object     The full json in object notication if successful
     */
    public function getAttendee(int $id)
    {
        return $this->request('GET', 'Attendees/'.$id);
    }

    /**
     * Call the get attendees endpoint
     * @param  array|object $options An array or object of extra options
     * @return object                The full json in object notication if successful
     */
    public function getAttendees($options = [])
    {
        return $this->request('GET', 'Attendees', $options);
    }

    /**
     * Call the get attendees endpoint repeatedly to get all pages
     * @return object The full json in object notication if successful
     */
    public function getAttendeesAll()
    {
        return $this->getAll('Attendees');
    }

    /**
     * Call the get event endpoint
     * @param  int    $id The id of the endpoint
     * @return object     The full json in object notication if successful
     */
    public function getEvent(int $id)
    {
        return $this->request('GET', 'Events/'.$id);
    }

    /**
     * Call the get events endpoint
     * @param  array|object $options An array or object of extra options
     * @return object                The full json in object notication if successful
     */
    public function getEvents($options = [])
    {
        return $this->request('GET', 'Events', $options);
    }

    /**
     * Call the get events endpoint repeatedly to get all pages
     * @return object The full json in object notication if successful
     */
    public function getEventsAll()
    {
        return $this->getAll('Events');
    }

    /**
     * Call the get membership endpoint
     * @param  int    $id The id of the endpoint
     * @return object     The full json in object notication if successful
     */
    public function getMembership(int $id)
    {
        return $this->request('GET', 'Memberships/'.$id);
    }

    /**
     * Call the get memberships endpoint
     * @param  array|object $options An array or object of extra options
     * @return object                The full json in object notication if successful
     */
    public function getMemberships($options = [])
    {
        return $this->request('GET', 'Memberships', $options);
    }

    /**
     * Call the get memberships endpoint repeatedly to get all pages
     * @return object The full json in object notication if successful
     */
    public function getMembershipsAll($options = [])
    {
        return $this->getAll('Memberships', $options);
    }

    /**
     * Call the get organization endpoint
     * @param  int    $id The id of the endpoint
     * @return object     The full json in object notication if successful
     */
    public function getOrganization(int $id)
    {
        return $this->request('GET', 'Organizations/'.$id);
    }

    /**
     * Call the get organizations endpoint
     * @param  array|object $options An array or object of extra options
     * @return object                The full json in object notication if successful
     */
    public function getOrganizations($options = [])
    {
        return $this->request('GET', 'Organizations', $options);
    }

    /**
     * Call the get organizations endpoint repeatedly to get all pages
     * @return object The full json in object notication if successful
     */
    public function getOrganizationsAll()
    {
        return $this->getAll('Organizations');
    }

    /**
     * Call the get position endpoint
     * @param  int    $id The id of the endpoint
     * @return object     The full json in object notication if successful
     */
    public function getPosition(int $id)
    {
        return $this->request('GET', 'Positions/'.$id);
    }

    /**
     * Call the get positions endpoint
     * @param  array|object $options An array or object of extra options
     * @return object                The full json in object notication if successful
     */
    public function getPositions($options = [])
    {
        return $this->request('GET', 'Positions', $options);
    }

    /**
     * Call the get positions endpoint repeatedly to get all pages
     * @return object The full json in object notication if successful
     */
    public function getPositionsAll()
    {
        return $this->getAll('Positions');
    }

    /**
     * Call the get user endpoint
     * @param  int    $id The id of the endpoint
     * @return object     The full json in object notication if successful
     */
    public function getUser(int $id)
    {
        return $this->request('GET', 'Users/'.$id);
    }

    /**
     * Call the get users endpoint
     * @param  array|object $options An array or object of extra options
     * @return object               The full json in object notication if successful
     */
    public function getUsers($options = [])
    {
        return $this->request('GET', 'Users', $options);
    }

    /**
     * Call the get users endpoint repeatedly to get all pages
     * @return object The full json in object notification if successful
     */
    public function getUsersAll()
    {
        return $this->getAll('Users');
    }

    /**
     * Update a user
     * @param  int    $id   The id of the endpoint
     * @param  object $user The users updateable values in object notation
     * @return object       The full json in object notication if successful
     * @throws Exception    When an invalid object is passed in
     */
    public function updateUser(int $id, $user)
    {
        if (!is_object($user)) {
            throw new Exception('The data stored in the $user variable must be an object.');
        }
        //Make sure all values are correctly set via the user object
        $u = $this->getUser($user->userId);
        $user->campusEmail                  = isset($user->campusEmail) ? $user->campusEmail : $u->campusEmail;
        $user->institutionProvidedFirstName = isset($user->institutionProvidedFirstName) ? $user->institutionProvidedFirstName : $u->institutionProvidedFirstName;
        $user->institutionProvidedLastName  = isset($user->institutionProvidedLastName) ? $user->institutionProvidedLastName : $u->institutionProvidedLastName;
        $user->legalFirstName               = isset($user->legalFirstName) ? $user->legalFirstName : $u->legalFirstName;
        $user->legalLastName                = isset($user->legalLastName) ? $user->legalLastName : $u->legalLastName;
        $user->cardId                       = isset($user->cardId) ? $user->cardId : $u->cardId;
        $user->sisId                        = isset($user->sisId) ? $user->sisId : $u->sisId;

        return $this->request('PUT', 'Users/'.$id, $user);
    }

    /**
     * Returns the last api response
     * @param  boolean       $decode Either get back the raw json or decoded values
     * @return object|string         The full json in object notication if successful
     */
    public function getLastResponse($decode = true)
    {
        if ($decode) {
            $res = json_decode($this->lastResponse);
        } else {
            $res = $this->lastResponse;
        }
        return $res;
    }

    /**
     * Parses over all of the pages of returned values to get a superset
     * @param  string       $method  The api endpoint you are after
     * @param  array|object  $options An array or object of extra options
     * @return object                 The full json in object notication if successful
     */
    private function getAll($method, $options = [])
    {
        $obj = new \stdClass();

        $method = 'get'.$method;


        $first = $this->{$method}(array_merge(['pageSize' => 500], $options));
        $items = $first->items;
        $totalPages = $first->totalPages;

        for ($i=2; $i<=$totalPages; $i++) {
            $u = $this->{$method}(array_merge(['pageSize' => 500, 'page' => $i], $options));
            $items = array_merge($items, $u->items);
        }

        $obj->pageNumber = 1;
        $obj->pageSize   = (count($items));
        $obj->totalItems = (count($items));
        $obj->totalPages = 1;
        $obj->items      = $items;

        $this->lastResponse = json_encode($obj);

        return $obj;
    }

    /**
     * Makes the actual api request
     * @param  string       $method  The http type you are after
     * @param  string       $request The api endpoint
     * @param  array|object $options An array or object of extra options
     * @return object                The full json in object notication if successful
     * @throws Exception             When an invalid status code is returned
     * @throws Exception             When an invalid request type is returned
     */
    private function request($method, $request, $options = [])
    {
        $parms['json'] = $parms['query'] = [];

        switch ($method) {
            case 'DELETE':
                $parms['json'] = ($options);
                break;
            case 'GET':
                $parms['query'] = $options;
                break;
            case 'POST':
                $parms['json'] = ($options);
                break;
            case 'PUT':
                $parms['json'] = ($options);
                break;
            default:
                throw new Exception('Invalid request type');
        }

        $res = $this->client->request(
            $method,
            $request,
            [
                'json'  => $parms['json'],
                'query' => $parms['query'],
                'debug' => $this->debugMode,
            ]
        );

        $this->statusCode = $res->getStatusCode();

        if ($method == 'DELETE' && $this->statusCode == '204') {
            echo "Delete successful\n";
            return true;
        } elseif ($this->statusCode <> '200') {
            throw new Exception('Invalid status code returned ('.$this->statusCode.')');
            return false;
        }

        if (is_object($res->getBody())) {
            $json = $res->getBody()->getContents();
        } else {
            $json = $res->getBody();
        }

        $this->lastResponse = $json;

        return json_decode($json);
    }
}
