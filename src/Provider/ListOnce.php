<?php

namespace ListOnce\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use ListOnce\Entity\Entity;
use ListOnce\Entity\EntityCollection;
use ListOnce\Message;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ListOnce
 *
 * @package ListOnce\Provider
 */
class ListOnce
{

    /**
     * End point URL
     */
    const ENDPOINT_URI = 'http://www.listonce.com.au/api/%s?api_key=%s';

    /**
     * List once API key
     *
     * @var string
     */
    protected $apiKey = null;

    /**
     * HTTP Adapter
     *
     * @var Client
     */
    protected $httpClient = null;

    /**
     * Constructor
     *
     * @param string $apiKey
     * @param Client $httpClient
     */
    public function __construct($apiKey, Client $httpClient)
    {
        $this->apiKey = $apiKey;
        $this->httpClient = $httpClient;
    }

    /**
     * Build a query URL to the API
     *
     * @param string $function API function
     * @param array $data Request data
     * @param string $method The request method
     *
     * @return Request
     */
    public function buildRequest($function, $data = [], $method = 'get')
    {
        $uri = sprintf(self::ENDPOINT_URI, $function, $this->apiKey);
        $query = http_build_query($data, '', '&');
        if (strtolower($method) === 'get') {
            if (!empty($query)) {
                $uri .= '&'  . $query;
            }
            return new Request($method, $uri);
        }
        return new Request($method, $uri, [], $query);
    }

    /**
     * Send the request and return the parsed response
     *
     * @param RequestInterface $request
     *
     * @return object
     */
    public function sendRequest(RequestInterface $request)
    {
        $response = $this->httpClient->send($request);
        return $this->parseResponse($response);
    }

    /**
     * Send the request and return the parsed response
     *
     * @param RequestInterface $request
     * @param string $dataType
     *
     * @return Entity
     */
    public function requestEntity(RequestInterface $request, $dataType)
    {
        return Entity::make($this->sendRequest($request), $request, $dataType);
    }

    /**
     * Send the request and return the parsed response
     *
     * @param RequestInterface $request
     * @param string $dataType
     *
     * @return EntityCollection
     */
    public function requestCollection(RequestInterface $request, $dataType)
    {
        return EntityCollection::make($this->sendRequest($request), $request, $dataType);
    }

    /**
     * Parse a JSON response
     *
     * @param ResponseInterface $response
     *
     * @return object
     */
    protected function parseResponse(ResponseInterface $response)
    {
        $json = (string) $response->getBody();
        if (empty($json)) {
            throw new \RuntimeException('Empty response from REST API.');
        }

        $result = @json_decode($json);
        $error = json_last_error();
        if ($error !== JSON_ERROR_NONE) {
            if (function_exists('json_last_error_msg')) {
                $error = json_last_error_msg();
            }
            throw new \RuntimeException('Unable to parse response JSON: ' . $error);
        }

        return $result;
    }

    /**
     * Returns details for a listing specified by listing_id.
     *
     * @param int $listingId
     *
     * @return Entity
     */
    public function getListing($listingId)
    {
        $request = $this->buildRequest('get-listing', ['listing_id' => $listingId]);
        return $this->requestEntity($request, 'Listing');
    }

    /**
     * Returns the specified list of listings based on the supplied arguments.
     *
     * @param array $query
     *
     * @return EntityCollection
     */
    public function searchListings($query = [])
    {
        $request = $this->buildRequest('search-listings', $query);
        return $this->requestCollection($request, 'Listing');
    }

    /**
     * Returns the specified list of inspection times for listings based on the supplied arguments.
     *
     * @param array $query
     *
     * @return EntityCollection
     */
    public function searchInspectionTimes($query = [])
    {
        $request = $this->buildRequest('search-inspection-times', $query);
        return $this->requestCollection($request, 'InspectionTime');
    }

    /**
     * Returns a list of auction date/times.
     *
     * @param array $query
     *
     * @return EntityCollection
     */
    public function searchAuctions($query = [])
    {
        $request = $this->buildRequest('search-auctions', $query);
        return $this->requestCollection($request, 'Auction');
    }

    /**
     * Returns a list of distinct suburbs which have at least one listing. Useful in search forms.
     *
     * @return array
     */
    public function getSuburbs()
    {
        $request = $this->buildRequest('get-suburbs');
        return $this->sendRequest($request);
    }

    /**
     * Returns a list of the current details for a given client.
     *
     * @param array $query
     *
     * @return EntityCollection
     */
    public function getOffices($query = [])
    {
        $request = $this->buildRequest('get-office', $query);
        return $this->requestCollection($request, 'Office');
    }

    /**
     * Returns a list of the current listing Agents and their details for a given group/client.
     *
     * @param array $query
     *
     * @return EntityCollection
     */
    public function getAgents($query = [])
    {
        $request = $this->buildRequest('get-agents', $query);
        return $this->requestCollection($request, 'Agent');
    }

    /**
     * Returns a list of News Articles created by the office for use on their website.
     *
     * @param array $query
     *
     * @return EntityCollection
     */
    public function getNews($query = [])
    {
        $request = $this->buildRequest('get-news', $query);
        return $this->requestCollection($request, 'News');
    }

    /**
     * Returns a list of Testimonials.
     *
     * @param array $query
     *
     * @return EntityCollection
     */
    public function getTestimonials($query = [])
    {
        $request = $this->buildRequest('get-testimonials', $query);
        return $this->requestCollection($request, 'Testimonial');
    }

    /**
     * Returns a list of featured listings.
     *
     * @param array $query
     *
     * @return EntityCollection
     */
    public function getFeaturedListings($query = [])
    {
        $request = $this->buildRequest('get-featured-listings', $query);
        return $this->requestCollection($request, 'Listing');
    }

    /**
     * Uses the ListOnce system to email from one user to another and record statistics
     *
     * @todo return a Message instance
     *
     * @param int $listingId
     * @param string $fromEmail
     * @param string $toEmail
     * @param string $subject
     * @param string $message
     * @param array $query
     *
     * @return object
     */
    public function emailFriend($listingId, $fromEmail, $toEmail, $subject, $message, $query = [])
    {
        $query += [
            'listing_id' => $listingId,
            'from_email' => $fromEmail,
            'to_email' => $toEmail,
            'subject' => $subject,
            'message' => $message,
        ];
        $request = $this->buildRequest('email-a-friend', $query, 'post');
        return $this->sendRequest($request);
    }

    /**
     * Uses the ListOnce system to email the Listing Agent/Office and record statistics
     *
     * @todo return a Message instance
     *
     * @param int $listingId
     * @param string $fromEmail
     * @param string $message
     * @param array $query
     *
     * @return object
     */
    public function contactAgentListing($listingId, $name, $fromEmail, $message, $query = [])
    {
        $query += [
            'listing_id' => $listingId,
            'name' => $name,
            'from_email' => $fromEmail,
            'message' => $message,
        ];
        $request = $this->buildRequest('contact-enquiry', $query, 'post');
        return $this->sendRequest($request);
    }

    /**
     * Uses the ListOnce system to email the Agent Directly
     *
     * @todo return a Message instance
     *
     * @param int $listingId
     * @param string $fromEmail
     * @param string $message
     * @param array $query
     *
     * @return object
     */
    public function contactAgent($listingId, $name, $fromEmail, $message, $query = [])
    {
        $query += [
            'listing_id' => $listingId,
            'name' => $name,
            'from_email' => $fromEmail,
            'message' => $message,
        ];
        $request = $this->buildRequest('agent-contact-enquiry', $query, 'post');
        return $this->sendRequest($request);
    }

    /**
     * Uses the ListOnce system to email the Office Directly
     *
     * @todo return a Message instance
     *
     * @param int $clientId Office ID
     * @param string $fromEmail
     * @param string $message
     * @param array $query
     *
     * @return object
     */
    public function contactOffice($clientId, $name, $fromEmail, $message, $query = [])
    {
        $query += [
            'client_id' => $clientId,
            'name' => $name,
            'from_email' => $fromEmail,
            'message' => $message,
        ];
        $request = $this->buildRequest('office-contact-enquiry', $query, 'post');
        return $this->sendRequest($request);
    }

    /**
     * Returns a list of current alert subscribers for a given group.
     *
     * @param array $query
     *
     * @return EntityCollection
     */
    public function getAlertList($query = [])
    {
        $request = $this->buildRequest('alerts/list/', $query);
        return $this->requestCollection($request, 'Alert');
    }

    /**
     * Creates a new alert subscription.
     *
     * @todo return a Message instance
     *
     * @param string $email
     * @param array $search
     * @param array $query
     *
     * @return object
     */
    public function alertSubscribe($email, $search = [], $query = [])
    {
        $query += [
            'email_address' => $email,
            'search_criteria' => http_build_query($search),
        ];
        $request = $this->buildRequest('alerts/subscribe/', $query);
        return $this->sendRequest($request);
    }

    /**
     * Returns an object containing details about the subscription.
     *
     * @param int $alertId
     *
     * @return Entity
     */
    public function getAlertDetails($alertId)
    {
        $request = $this->buildRequest('alerts/details/' . $alertId . '/');
        return $this->requestEntity($request, 'Alert');
    }

    /**
     * Update existing fields of an alert
     *
     * @todo return a Message instance
     *
     * @param int $alertId
     * @param array $search
     * @param array $query
     *
     * @return object
     */
    public function alertUpdate($alertId, $search = [], $query = [])
    {
        $query += [
            'alert_id' => $alertId,
            'search_criteria' => http_build_query($search),
        ];
        $request = $this->buildRequest('alerts/update/', $query);
        return $this->sendRequest($request);
    }

    /**
     * Update existing fields of an alert
     *
     * @todo return a Message instance
     *
     * @param int $alertId
     *
     * @return object
     */
    public function alertUnsubscribe($alertId)
    {
        $query = [
            'alert_id' => $alertId,
        ];
        $request = $this->buildRequest('alerts/unsubscribe/', $query);
        return $this->sendRequest($request);
    }

    /**
     * Return floorplans for a property
     *
     * @param array $query
     *
     * @return EntityCollection
     */
    public function getInteractiveFloorplans($query = [])
    {
        $request = $this->buildRequest('get-interactive-floorplans', $query);
        return $this->requestCollection($request, 'Floorplan');
    }

    /**
     * Return floorplans for a property
     *
     * @param array $query
     *
     * @return EntityCollection
     */
    public function getExternalLinks($query = [])
    {
        $request = $this->buildRequest('external-links', $query);
        return $this->requestCollection($request, 'ExternalLink');
    }

    /**
     * Return a list of categories which contain listings
     *
     * This function is undocumented.
     *
     * @return object
     */
    public function getCategories()
    {
        $request = $this->buildRequest('get-categories');
        return $this->sendRequest($request);
    }
}