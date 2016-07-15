<?php

namespace ListOnce\Provider;

use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use ListOnce\Response;

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
    const ENDPOINT_URL = 'http://www.listonce.com.au/api/%s?api_key=%s';

    /**
     * List once API key
     *
     * @var string
     */
    protected $apiKey = null;

    /**
     * HTTP Adapter
     *
     * @var HttpAdapterInterface
     */
    protected $httpAdapter = null;

    /**
     * Constructor
     *
     * @param HttpAdapterInterface $httpAdapter
     * @param string $apiKey
     */
    public function __construct(HttpAdapterInterface $httpAdapter, $apiKey)
    {
        $this->httpAdapter = $httpAdapter;
        $this->apiKey = $apiKey;
    }

    /**
     * Query the API
     *
     * @param string $function
     * @param array $query
     *
     * @return object
     */
    public function executeQuery($function, $query = [])
    {
        $url = $this->buildQuery($function, $query);
        $response = $this->httpAdapter->get($url);
        return $this->parseResponse($response);
    }

    /**
     * Build a query URL to the API
     *
     * @return string
     */
    protected function buildQuery($function, $query)
    {
        $url = sprintf(self::ENDPOINT_URL, $function, $this->apiKey);
        $queryStr = http_build_query($query);
        if (!empty($queryStr)) {
            $url .= '&'  . $queryStr;
        }
        return $url;
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

        if (!empty($result->error_message)) {
            throw new \RuntimeException('REST API error: ' . $result->error_message);
        }

        return $result;
    }

    /**
     * Returns details for a listing specified by listing_id.
     *
     * @param int $listingId
     *
     * @return Response
     */
    public function getListing($listingId)
    {
        return new Response($this->executeQuery('get-listing', ['listing_id' => $listingId]), null, 'listing');
    }

    /**
     * Returns the specified list of listings based on the supplied arguments.
     *
     * @param array $query
     *
     * @return Response
     */
    public function searchListings($query = [])
    {
        return new Response($this->executeQuery('search-listings', $query), 'listings', 'listing');
    }

    /**
     * Returns the specified list of inspection times for listings based on the supplied arguments.
     *
     * @param array $query
     *
     * @return Response
     */
    public function searchInspectionTimes($query = [])
    {
        return new Response($this->executeQuery('search-inspection-times', $query), 'inspection_times', 'inspection-time');
    }

    /**
     * Returns a list of auction date/times.
     *
     * @param array $query
     *
     * @return Response
     */
    public function searchAuctions($query = [])
    {
        return new Response($this->executeQuery('search-auctions', $query), 'auctions', 'auction');
    }

    /**
     * Returns a list of distinct suburbs which have at least one listing. Useful in search forms.
     *
     * @return Response
     */
    public function getSuburbs()
    {
        return new Response($this->executeQuery('get-suburbs'), null, 'suburb');
    }

    /**
     * Returns a list of the current details for a given client.
     *
     * @param int $clientId
     *
     * @return Response
     */
    public function getOffice($clientId)
    {
        return new Response($this->executeQuery('get-office', ['client_id' => $clientId]), null, 'office');
    }

    /**
     * Returns a list of the current listing Agents and their details for a given group/client.
     *
     * @param array $query
     *
     * @return Response
     */
    public function getAgents($query = [])
    {
        return new Response($this->executeQuery('get-agents', $query), 'agents', 'agent');
    }

    /**
     * Returns a list of News Articles created by the office for use on their website.
     *
     * @param array $query
     *
     * @return Response
     */
    public function getNews($query = [])
    {
        return new Response($this->executeQuery('get-news', $query), null, 'news');
    }

    /**
     * Returns a list of Testimonials.
     *
     * @param array $query
     *
     * @return Response
     */
    public function getTestimonials($query = [])
    {
        return new Response($this->executeQuery('get-testimonials', $query), null, 'testimonial');
    }

    /**
     * Returns a list of featured listings.
     *
     * @param array $query
     *
     * @return Response
     */
    public function getFeaturedListings($query = [])
    {
        return new Response($this->executeQuery('get-featured-listings', $query), 'featured_listings', 'listing');
    }

    /**
     * Return floorplans for a property
     *
     * @param array $query
     *
     * @return Response
     */
    public function getInteractiveFloorplans($query = [])
    {
        return new Response($this->executeQuery('get-interactive-floorplans', $query), 'floorplans', 'floorplan');
    }
}