<?php

namespace Tests\Functional;

class RoutesTest extends BaseTestCase
{



    /**
     * Test that the index route returns a rendered response containing the text 'SlimFramework' but not a greeting
     */
    public function testGetRoutes()
    {

        $get = [
            '/text/es/comerces',
            '/getUserProfiles/18',
            '/adminText/es/comerces/18',
            '/userGallery/1',
            '/getDescription/1',
            '/getContactEmail/1',
            '/getAllPaymentMethods/es',
            '/getUserPaymentMethods/1',
            '/getFiltersAndSubfilters/es',
            '/getUserFilters/1',
            '/getUserProfiles/18',
            '/getAllProducts/es',
            '/getCart/es/18'
        ];
        foreach( $get as $ruta){
            $response = $this->runApp('GET', $ruta);        
            $this->assertEquals(200, $response->getStatusCode());
            $this->assertContains('"error":false', (string)$response->getBody());
            $this->assertNotContains('"error":true', (string)$response->getBody());
        }
        
        $post = [
            '/login',
            '/updateImagePosition',
            '/activateUser',
            '/forgotPass',
            '/setFavoriteImage',
            '/deleteImage',
            '/setDescription',
            '/setContactEmail',
            '/setFilters',
            '/setPaymentMethods',
            '/setProfile',
            '/setCart'
        ];
        foreach( $post as $ruta){
            $response = $this->runApp('POST', $ruta);
            $this->assertEquals(200, $response->getStatusCode());
            $this->assertContains('"error":false', (string)$response->getBody());
            $this->assertNotContains('"error":true', (string)$response->getBody());            
        }
    }
}