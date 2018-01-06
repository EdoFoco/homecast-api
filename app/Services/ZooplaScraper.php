<?php

namespace App\Services;

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use App\User;
use App\Models\Property;
use App\Models\DescriptionSection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ZooplaScraper
{
    protected $baseUrl;
    
    public function __construct()
    {
        $this->baseUrl = "https://www.zoopla.co.uk/to-rent/details";
    }

    private function getBaseProperty(){
        $property = [];
        $property['name'] = '';
        $property['thumbnail'] = '';
        $property['address'] = '';
        $property['postcode'] = '';
        $property['city'] = 'London';
        $property['price'] = 0.0;
        $property['bedrooms'] = 0;
        $property['bathrooms'] = 0;
        $property['living_rooms'] = 0;
        $property['type'] = 'Rent';
        $property['minimum_rental_period'] = 0;
        $property['description_sections'] = [];
        
        return $property;
    }

    public function scrapeProperty(User $user, $propertyId){
        $client = new Client();
        $crawler = $client->request('GET', $this->baseUrl.'/'.$propertyId);
        $notFound = $crawler->filter('#content > h1')->extract('_text');
        if(count($notFound) > 0){
            if(strpos($notFound[0], "be found") !== false){
                throw new NotFoundHttpException('Property not found');
            }
        }
        
        $property = $this->getBaseProperty();
        $name = $crawler->filter('#listing-details > div > h2')->extract('_text');
        if(count($name) > 0){
            $property['name'] = $name[0];
        }

        $node = $crawler->filter("img[title='Image 1']")->extract('src');
        if(count($node) > 0){
            $property['thumbnail'] = str_replace('80/60', '645/430', $node[0]);
            $property['thumbnail'] = str_replace('80_60', '645_430', $property['thumbnail']);
        }
        
        
        $addressNode = $crawler->filter(".listing-details-address > h2[itemprop*='streetAddress']")->extract('_text');
        if(count($addressNode) > 0){
       
            $address = explode(',', $addressNode[0]);
            $postcode = $address[count($address) -1];
            $postcode = explode(' ', $postcode);
            
            $property['address'] = $addressNode[0];

            if($postcode){
                $property['postcode'] = $postcode[count($postcode) -1];
            }
        }
       

        $node = $crawler->filter(".num-beds")->extract('_text');
        if(count($node) > 0){
            $property['bedrooms'] = $node[0];
        }

        $node = $crawler->filter(".num-baths")->extract('_text');
        if(count($node) > 0){
            $property['bathrooms'] = $node[0];
        }     

        $node = $crawler->filter(".num-reception")->extract('_text');
        if(count($node) > 0){
            $property['living_rooms'] = $node[0];
        }      

        $node = $crawler->filter("#tab-details > .clearfix > h3")->extract('_text');
        if(count($node) > 0){
            $descriptionSection = [];
            $descriptionSection['title'] = $node[0];

            $descriptionNode = $crawler->filter("#tab-details > .clearfix > ul > li")->extract('_text');
            $descriptionText = '';
            foreach($descriptionNode as $key=>$dNode){
                if($key != 0 ){
                    $descriptionText = $descriptionText.', '.$dNode;
                }
                else{
                    $descriptionText = $descriptionText.$dNode;
                }  
            }
            $descriptionSection['description'] = preg_replace('/\v(?:[\v\h]+)/', '', $descriptionText);
            array_push($property['description_sections'], $descriptionSection);
        }

        $node = $crawler->filter(".bottom-plus-half > h3")->extract('_text');
        if(count($node) > 0){
            $descriptionSection = [];
            $descriptionSection['title'] = $node[0];

            $descriptionNode = $crawler->filter(".bottom-plus-half > div")->extract('_text');
            if(count($descriptionNode) > 0){
                $descriptionSection['description'] = preg_replace('/\v(?:[\v\h]+)/', '', $descriptionNode[0]);
            }
            array_push($property['description_sections'], $descriptionSection);
        } 
       
        //print_r($property);
        $propertyObj = $user->properties()->save(new Property($property));

        foreach($property['description_sections'] as $section){
            $propertyObj->descriptionSections()->save(new DescriptionSection($section));
        }
    }
}